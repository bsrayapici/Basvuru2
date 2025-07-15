<?php
/**
 * International Competition AJAX işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Ajax_International {
    
    public function __construct() {
        add_action('wp_ajax_trt_submit_international_application', array($this, 'handle_international_submit'));
        add_action('wp_ajax_nopriv_trt_submit_international_application', array($this, 'handle_international_submit'));
    }
    
    /**
     * International form submit işlemi
     */
    public function handle_international_submit() {
        // Nonce kontrolü
        if (!wp_verify_nonce($_POST['nonce'], 'trt_yarisma_nonce')) {
            wp_die('Security check failed.');
        }
        
        // Form verilerini al ve temizle
        $form_data = $this->sanitize_international_data($_POST);
        
        // Zorunlu alanları kontrol et
        $validation_result = $this->validate_international_data($form_data);
        if (!$validation_result['valid']) {
            wp_send_json_error($validation_result['message']);
        }
        
        // Veritabanına kaydet
        $database = new TRT_Yarisma_Database();
        $application_id = $database->save_international_application($form_data);
        
        if ($application_id) {
            // E-posta gönder
            $this->send_international_notification_emails($form_data, $application_id);
            
            wp_send_json_success('Your application has been successfully submitted. Application ID: ' . $application_id);
        } else {
            wp_send_json_error('An error occurred while saving your application.');
        }
    }
    
    /**
     * International form verilerini temizle
     */
    private function sanitize_international_data($data) {
        $sanitized = array();
        
        // Temel film bilgileri
        $text_fields = array(
            'category', 'original_title', 'original_title_english', 'original_language',
            'production_country', 'duration', 'audio_information', 'music_information',
            'aspect_ratio', 'production_date', 'downloadable_password'
        );
        
        foreach ($text_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? sanitize_text_field($data[$field]) : '';
        }
        
        // Textarea alanları
        $textarea_fields = array(
            'short_summary', 'director_biography', 'director_filmography', 'director_address'
        );
        
        foreach ($textarea_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? sanitize_textarea_field($data[$field]) : '';
        }
        
        // URL alanları
        $url_fields = array('downloadable_link', 'imdb_link');
        foreach ($url_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? esc_url_raw($data[$field]) : '';
        }
        
        // Director bilgileri
        $director_fields = array('director_name', 'director_surname', 'director_phone', 'director_email');
        foreach ($director_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? sanitize_text_field($data[$field]) : '';
        }
        
        // E-posta özel kontrolü
        if (!empty($sanitized['director_email'])) {
            $sanitized['director_email'] = sanitize_email($sanitized['director_email']);
        }
        
        // Checkbox alanları
        $sanitized['participation_agreement'] = isset($data['participation_agreement']) ? 1 : 0;
        $sanitized['data_protection'] = isset($data['data_protection']) ? 1 : 0;
        
        // Array alanları (festivals, prizes, social media)
        $array_fields = array(
            'festival_name', 'festival_year', 'prize_name', 'prize_event',
            'social_platform', 'social_url'
        );
        
        foreach ($array_fields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $sanitized[$field] = array_map('sanitize_text_field', $data[$field]);
            } else {
                $sanitized[$field] = array();
            }
        }
        
        return $sanitized;
    }
    
    /**
     * International form verilerini doğrula
     */
    private function validate_international_data($data) {
        $errors = array();
        
        // Zorunlu alanlar
        $required_fields = array(
            'original_title' => 'Original Title',
            'original_language' => 'Original Language',
            'production_country' => 'Production Country',
            'duration' => 'Duration',
            'short_summary' => 'Short Summary',
            'downloadable_link' => 'Downloadable Link',
            'director_name' => 'Director Name',
            'director_surname' => 'Director Surname',
            'director_email' => 'Director E-mail'
        );
        
        foreach ($required_fields as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = $label . ' field is required.';
            }
        }
        
        // E-posta formatı kontrolü
        if (!empty($data['director_email']) && !is_email($data['director_email'])) {
            $errors[] = 'Please enter a valid e-mail address.';
        }
        
        // Duration sayısal kontrol
        if (!empty($data['duration']) && (!is_numeric($data['duration']) || $data['duration'] <= 0)) {
            $errors[] = 'Duration must be a positive number.';
        }
        
        // Summary karakter limiti
        if (!empty($data['short_summary']) && strlen($data['short_summary']) > 1000) {
            $errors[] = 'Short summary cannot exceed 1000 characters.';
        }
        
        // Checkbox kontrolü
        if (empty($data['participation_agreement'])) {
            $errors[] = 'You must accept the Participation Agreement.';
        }
        
        if (empty($data['data_protection'])) {
            $errors[] = 'You must accept the Personal Data Protection Policy.';
        }
        
        // URL formatı kontrolü
        if (!empty($data['downloadable_link']) && !filter_var($data['downloadable_link'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid downloadable link.';
        }
        
        if (!empty($data['imdb_link']) && !filter_var($data['imdb_link'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid IMDB link.';
        }
        
        return array(
            'valid' => empty($errors),
            'message' => empty($errors) ? '' : implode(' ', $errors)
        );
    }
    
    /**
     * International bildirim e-postalarını gönder
     */
    private function send_international_notification_emails($form_data, $application_id) {
        $email_handler = new TRT_Yarisma_Email();
        
        // Başvuru sahibine onay e-postası
        $email_handler->send_international_confirmation_email($form_data['director_email'], $form_data, $application_id);
        
        // Admin'e bildirim e-postası
        $admin_email = get_option('admin_email');
        $email_handler->send_international_admin_notification($admin_email, $form_data, $application_id);
        
        wp_die();
    }
}