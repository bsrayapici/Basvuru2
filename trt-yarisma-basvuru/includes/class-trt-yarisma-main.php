<?php
/**
 * Ana plugin sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Main {
    
    public function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Hook'ları başlat
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    
    /**
     * Bağımlılıkları yükle
     */
    private function load_dependencies() {
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-shortcodes.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-ajax.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-email.php';
        require_once TRT_YARISMA_PATH . 'admin/class-trt-yarisma-admin.php';
        
        // International Competition
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-shortcodes-international.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-ajax-international.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database-international.php';
        
        // Student Competition
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-shortcodes-student.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-ajax-student.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database-student.php';
        
        // Professional Competition
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-shortcodes-professional.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-ajax-professional.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database-professional.php';
    }
    
    /**
     * Plugin başlatma
     */
    public function init() {
        // Veritabanı tablolarını oluştur
        $database = new TRT_Yarisma_Database();
        $database->create_tables();
        
        $international_database = new TRT_Yarisma_Database_International();
        $international_database->create_tables();
        
        $student_database = new TRT_Yarisma_Database_Student();
        $student_database->create_tables();
        
        $professional_database = new TRT_Yarisma_Database_Professional();
        $professional_database->create_tables();
        
        // Shortcode sınıflarını başlat
        new TRT_Yarisma_Shortcodes();
        new TRT_Yarisma_Shortcodes_International();
        new TRT_Yarisma_Shortcodes_Student();
        new TRT_Yarisma_Shortcodes_Professional();
        
        // AJAX sınıflarını başlat
        new TRT_Yarisma_Ajax();
        new TRT_Yarisma_Ajax_International();
        new TRT_Yarisma_Ajax_Student();
        new TRT_Yarisma_Ajax_Professional();
        
        // E-posta sınıfını başlat
        new TRT_Yarisma_Email();
        
        // Admin paneli
        if (is_admin()) {
            new TRT_Yarisma_Admin();
        }
    }
    
    /**
     * Frontend script ve stil dosyalarını yükle
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'trt-yarisma-frontend',
            TRT_YARISMA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            TRT_YARISMA_VERSION
        );
        
        wp_enqueue_script(
            'trt-yarisma-frontend',
            TRT_YARISMA_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            TRT_YARISMA_VERSION,
            true
        );
        
        wp_enqueue_script(
            'trt-yarisma-international',
            TRT_YARISMA_PLUGIN_URL . 'assets/js/international.js',
            array('jquery'),
            TRT_YARISMA_VERSION,
            true
        );
        
        wp_enqueue_script(
            'trt-yarisma-professional',
            TRT_YARISMA_PLUGIN_URL . 'assets/js/professional.js',
            array('jquery'),
            TRT_YARISMA_VERSION,
            true
        );
        
        // AJAX için localize
        wp_localize_script('trt-yarisma-frontend', 'trt_yarisma_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trt_yarisma_nonce')
        ));
        
        wp_localize_script('trt-yarisma-international', 'trt_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trt_yarisma_nonce')
        ));
        
        wp_localize_script('trt-yarisma-student', 'trt_student_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trt_student_nonce'),
            'messages' => array(
                'required_field' => 'Bu alan zorunludur.',
                'invalid_email' => 'Geçerli bir e-posta adresi giriniz.',
                'invalid_phone' => 'Geçerli bir telefon numarası giriniz.',
                'success' => 'Başvurunuz başarıyla gönderildi.',
                'error' => 'Bir hata oluştu. Lütfen tekrar deneyiniz.'
            )
        ));
        
        wp_localize_script('trt-yarisma-professional', 'trt_professional_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trt_professional_nonce'),
            'messages' => array(
                'required_field' => 'Bu alan zorunludur.',
                'invalid_email' => 'Geçerli bir e-posta adresi giriniz.',
                'invalid_phone' => 'Geçerli bir telefon numarası giriniz.',
                'success' => 'Başvurunuz başarıyla gönderildi.',
                'error' => 'Bir hata oluştu. Lütfen tekrar deneyiniz.'
            )
        ));
    }
    
    /**
     * Admin script ve stil dosyalarını yükle
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style(
            'trt-yarisma-admin-style',
            TRT_YARISMA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TRT_YARISMA_VERSION
        );
        
        wp_enqueue_script(
            'trt-yarisma-admin-script',
            TRT_YARISMA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            TRT_YARISMA_VERSION,
            true
        );
    }
    
    /**
     * Plugin aktivasyon
     */
    public static function activate() {
        // Veritabanı tablolarını oluştur
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database-international.php';
        require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-database-student.php';
        
        $database = new TRT_Yarisma_Database();
        $database->create_tables();
        
        $international_database = new TRT_Yarisma_Database_International();
        $international_database->create_tables();
        
        $student_database = new TRT_Yarisma_Database_Student();
        $student_database->create_tables();
        
        // Varsayılan ayarları oluştur
        self::create_default_settings();
    }
    
    /**
     * Plugin deaktivasyon
     */
    public static function deactivate() {
        // Gerekirse temizlik işlemleri
    }
    
    /**
     * Varsayılan ayarları oluştur
     */
    private static function create_default_settings() {
        $default_settings = array(
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_email' => get_option('admin_email'),
            'from_name' => get_option('blogname')
        );
        
        add_option('trt_yarisma_settings', $default_settings);
    }
}

