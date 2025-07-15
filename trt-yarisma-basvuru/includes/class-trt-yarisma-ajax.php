<?php
/**
 * AJAX işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Ajax {
    
    public function __construct() {
        add_action('wp_ajax_trt_yarisma_submit', array($this, 'handle_form_submit'));
        add_action('wp_ajax_nopriv_trt_yarisma_submit', array($this, 'handle_form_submit'));
        
        add_action('wp_enqueue_scripts', array($this, 'localize_scripts'));
    }
    
    /**
     * Script'leri lokalize et
     */
    public function localize_scripts() {
        wp_localize_script('trt-yarisma-frontend', 'trt_yarisma_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trt_yarisma_nonce')
        ));
    }
    
    /**
     * Form submit işlemi
     */
    public function handle_form_submit() {
        // Nonce kontrolü
        if (!wp_verify_nonce($_POST['nonce'], 'trt_yarisma_nonce')) {
            wp_die('Güvenlik kontrolü başarısız.');
        }
        
        // Form verilerini al ve temizle
        $form_data = $this->sanitize_form_data($_POST);
        
        // Zorunlu alanları kontrol et
        $validation_result = $this->validate_form_data($form_data);
        if (!$validation_result['valid']) {
            wp_send_json_error($validation_result['message']);
        }
        
        // Veritabanına kaydet
        $database = new TRT_Yarisma_Database();
        $application_id = $database->save_application($form_data);
        
        if ($application_id) {
            // E-posta gönder
            $this->send_notification_emails($form_data, $application_id);
            
            wp_send_json_success('Başvurunuz başarıyla kaydedildi. Başvuru numaranız: ' . $application_id);
        } else {
            wp_send_json_error('Başvuru kaydedilirken bir hata oluştu.');
        }
    }
    
    /**
     * Form verilerini temizle
     */
    private function sanitize_form_data($data) {
        $sanitized = array();
        
        // Metin alanları
        $text_fields = array(
            'trt_category', 'program_adi', 'program_konusu', 'tahmini_butce', 
            'yapimci_ulke', 'ad', 'soyad', 'telefon', 'email', 'adres'
        );
        
        foreach ($text_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? sanitize_text_field($data[$field]) : '';
        }
        
        // Textarea alanları
        $textarea_fields = array(
            'yararlanilacak_kisiler', 'cekim_yerleri', 'onceki_isler', 
            'ozgecmis', 'projeye_yaklasim'
        );
        
        foreach ($textarea_fields as $field) {
            $sanitized[$field] = isset($data[$field]) ? sanitize_textarea_field($data[$field]) : '';
        }
        
        // URL alanları
        $sanitized['proje_sunum_linki'] = isset($data['proje_sunum_linki']) ? 
            esc_url_raw($data['proje_sunum_linki']) : '';
        
        // Şifre alanı
        $sanitized['indirme_sifresi'] = isset($data['indirme_sifresi']) ? 
            sanitize_text_field($data['indirme_sifresi']) : '';
        
        // Checkbox alanları
        $sanitized['sozlesme_onay'] = isset($data['sozlesme_onay']) ? 1 : 0;
        $sanitized['kvkk_onay'] = isset($data['kvkk_onay']) ? 1 : 0;
        
        // E-posta özel kontrolü
        if (!empty($sanitized['email'])) {
            $sanitized['email'] = sanitize_email($sanitized['email']);
        }
        
        return $sanitized;
    }
    
    /**
     * Form verilerini doğrula
     */
    private function validate_form_data($data) {
        $errors = array();
        
        // Zorunlu alanlar
        $required_fields = array(
            'program_adi' => 'Programın Adı',
            'ad' => 'Ad',
            'soyad' => 'Soyad',
            'email' => 'E-posta Adresi'
        );
        
        foreach ($required_fields as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = $label . ' alanı zorunludur.';
            }
        }
        
        // E-posta formatı kontrolü
        if (!empty($data['email']) && !is_email($data['email'])) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz.';
        }
        
        // Checkbox kontrolü
        if (empty($data['sozlesme_onay'])) {
            $errors[] = 'Katılım sözleşmesini kabul etmelisiniz.';
        }
        
        if (empty($data['kvkk_onay'])) {
            $errors[] = 'KVKK metnini kabul etmelisiniz.';
        }
        
        // URL formatı kontrolü (eğer girilmişse)
        if (!empty($data['proje_sunum_linki']) && !filter_var($data['proje_sunum_linki'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Geçerli bir proje sunum linki giriniz.';
        }
        
        return array(
            'valid' => empty($errors),
            'message' => empty($errors) ? '' : implode(' ', $errors)
        );
    }
    
    /**
     * Bildirim e-postalarını gönder
     */
    private function send_notification_emails($form_data, $application_id) {
        $email_handler = new TRT_Yarisma_Email();
        
        // Başvuru sahibine onay e-postası
        $email_handler->send_confirmation_email($form_data['email'], $form_data, $application_id);
        
        // Admin'e bildirim e-postası
        $admin_email = get_option('admin_email');
        $email_handler->send_admin_notification($admin_email, $form_data, $application_id);
        
        wp_die();
    }
}