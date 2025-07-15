<?php
/**
 * Shortcode işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Shortcodes {
    
    public function __construct() {
        add_shortcode('trt_yarisma_form', array($this, 'render_form'));
    }
    
    /**
     * Ana form shortcode'u
     */
    public function render_form($atts) {
        $atts = shortcode_atts(array(
            'kategori' => ''
        ), $atts);
        
        ob_start();
        
        // CSS ve JS dosyalarını yükle
        wp_enqueue_style('trt-yarisma-frontend');
        wp_enqueue_script('trt-yarisma-frontend');
        
        // Logo URL'sini belirle
        $logo_url = TRT_YARISMA_PLUGIN_URL . 'assets/images/trt-logo.png';
        
        ?>
        <div class="trt-yarisma-app">
            <!-- Başvuruya Başla Ekranı -->
            <div id="trt-start-screen" class="trt-application-container">
                <header class="trt-app-header">
                    <div class="trt-header-content">
                        <img src="<?php echo $logo_url; ?>" alt="TRT Logo" class="trt-logo">
                        <div class="trt-header-text">
                            <h1 id="trt-main-title">TRT Yarışma Başvuru Sistemi</h1>
                            <p id="trt-main-subtitle">Başvuru Sistemi</p>
                        </div>
                    </div>
                </header>

                <div class="trt-form-container">
                    <div class="trt-start-screen">
                        <h2 id="trt-instructions-title">Başvuru Yönergeleri</h2>
                        
                        <!-- Progress Steps Preview -->
                        <div class="trt-progress-preview">
                            <div class="trt-progress-step-preview">
                                <div class="trt-step-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                </div>
                                <div class="trt-step-title" data-tr="Eser Linki ve Bilgileri" data-en="Work Link and Details">Eser Linki ve Bilgileri</div>
                            </div>
                            <div class="trt-arrow">→</div>
                            <div class="trt-progress-step-preview">
                                <div class="trt-step-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                </div>
                                <div class="trt-step-title" data-tr="Eser Sahibi Bilgileri" data-en="Applicant Information">Eser Sahibi Bilgileri</div>
                            </div>
                            <div class="trt-arrow">→</div>
                            <div class="trt-progress-step-preview">
                                <div class="trt-step-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,12V14H16V12H8M8,16V18H13V16H8Z"/>
                                    </svg>
                                </div>
                                <div class="trt-step-title" data-tr="Katılım Sözleşmesi" data-en="Participation Agreement">Katılım Sözleşmesi</div>
                            </div>
                            <div class="trt-arrow">→</div>
                            <div class="trt-progress-step-preview">
                                <div class="trt-step-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                                    </svg>
                                </div>
                                <div class="trt-step-title" data-tr="Başvuru Özeti ve Onay" data-en="Application Summary and Confirmation">Başvuru Özeti ve Onay</div>
                            </div>
                        </div>

                        <!-- Instructions Box -->
                        <div class="trt-instructions-box">
                            <h3 id="trt-key-info-title">Başvuru Sürecinde Bilinmesi Gerekenler</h3>
                            <p id="trt-instruction-1"><strong>1.</strong> Aynı eserin tamamı veya parçaları ile birden fazla kategoride başvuru yapılamaz.</p>
                            <p id="trt-instruction-2"><strong>2.</strong> Yarışma süreci boyunca belirttiğiniz e-posta adresi üzerinden sizi bilgilendireceğiz. Bu nedenle sisteme giriş yaptığınız e-posta adresini (spam, istenmeyen posta, junk vb. olarak adlandırılan klasörler dahil) aralıklarla kontrol ediniz.</p>
                        </div>

                        <!-- Category Selection -->
                        <div class="trt-category-selection">
                            <h3 id="trt-category-title">Başvuru yapmak istediğiniz kategoriyi seçiniz.</h3>
                            
                            <div class="trt-category-options">
                                <label class="trt-category-option">
                                    <input type="radio" name="trt_category" value="professional">
                                    <span class="trt-radio-custom"></span>
                                    <span class="trt-category-text">Ulusal Profesyonel Kategori</span>
                                </label>

                                <label class="trt-category-option">
                                    <input type="radio" name="trt_category" value="student">
                                    <span class="trt-radio-custom"></span>
                                    <span class="trt-category-text">Ulusal Öğrenci Kategorisi</span>
                                </label>

                                <label class="trt-category-option">
                                    <input type="radio" name="trt_category" value="international">
                                    <span class="trt-radio-custom"></span>
                                    <span class="trt-category-text">Uluslararası Profesyonel Kategori</span>
                                </label>

                                <label class="trt-category-option">
                                    <input type="radio" name="trt_category" value="project-support">
                                    <span class="trt-radio-custom"></span>
                                    <span class="trt-category-text">Proje Destek Kategorisi</span>
                                </label>
                            </div>
                        </div>

                        <!-- Deadline Notice -->
                        <div class="trt-deadline-notice">
                            <span class="trt-deadline-text" id="trt-deadline-text">
                                Başvuruların bitmesine son <strong>23 gün!</strong>
                            </span>
                            <button type="button" class="trt-start-application-btn" id="trt-start-btn">
                                Başvuruya Başla
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Ekranları -->
            <div id="trt-form-screen" class="trt-application-container" style="display: none;">
                <header class="trt-app-header">
                    <div class="trt-header-content">
                        <img src="<?php echo $logo_url; ?>" alt="TRT Logo" class="trt-logo">
                        <div class="trt-header-text">
                            <h1 id="trt-form-title">TRT Yarışma Başvuru Sistemi</h1>
                            <p id="trt-form-subtitle">Başvuru Sistemi</p>
                        </div>
                    </div>
                </header>

                <div class="trt-form-container">
                    <button class="trt-back-button" id="trt-back-btn">
                        Başlangıç Ekranına Dön
                    </button>
                    
                    <!-- Progress Bar -->
                    <div class="trt-progress-container">
                        <div class="trt-progress-bar">
                            <div class="trt-progress-step active" data-step="0">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">✓</span>
                                </div>
                                <div class="trt-step-label">Başvuruya Başla</div>
                            </div>
                            <div class="trt-progress-step" data-step="1">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">1</span>
                                </div>
                                <div class="trt-step-label">Eser Linki ve Bilgileri</div>
                            </div>
                            <div class="trt-progress-step" data-step="2">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">2</span>
                                </div>
                                <div class="trt-step-label">Eser Sahibi Bilgileri</div>
                            </div>
                            <div class="trt-progress-step" data-step="3">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">3</span>
                                </div>
                                <div class="trt-step-label">Katılım Sözleşmesi</div>
                            </div>
                            <div class="trt-progress-step" data-step="4">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">4</span>
                                </div>
                                <div class="trt-step-label">Başvuru Özeti ve Onayı</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div id="trt-form-content" class="trt-form-content">
                        <!-- Form içeriği JavaScript ile yüklenecek -->
                    </div>

                    <!-- Form Navigation -->
                    <div class="trt-form-navigation">
                        <button type="button" class="trt-btn-secondary" id="trt-prev-btn" style="display: none;">
                            Geri Dön
                        </button>
                        <button type="button" class="trt-btn-primary" id="trt-next-btn">
                            İleri
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
}

