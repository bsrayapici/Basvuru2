<?php
/**
 * Profesyonel Yarışması AJAX işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Ajax_Professional {
    
    public function __construct() {
        add_action('wp_ajax_trt_professional_submit', array($this, 'handle_professional_submission'));
        add_action('wp_ajax_nopriv_trt_professional_submit', array($this, 'handle_professional_submission'));
    }
    
    public function handle_professional_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['professional_nonce'], 'trt_professional_nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => 'Güvenlik doğrulaması başarısız.'
            )));
        }
        
        // Sanitize and validate data
        $data = $this->sanitize_form_data($_POST);
        $validation = $this->validate_form_data($data);
        
        if (!$validation['valid']) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => $validation['message']
            )));
        }
        
        // Save to database
        $database = new TRT_Yarisma_Database_Professional();
        $submission_id = $database->save_submission($data);
        
        if (!$submission_id) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => 'Başvuru kaydedilirken bir hata oluştu.'
            )));
        }
        
        // Send confirmation email
        $this->send_confirmation_email($data, $submission_id);
        
        // Send notification to admin
        $this->send_admin_notification($data, $submission_id);
        
        wp_die(json_encode(array(
            'success' => true,
            'data' => 'Başvurunuz başarıyla gönderildi. Başvuru numaranız: ' . $submission_id
        )));
    }
    
    private function sanitize_form_data($post_data) {
        $data = array();
        
        // Basic fields
        $data['category'] = sanitize_text_field($post_data['category']);
        $data['original_title'] = sanitize_text_field($post_data['original_title']);
        $data['turkish_title'] = sanitize_text_field($post_data['turkish_title']);
        $data['original_language'] = sanitize_text_field($post_data['original_language']);
        $data['production_country'] = sanitize_text_field($post_data['production_country']);
        $data['duration'] = intval($post_data['duration']);
        $data['audio_info'] = sanitize_text_field($post_data['audio_info']);
        $data['music_info'] = sanitize_text_field($post_data['music_info']);
        $data['aspect_ratio'] = sanitize_text_field($post_data['aspect_ratio']);
        $data['production_date'] = sanitize_text_field($post_data['production_date']);
        $data['summary'] = sanitize_textarea_field($post_data['summary']);
        $data['download_link'] = esc_url_raw($post_data['download_link']);
        $data['download_password'] = sanitize_text_field($post_data['download_password']);
        $data['imdb_link'] = esc_url_raw($post_data['imdb_link']);
        
        // Applicant info
        $data['applicant_name'] = sanitize_text_field($post_data['applicant_name']);
        $data['applicant_surname'] = sanitize_text_field($post_data['applicant_surname']);
        $data['phone'] = sanitize_text_field($post_data['phone']);
        $data['email'] = sanitize_email($post_data['email']);
        $data['address'] = sanitize_textarea_field($post_data['address']);
        $data['biography'] = sanitize_textarea_field($post_data['biography']);
        $data['filmography'] = sanitize_textarea_field($post_data['filmography']);
        
        // Agreement
        $data['agreement_accept'] = isset($post_data['agreement_accept']) ? 1 : 0;
        $data['privacy_accept'] = isset($post_data['privacy_accept']) ? 1 : 0;
        
        // Dynamic fields
        $data['festivals'] = isset($post_data['festivals']) ? array_map('sanitize_text_field', $post_data['festivals']) : array();
        $data['awards'] = isset($post_data['awards']) ? array_map('sanitize_text_field', $post_data['awards']) : array();
        $data['social_media'] = isset($post_data['social_media']) ? array_map('esc_url_raw', $post_data['social_media']) : array();
        
        // Person fields (directors, producers, etc.)
        $person_fields = array('directors', 'producers', 'writers', 'sponsors', 'sales_agent', 'crew');
        foreach ($person_fields as $field) {
            if (isset($post_data[$field . '_name']) && is_array($post_data[$field . '_name'])) {
                $data[$field] = array();
                for ($i = 0; $i < count($post_data[$field . '_name']); $i++) {
                    if (!empty($post_data[$field . '_name'][$i])) {
                        $data[$field][] = array(
                            'name' => sanitize_text_field($post_data[$field . '_name'][$i]),
                            'surname' => sanitize_text_field($post_data[$field . '_surname'][$i]),
                            'phone' => sanitize_text_field($post_data[$field . '_phone'][$i]),
                            'email' => sanitize_email($post_data[$field . '_email'][$i]),
                            'address' => sanitize_textarea_field($post_data[$field . '_address'][$i])
                        );
                    }
                }
            }
        }
        
        return $data;
    }
    
    private function validate_form_data($data) {
        $errors = array();
        
        // Required fields validation
        if (empty($data['original_title'])) {
            $errors[] = 'Filmin özgün adı zorunludur.';
        }
        
        if (empty($data['original_language'])) {
            $errors[] = 'Özgün dil seçimi zorunludur.';
        }
        
        if (empty($data['production_country'])) {
            $errors[] = 'Yapımcı ülke seçimi zorunludur.';
        }
        
        if (empty($data['duration']) || $data['duration'] < 1) {
            $errors[] = 'Geçerli bir süre giriniz.';
        }
        
        if (empty($data['summary']) || strlen($data['summary']) < 250 || strlen($data['summary']) > 1000) {
            $errors[] = 'Film özeti maksimum 2500 karakter olmalıdır.';
        }
        
        if (empty($data['download_link']) || !filter_var($data['download_link'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Geçerli bir film linki giriniz.';
        }
        
        if (empty($data['applicant_name'])) {
            $errors[] = 'Ad alanı zorunludur.';
        }
        
        if (empty($data['applicant_surname'])) {
            $errors[] = 'Soyad alanı zorunludur.';
        }
        
        if (empty($data['phone'])) {
            $errors[] = 'Telefon numarası zorunludur.';
        }
        
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz.';
        }
        
        if (empty($data['address'])) {
            $errors[] = 'Adres alanı zorunludur.';
        }
        
        // Check if at least one director is provided
        if (empty($data['directors']) || count($data['directors']) === 0) {
            $errors[] = 'En az bir yönetmen bilgisi eklemelisiniz.';
        }
        
        // Agreement validation
        if (!$data['agreement_accept']) {
            $errors[] = 'Katılım sözleşmesini kabul etmelisiniz.';
        }
        
        if (!$data['privacy_accept']) {
            $errors[] = 'Kişisel verilerin korunması metnini kabul etmelisiniz.';
        }
        
        return array(
            'valid' => empty($errors),
            'message' => empty($errors) ? '' : implode(' ', $errors)
        );
    }
    
    private function send_confirmation_email($data, $submission_id) {
        $to = $data['email'];
        $subject = 'TRT Profesyonel Yarışması Başvuru Onayı';
        
        $message = "Sayın {$data['applicant_name']} {$data['applicant_surname']},\n\n";
        $message .= "TRT Profesyonel | Ulusal Belgesel Ödülleri Yarışması başvurunuz başarıyla alınmıştır.\n\n";
        $message .= "Başvuru Numaranız: {$submission_id}\n";
        $message .= "Film Adı: {$data['original_title']}\n";
        $message .= "Başvuru Tarihi: " . date('d.m.Y H:i') . "\n\n";
        $message .= "Başvuru süreciyle ilgili güncellemeler bu e-posta adresine gönderilecektir.\n\n";
        $message .= "Saygılarımızla,\nTRT Yarışma Ekibi";
        
        wp_mail($to, $subject, $message);
    }
    
    private function send_admin_notification($data, $submission_id) {
        $admin_email = get_option('admin_email');
        $subject = 'Yeni Profesyonel Yarışması Başvurusu';
        
        $message = "Yeni bir profesyonel yarışması başvurusu alındı.\n\n";
        $message .= "Başvuru ID: {$submission_id}\n";
        $message .= "Başvuran: {$data['applicant_name']} {$data['applicant_surname']}\n";
        $message .= "E-posta: {$data['email']}\n";
        $message .= "Film Adı: {$data['original_title']}\n";
        $message .= "Başvuru Tarihi: " . date('d.m.Y H:i') . "\n\n";
        $message .= "Admin panelinden detayları görüntüleyebilirsiniz.";
        
        wp_mail($admin_email, $subject, $message);
        
        wp_die();
    }
}