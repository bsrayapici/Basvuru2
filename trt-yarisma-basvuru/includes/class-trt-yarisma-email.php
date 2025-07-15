<?php
/**
 * E-posta işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Email {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('trt_yarisma_settings', array());
        add_action('wp_ajax_trt_yarisma_test_email', array($this, 'test_email'));
        add_action('wp_ajax_trt_yarisma_update_status', array($this, 'update_application_status'));
    }
    
    /**
     * Başvuru onay e-postası gönder
     */
    public function send_confirmation_email($data, $application_id) {
        $to = $data['email'];
        $subject = 'TRT Yarışma Başvurunuz Alındı - Başvuru No: ' . $application_id;
        
        $message = $this->get_confirmation_email_template($data, $application_id);
        
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * Durum değişikliği e-postası gönder
     */
    public function send_status_update_email($application_id, $new_status) {
        $application = TRT_Yarisma_Database::get_application($application_id);
        
        if (!$application) {
            return false;
        }
        
        $to = $application->email;
        $subject = $this->get_status_email_subject($new_status, $application_id);
        $message = $this->get_status_email_template($application, $new_status);
        
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * E-posta gönder
     */
    private function send_email($to, $subject, $message) {
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        if (!empty($this->settings['from_email'])) {
            $headers[] = 'From: ' . $this->settings['from_name'] . ' <' . $this->settings['from_email'] . '>';
        }
        
        // SMTP ayarları varsa kullan
        if (!empty($this->settings['smtp_host'])) {
            $this->configure_smtp();
        }
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * SMTP ayarlarını yapılandır
     */
    private function configure_smtp() {
        add_action('phpmailer_init', array($this, 'setup_phpmailer'));
    }
    
    /**
     * PHPMailer'ı yapılandır
     */
    public function setup_phpmailer($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $this->settings['smtp_host'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $this->settings['smtp_port'];
        $phpmailer->Username = $this->settings['smtp_username'];
        $phpmailer->Password = $this->settings['smtp_password'];
        $phpmailer->SMTPSecure = $this->settings['smtp_encryption'];
        $phpmailer->From = $this->settings['from_email'];
        $phpmailer->FromName = $this->settings['from_name'];
        
        // Debug için (geliştirme ortamında)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $phpmailer->SMTPDebug = 2;
        }
    }
    
    /**
     * Onay e-postası şablonu
     */
    private function get_confirmation_email_template($data, $application_id) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>TRT Yarışma Başvuru Onayı</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; background: #ff6b35; color: white; padding: 20px; border-radius: 8px; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .success-box { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .info-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; }
                .info-table th, .info-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .info-table th { background: #f8f9fa; font-weight: bold; width: 40%; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
                .important-info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; }
                .important-info h4 { margin-top: 0; color: #1976d2; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>TRT Yarışma Başvuru Sistemi</h1>
                </div>
                
                <div class="success-box">
                    <h2 style="margin-top: 0; color: #155724;">✓ Başvurunuz Başarıyla Alındı!</h2>
                    <p>Sayın <strong><?php echo esc_html($data['ad'] . ' ' . $data['soyad']); ?></strong>,</p>
                    <p>TRT Yarışma başvurunuz başarıyla alınmıştır.</p>
                </div>
                
                <div class="content">
                    <table class="info-table">
                        <tr>
                            <th>Başvuru Numaranız</th>
                            <td><strong><?php echo $application_id; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Başvuru Tarihi</th>
                            <td><?php echo date('d.m.Y H:i'); ?></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td><?php echo esc_html($data['kategori']); ?></td>
                        </tr>
                        <tr>
                            <th>Program Adı</th>
                            <td><?php echo esc_html($data['program_adi']); ?></td>
                        </tr>
                        <?php if (!empty($data['program_konusu'])): ?>
                        <tr>
                            <th>Program Konusu</th>
                            <td><?php echo esc_html($data['program_konusu']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>E-posta</th>
                            <td><?php echo esc_html($data['email']); ?></td>
                        </tr>
                        <?php if (!empty($data['telefon'])): ?>
                        <tr>
                            <th>Telefon</th>
                            <td><?php echo esc_html($data['telefon']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <div class="important-info">
                    <h4>📋 Önemli Bilgiler</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li>Başvurunuz değerlendirme sürecine alınmıştır.</li>
                        <li>Değerlendirme süreci tamamlandığında e-posta ile bilgilendirileceksiniz.</li>
                        <li>Başvuru numaranızı not alınız ve iletişimde belirtiniz.</li>
                        <li>Sorularınız için <strong>geleceginiletisilmcileri@trt.net.tr</strong> adresine yazabilirsiniz.</li>
                    </ul>
                </div>
                
                <div class="footer">
                    <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.</p>
                    <p>&copy; <?php echo date('Y'); ?> TRT - Türkiye Radyo Televizyon Kurumu</p>
                    <p>Bu e-postayı almak istemiyorsanız, lütfen bizimle iletişime geçiniz.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Durum e-postası konusu
     */
    private function get_status_email_subject($status, $application_id) {
        $subjects = array(
            'inceleniyor' => 'Başvurunuz İnceleme Aşamasında - Başvuru No: ' . $application_id,
            'onaylandi' => 'Başvurunuz Onaylandı - Başvuru No: ' . $application_id,
            'reddedildi' => 'Başvuru Sonucu - Başvuru No: ' . $application_id
        );
        
        return isset($subjects[$status]) ? $subjects[$status] : 'Başvuru Durumu Güncellendi - Başvuru No: ' . $application_id;
    }
    
    /**
     * Durum e-postası şablonu
     */
    private function get_status_email_template($application, $status) {
        $status_messages = array(
            'inceleniyor' => array(
                'title' => '🔍 Başvurunuz İnceleme Aşamasında',
                'message' => 'Başvurunuz uzman ekibimiz tarafından incelenmektedir. Sonuç hakkında en kısa sürede bilgilendirileceksiniz.',
                'color' => '#ffc107'
            ),
            'onaylandi' => array(
                'title' => '🎉 Tebrikler! Başvurunuz Onaylandı',
                'message' => 'Başvurunuz başarıyla onaylanmıştır. Detaylı bilgi için sizinle iletişime geçilecektir.',
                'color' => '#28a745'
            ),
            'reddedildi' => array(
                'title' => '📋 Başvuru Sonucu',
                'message' => 'Başvurunuz değerlendirme sürecini tamamlamıştır. Maalesef bu sefer başvurunuz kabul edilememiştir.',
                'color' => '#dc3545'
            )
        );
        
        $status_info = isset($status_messages[$status]) ? $status_messages[$status] : $status_messages['inceleniyor'];
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>TRT Yarışma Başvuru Durumu</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; background: #ff6b35; color: white; padding: 20px; border-radius: 8px; }
                .header h1 { margin: 0; font-size: 24px; }
                .status-box { background: <?php echo $status_info['color']; ?>; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
                .status-box h2 { margin-top: 0; font-size: 20px; }
                .content { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .info-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; }
                .info-table th, .info-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .info-table th { background: #f8f9fa; font-weight: bold; width: 40%; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
                .contact-info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>TRT Yarışma Başvuru Sistemi</h1>
                </div>
                
                <div class="status-box">
                    <h2><?php echo $status_info['title']; ?></h2>
                    <p><?php echo $status_info['message']; ?></p>
                </div>
                
                <div class="content">
                    <p>Sayın <strong><?php echo esc_html($application->ad . ' ' . $application->soyad); ?></strong>,</p>
                    
                    <table class="info-table">
                        <tr>
                            <th>Başvuru Numarası</th>
                            <td><strong><?php echo $application->id; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Program Adı</th>
                            <td><?php echo esc_html($application->program_adi); ?></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td><?php echo esc_html($application->kategori); ?></td>
                        </tr>
                        <tr>
                            <th>Başvuru Tarihi</th>
                            <td><?php echo date('d.m.Y H:i', strtotime($application->basvuru_tarihi)); ?></td>
                        </tr>
                        <tr>
                            <th>Güncel Durum</th>
                            <td><strong style="color: <?php echo $status_info['color']; ?>;"><?php echo ucfirst($status); ?></strong></td>
                        </tr>
                    </table>
                </div>
                
                <div class="contact-info">
                    <h4 style="margin-top: 0; color: #1976d2;">📞 İletişim</h4>
                    <p>Sorularınız için <strong>geleceginiletisilmcileri@trt.net.tr</strong> adresine yazabilirsiniz.</p>
                    <p>İletişimde başvuru numaranızı (<strong><?php echo $application->id; ?></strong>) belirtmeyi unutmayınız.</p>
                </div>
                
                <div class="footer">
                    <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.</p>
                    <p>&copy; <?php echo date('Y'); ?> TRT - Türkiye Radyo Televizyon Kurumu</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * International Competition onay e-postası gönder
     */
    public function send_international_confirmation_email($to, $data, $application_id) {
        $subject = 'TRT International Competition Application Received - Application No: ' . $application_id;
        $message = $this->get_international_confirmation_template($data, $application_id);
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * Student Competition onay e-postası gönder
     */
    public function send_student_confirmation_email($to, $data, $application_id) {
        $subject = 'TRT Öğrenci Yarışması Başvurunuz Alındı - Başvuru No: ' . $application_id;
        $message = $this->get_student_confirmation_template($data, $application_id);
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * Professional Competition onay e-postası gönder
     */
    public function send_professional_confirmation_email($to, $data, $application_id) {
        $subject = 'TRT Profesyonel Yarışması Başvurunuz Alındı - Başvuru No: ' . $application_id;
        $message = $this->get_professional_confirmation_template($data, $application_id);
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * Admin bildirim e-postaları
     */
    public function send_international_admin_notification($to, $data, $application_id) {
        $subject = 'New International Competition Application - ID: ' . $application_id;
        $message = $this->get_admin_notification_template($data, $application_id, 'International Competition');
        return $this->send_email($to, $subject, $message);
    }
    
    public function send_student_admin_notification($to, $data, $application_id) {
        $subject = 'Yeni Öğrenci Yarışması Başvurusu - ID: ' . $application_id;
        $message = $this->get_admin_notification_template($data, $application_id, 'Öğrenci Yarışması');
        return $this->send_email($to, $subject, $message);
    }
    
    public function send_professional_admin_notification($to, $data, $application_id) {
        $subject = 'Yeni Profesyonel Yarışması Başvurusu - ID: ' . $application_id;
        $message = $this->get_admin_notification_template($data, $application_id, 'Profesyonel Yarışması');
        return $this->send_email($to, $subject, $message);
    }
    
    /**
     * International Competition e-posta şablonu
     */
    private function get_international_confirmation_template($data, $application_id) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>TRT International Competition Application Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; background: #ff6b35; color: white; padding: 20px; border-radius: 8px; }
                .success-box { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .info-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; }
                .info-table th, .info-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .info-table th { background: #f8f9fa; font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>TRT International Documentary Competition</h1>
                </div>
                
                <div class="success-box">
                    <h2 style="margin-top: 0;">✓ Application Successfully Received!</h2>
                    <p>Dear <strong><?php echo esc_html($data['director_name'] . ' ' . $data['director_surname']); ?></strong>,</p>
                    <p>Your application for TRT International Documentary Competition has been successfully received.</p>
                </div>
                
                <table class="info-table">
                    <tr><th>Application Number</th><td><strong><?php echo $application_id; ?></strong></td></tr>
                    <tr><th>Application Date</th><td><?php echo date('d.m.Y H:i'); ?></td></tr>
                    <tr><th>Film Title</th><td><?php echo esc_html($data['original_title']); ?></td></tr>
                    <tr><th>Director</th><td><?php echo esc_html($data['director_name'] . ' ' . $data['director_surname']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo esc_html($data['director_email']); ?></td></tr>
                </table>
                
                <div style="background: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0;">📋 Important Information</h4>
                    <ul>
                        <li>Your application is under review.</li>
                        <li>You will be notified via email when the evaluation is complete.</li>
                        <li>Please keep your application number for reference.</li>
                        <li>For questions: <strong>geleceginiletisilmcileri@trt.net.tr</strong></li>
                    </ul>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Student Competition e-posta şablonu
     */
    private function get_student_confirmation_template($data, $application_id) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>TRT Öğrenci Yarışması Başvuru Onayı</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; background: #ff6b35; color: white; padding: 20px; border-radius: 8px; }
                .success-box { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .info-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; }
                .info-table th, .info-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .info-table th { background: #f8f9fa; font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>TRT Öğrenci Belgesel Yarışması</h1>
                </div>
                
                <div class="success-box">
                    <h2 style="margin-top: 0;">✓ Başvurunuz Başarıyla Alındı!</h2>
                    <p>Sayın <strong><?php echo esc_html($data['applicant_name'] . ' ' . $data['applicant_surname']); ?></strong>,</p>
                    <p>TRT Öğrenci | Ulusal Belgesel Ödülleri Yarışması başvurunuz başarıyla alınmıştır.</p>
                </div>
                
                <table class="info-table">
                    <tr><th>Başvuru Numaranız</th><td><strong><?php echo $application_id; ?></strong></td></tr>
                    <tr><th>Başvuru Tarihi</th><td><?php echo date('d.m.Y H:i'); ?></td></tr>
                    <tr><th>Film Adı</th><td><?php echo esc_html($data['original_title']); ?></td></tr>
                    <tr><th>Başvuran</th><td><?php echo esc_html($data['applicant_name'] . ' ' . $data['applicant_surname']); ?></td></tr>
                    <tr><th>E-posta</th><td><?php echo esc_html($data['email']); ?></td></tr>
                </table>
                
                <div style="background: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0;">📋 Önemli Bilgiler</h4>
                    <ul>
                        <li>Başvurunuz değerlendirme sürecine alınmıştır.</li>
                        <li>Değerlendirme tamamlandığında e-posta ile bilgilendirileceksiniz.</li>
                        <li>Başvuru numaranızı not alınız.</li>
                        <li>Sorularınız için: <strong>geleceginiletisilmcileri@trt.net.tr</strong></li>
                    </ul>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Professional Competition e-posta şablonu
     */
    private function get_professional_confirmation_template($data, $application_id) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>TRT Profesyonel Yarışması Başvuru Onayı</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; background: #ff6b35; color: white; padding: 20px; border-radius: 8px; }
                .success-box { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .info-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; }
                .info-table th, .info-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .info-table th { background: #f8f9fa; font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>TRT Profesyonel Belgesel Yarışması</h1>
                </div>
                
                <div class="success-box">
                    <h2 style="margin-top: 0;">✓ Başvurunuz Başarıyla Alındı!</h2>
                    <p>Sayın <strong><?php echo esc_html($data['applicant_name'] . ' ' . $data['applicant_surname']); ?></strong>,</p>
                    <p>TRT Profesyonel | Ulusal Belgesel Ödülleri Yarışması başvurunuz başarıyla alınmıştır.</p>
                </div>
                
                <table class="info-table">
                    <tr><th>Başvuru Numaranız</th><td><strong><?php echo $application_id; ?></strong></td></tr>
                    <tr><th>Başvuru Tarihi</th><td><?php echo date('d.m.Y H:i'); ?></td></tr>
                    <tr><th>Film Adı</th><td><?php echo esc_html($data['original_title']); ?></td></tr>
                    <tr><th>Başvuran</th><td><?php echo esc_html($data['applicant_name'] . ' ' . $data['applicant_surname']); ?></td></tr>
                    <tr><th>E-posta</th><td><?php echo esc_html($data['email']); ?></td></tr>
                </table>
                
                <div style="background: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 4px;">
                    <h4 style="margin-top: 0;">📋 Önemli Bilgiler</h4>
                    <ul>
                        <li>Başvurunuz değerlendirme sürecine alınmıştır.</li>
                        <li>Değerlendirme tamamlandığında e-posta ile bilgilendirileceksiniz.</li>
                        <li>Başvuru numaranızı not alınız.</li>
                        <li>Sorularınız için: <strong>geleceginiletisilmcileri@trt.net.tr</strong></li>
                    </ul>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Admin bildirim şablonu
     */
    private function get_admin_notification_template($data, $application_id, $category) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Yeni Başvuru Bildirimi</title>
        </head>
        <body>
            <h2>Yeni <?php echo $category; ?> Başvurusu</h2>
            <p><strong>Başvuru ID:</strong> <?php echo $application_id; ?></p>
            <p><strong>Kategori:</strong> <?php echo $category; ?></p>
            <p><strong>Başvuru Tarihi:</strong> <?php echo date('d.m.Y H:i'); ?></p>
            
            <?php if (isset($data['original_title'])): ?>
            <p><strong>Film Adı:</strong> <?php echo esc_html($data['original_title']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($data['director_name'])): ?>
            <p><strong>Yönetmen:</strong> <?php echo esc_html($data['director_name'] . ' ' . $data['director_surname']); ?></p>
            <p><strong>E-posta:</strong> <?php echo esc_html($data['director_email']); ?></p>
            <?php elseif (isset($data['applicant_name'])): ?>
            <p><strong>Başvuran:</strong> <?php echo esc_html($data['applicant_name'] . ' ' . $data['applicant_surname']); ?></p>
            <p><strong>E-posta:</strong> <?php echo esc_html($data['email']); ?></p>
            <?php endif; ?>
            
            <p>Admin panelinden detayları görüntüleyebilirsiniz.</p>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Test e-postası gönder (AJAX)
     */
    public function test_email() {
        if (!current_user_can('manage_options')) {
            wp_die('Yetkiniz yok.');
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'trt_yarisma_admin_nonce')) {
            wp_die('Güvenlik kontrolü başarısız.');
        }
        
        $test_email = sanitize_email($_POST['test_email']);
        
        if (!is_email($test_email)) {
            wp_send_json_error(array('message' => 'Geçerli bir e-posta adresi giriniz.'));
        }
        
        $subject = 'TRT Yarışma - Test E-postası';
        $message = '<h2>Test E-postası</h2><p>Bu bir test e-postasıdır. SMTP ayarlarınız doğru çalışıyor.</p><p>Gönderim zamanı: ' . date('d.m.Y H:i:s') . '</p>';
        
        $result = $this->send_email($test_email, $subject, $message);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Test e-postası başarıyla gönderildi.'));
        } else {
            wp_send_json_error(array('message' => 'Test e-postası gönderilemedi. SMTP ayarlarını kontrol ediniz.'));
        }
    }
    
    /**
     * Başvuru durumu güncelle (AJAX)
     */
    public function update_application_status() {
        if (!current_user_can('manage_options')) {
            wp_die('Yetkiniz yok.');
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'trt_yarisma_admin_nonce')) {
            wp_die('Güvenlik kontrolü başarısız.');
        }
        
        $id = intval($_POST['id']);
        $status = sanitize_text_field($_POST['status']);
        
        $result = TRT_Yarisma_Database::update_application_status($id, $status);
        
        if ($result !== false) {
            // Durum değişikliği e-postası gönder
            $this->send_status_update_email($id, $status);
            
            wp_send_json_success(array('message' => 'Durum başarıyla güncellendi.'));
        } else {
            wp_send_json_error(array('message' => 'Durum güncellenirken hata oluştu.'));
        }
    }
}

