<?php
/**
 * Öğrenci Yarışması AJAX işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Ajax_Student {
    
    public function __construct() {
        add_action('wp_ajax_trt_student_submit', array($this, 'handle_student_submission'));
        add_action('wp_ajax_nopriv_trt_student_submit', array($this, 'handle_student_submission'));
    }
    
    public function handle_student_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'trt_student_nonce')) {
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
        
        // Handle file upload
        $file_upload_result = $this->handle_file_upload();
        if (!$file_upload_result['success']) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => $file_upload_result['message']
            )));
        }
        
        $data['student_document_path'] = $file_upload_result['file_path'];
        
        // Save to database
        $database = new TRT_Yarisma_Database_Student();
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
    
    private function handle_file_upload() {
        if (!isset($_FILES['student_document']) || $_FILES['student_document']['error'] !== UPLOAD_ERR_OK) {
            return array(
                'success' => false,
                'message' => 'Öğrenci belgesi yüklenmesi zorunludur.'
            );
        }
        
        $file = $_FILES['student_document'];
        $allowed_types = array('application/pdf', 'image/jpeg', 'image/jpg', 'image/png');
        
        if (!in_array($file['type'], $allowed_types)) {
            return array(
                'success' => false,
                'message' => 'Sadece PDF, JPG ve PNG dosyaları kabul edilir.'
            );
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return array(
                'success' => false,
                'message' => 'Dosya boyutu 5MB\'dan küçük olmalıdır.'
            );
        }
        
        $upload_dir = wp_upload_dir();
        $trt_upload_dir = $upload_dir['basedir'] . '/trt-yarisma-student/';
        
        if (!file_exists($trt_upload_dir)) {
            wp_mkdir_p($trt_upload_dir);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'student_doc_' . time() . '_' . wp_generate_password(8, false) . '.' . $file_extension;
        $file_path = $trt_upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return array(
                'success' => true,
                'file_path' => $upload_dir['baseurl'] . '/trt-yarisma-student/' . $new_filename
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Dosya yüklenirken bir hata oluştu.'
            );
        }
    }
    
    private function send_confirmation_email($data, $submission_id) {
        $to = $data['email'];
        $subject = 'TRT Öğrenci Yarışması Başvuru Onayı';
        
        $message = "Sayın {$data['applicant_name']} {$data['applicant_surname']},\n\n";
        $message .= "TRT Öğrenci | Ulusal Belgesel Ödülleri Yarışması başvurunuz başarıyla alınmıştır.\n\n";
        $message .= "Başvuru Numaranız: {$submission_id}\n";
        $message .= "Film Adı: {$data['original_title']}\n";
        $message .= "Başvuru Tarihi: " . date('d.m.Y H:i') . "\n\n";
        $message .= "Başvuru süreciyle ilgili güncellemeler bu e-posta adresine gönderilecektir.\n\n";
        $message .= "Saygılarımızla,\nTRT Yarışma Ekibi";
        
        wp_mail($to, $subject, $message);
    }
    
    private function send_admin_notification($data, $submission_id) {
        $admin_email = get_option('admin_email');
        $subject = 'Yeni Öğrenci Yarışması Başvurusu';
        
        $message = "Yeni bir öğrenci yarışması başvurusu alındı.\n\n";
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