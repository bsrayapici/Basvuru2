<?php
/**
 * Öğrenci | Ulusal Belgesel Ödülleri Yarışması Shortcode sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Shortcodes_Student {
    
    public function __construct() {
        add_shortcode('trt_student_form', array($this, 'render_student_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('trt-yarisma-frontend', TRT_YARISMA_PLUGIN_URL . 'assets/css/frontend.css', array(), TRT_YARISMA_VERSION);
        wp_enqueue_script('trt-yarisma-student', TRT_YARISMA_PLUGIN_URL . 'assets/js/student.js', array('jquery'), TRT_YARISMA_VERSION, true);
        
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
    }
    
    public function render_student_form($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Öğrenci | Ulusal Belgesel Ödülleri Yarışması'
        ), $atts);
        
        ob_start();
        ?>
        <div class="trt-yarisma-container">
            <header class="trt-yarisma-header">
                <div class="header-content">
                    <img src="<?php echo TRT_YARISMA_PLUGIN_URL; ?>assets/images/trt-logo.png" alt="TRT Logo" class="trt-logo">
                    <div class="header-text">
                        <h1><?php echo esc_html($atts['title']); ?></h1>
                        <p>Başvuru Sistemi</p>
                    </div>
                </div>
            </header>

            <div class="instructions-section">
                <h2>Başvuru Yönergeleri</h2>
                <div class="key-info">
                    <div class="info-box">
                        <h3>Başvuru Sürecinde Bilinmesi Gerekenler</h3>
                        <p><strong>1.</strong> Aynı eserin tamamı veya parçaları ile birden fazla kategoride başvuru yapılamaz.</p>
                        <p><strong>2.</strong> Yarışma süreci boyunca belirttiğiniz e-posta adresi üzerinden sizi bilgilendireceğiz. Bu nedenle sisteme giriş yaptığınız e-posta adresini (spam, istenmeyen posta, junk vb. olarak adlandırılan klasörler dahil) aralıklarla kontrol ediniz.</p>
                    </div>
                </div>

                <div class="category-selection">
                    <h3>Başvuru yapmak istediğiniz kategoriyi seçiniz.</h3>
                    <div class="category-options">
                        <label class="category-option">
                            <input type="radio" name="category" value="professional">
                            <span>Profesyonel | Ulusal Belgesel Ödülleri Yarışması</span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="student" checked>
                            <span>Öğrenci | Ulusal Belgesel Ödülleri Yarışması</span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="international">
                            <span>International Competition</span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="project">
                            <span>Proje Destek Yarışması</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-steps">
                    <div class="progress-step active" data-step="1">
                        <div class="step-icon">📄</div>
                        <div class="step-content">
                            <div class="step-title">Eser Linki ve Bilgileri</div>
                            <div class="step-number">1</div>
                        </div>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-icon">👤</div>
                        <div class="step-content">
                            <div class="step-title">Eser Sahibi Bilgileri</div>
                            <div class="step-number">2</div>
                        </div>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-icon">📋</div>
                        <div class="step-content">
                            <div class="step-title">Katılım Sözleşmesi</div>
                            <div class="step-number">3</div>
                        </div>
                    </div>
                    <div class="progress-step" data-step="4">
                        <div class="step-icon">✅</div>
                        <div class="step-content">
                            <div class="step-title">Başvuru Özeti ve Onay</div>
                            <div class="step-number">4</div>
                        </div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 25%;"></div>
                </div>
            </div>

            <form id="trt-student-form" class="trt-yarisma-form">
                <?php wp_nonce_field('trt_student_form', 'trt_student_nonce'); ?>
                
                <!-- Step 1: Eser Linki ve Bilgileri -->
                <div class="form-step active" data-step="1">
                    <h2>Eser Linki ve Bilgileri</h2>
                    
                    <div class="form-section">
                        <h3>Film Bilgileri (Zorunlu)</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category" class="form-select" disabled>
                                    <option value="student">Öğrenci | Ulusal Belgesel Ödülleri Yarışması</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Filmin Özgün Adı *</label>
                                <input type="text" name="original_title" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label>Filmin Türkçe Adı</label>
                                <input type="text" name="turkish_title" class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Özgün Dili *</label>
                                <select name="original_language" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <option value="turkish">Türkçe</option>
                                    <option value="english">İngilizce</option>
                                    <option value="arabic">Arapça</option>
                                    <option value="french">Fransızca</option>
                                    <option value="german">Almanca</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Yapımcı Ülke *</label>
                                <select name="production_country" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <option value="turkey">Türkiye</option>
                                    <option value="usa">ABD</option>
                                    <option value="uk">İngiltere</option>
                                    <option value="france">Fransa</option>
                                    <option value="germany">Almanya</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                            <div class="form-group duration-group">
                                <label>Süresi *</label>
                                <div class="duration-input">
                                    <input type="number" name="duration" class="form-input" required min="1" max="999">
                                    <span class="duration-unit">Dakika</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Ses Bilgisi</label>
                                <input type="text" name="audio_info" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Müzik/Özgün Müzik Bilgisi</label>
                                <input type="text" name="music_info" class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Yapım Formatı Ekran Oranı</label>
                                <select name="aspect_ratio" class="form-select">
                                    <option value="">Seçiniz</option>
                                    <option value="16:9">16:9</option>
                                    <option value="4:3">4:3</option>
                                    <option value="21:9">21:9</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Yapım Tarihi (Ay/Yıl)</label>
                                <input type="month" name="production_date" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Filmin Kısa Özeti (Maksimum 2500 Karakter) *</label>
                            <textarea name="summary" class="form-textarea" rows="4" required minlength="1" maxlength="2500"></textarea>
                            <div class="character-count">
                                <span class="current-count">0</span>/1000 karakter
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>İndirilebilir Film Linki *</label>
                                <input type="url" name="download_link" class="form-input" required>
                                <small class="form-note">*Filminizi Google Drive'a yüklemenizi gerekmektedir.</small>
                            </div>
                            <div class="form-group">
                                <label>İndirilebilir Link Şifresi</label>
                                <input type="text" name="download_password" class="form-input">
                                <small class="form-note">*İndirme için şifre gerekli değilse bu alanı boş bırakınız.</small>
                            </div>
                        </div>

                        <div class="format-info">
                            <ul>
                                <li>Kabul edilen formatlar: mpeg2, mov, mxf, mp4</li>
                                <li>Verdiğiniz bağlantıların indirilebilir olduğundan emin olunuz. (Youtube bağlantıları kabul edilmeyecektir.)</li>
                            </ul>
                        </div>

                        <div class="dynamic-sections">
                            <div class="dynamic-section">
                                <h4>Katıldığı Festivaller (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="festivals">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="festivals">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Aldığı Ödüller (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="awards">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="awards">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Sosyal Medya Hesapları (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="social_media">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="social_media">+ Ekle</button>
                            </div>

                            <div class="form-group">
                                <label>IMDB Linki (Opsiyonel)</label>
                                <input type="url" name="imdb_link" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Eser Sahibi Bilgileri -->
                <div class="form-step" data-step="2">
                    <h2>Eser Sahibi Bilgileri</h2>
                    
                    <div class="form-section">
                        <div class="dynamic-section">
                            <h3>Yönetmen Bilgileri (En az bir yönetmen eklenmeli) *</h3>
                            <div class="dynamic-items" data-field="directors">
                                <!-- Dynamic items will be added here -->
                            </div>
                            <button type="button" class="add-btn" data-field="directors">+ Ekle</button>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Ad *</label>
                                <input type="text" name="applicant_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label>Soyad *</label>
                                <input type="text" name="applicant_surname" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Telefon *</label>
                                <input type="tel" name="phone" class="form-input" required placeholder="+90 (___) ___ __ __">
                            </div>
                            <div class="form-group">
                                <label>E-Posta Adresi *</label>
                                <input type="email" name="email" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Adres *</label>
                            <textarea name="address" class="form-textarea" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Özgeçmiş</label>
                            <textarea name="biography" class="form-textarea" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Filmografi</label>
                            <textarea name="filmography" class="form-textarea" rows="4"></textarea>
                        </div>

                        <div class="form-group file-upload">
                            <label>Öğrenci Belgesi *</label>
                            <div class="upload-area">
                                <div class="upload-icon">📄</div>
                                <p>Dosya yüklemek için <span class="upload-link">buraya tıklayın</span> yada dosyayı sürükleyip bırakın.</p>
                                <small>(Dosya formatı: pdf, omat ve boyutu, limitlerin küçük olmalı)</small>
                            </div>
                            <input type="file" name="student_document" class="file-input" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>

                        <div class="dynamic-sections">
                            <div class="dynamic-section">
                                <h4>Yapımcı Bilgileri (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="producers">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="producers">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Metin Yazarı Bilgileri (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="writers">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="writers">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Destekçi Kurum/Kuruluş (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="sponsors">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="sponsors">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Satış Yetkilisi (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="sales_agent">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="sales_agent">+ Ekle</button>
                            </div>

                            <div class="dynamic-section">
                                <h4>Teknik Ekip (Opsiyonel)</h4>
                                <div class="dynamic-items" data-field="crew">
                                    <!-- Dynamic items will be added here -->
                                </div>
                                <button type="button" class="add-btn" data-field="crew">+ Ekle</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Katılım Sözleşmesi -->
                <div class="form-step" data-step="3">
                    <h2>Katılım Sözleşmesi</h2>
                    
                    <div class="agreement-content">
                        <div class="agreement-text">
                            <h3>TARAFLAR</h3>
                            <p><strong>1.1. DÜZENLEYİCİ:</strong><br>
                            Unvan : TÜRKİYE RADYO TELEVİZYON KURUMU ("TRT")<br>
                            Adresi : TRT Genel Müdürlüğü<br>
                            E-posta : geleceginiletigimicleri@trt.net.tr<br>
                            Ticaret Sicil No : 13446<br>
                            Vergi Dairesi/No : Ankara Kurumlar Vergi Dairesi / 8790032867.12. KATILIMCI ("KATILIMCI")<br>
                            Grup içinde katılım olması halinde;<br>
                            İlgili Grup İsmi :<br>
                            Grup Temsilcisi :<br>
                            Grup yedek temsilcisi :<br>
                            18 Yaşından Küçükler İçin Katılımcının Yasal Velisinin;<br>
                            Ad Soyad : ("KATILIMCI VELİSİ")<br>
                            Adresi :<br>
                            Telefon : E-Posta : TC Kimlik :<br>
                            1.3. Her iki taraf 1.1 ve 1.2. maddelerinde belirtilen adreslerini tebligat adresleri olarak kabul etmişlerdir. Adres değişiklikleri usulüne uygun şekilde karşı tarafa tebliğ edilmedikçe yukarıda bildirilen adrese yapılacak tebligat ilgili tarafa yapılmış sayılır.</p>
                        </div>
                        
                        <div class="agreement-checkboxes">
                            <label class="checkbox-label">
                                <input type="checkbox" name="agreement_accept" required>
                                <span class="checkmark"></span>
                                Katılım Sözleşmesini Okudum ve Kabul Ediyorum.
                            </label>
                            
                            <label class="checkbox-label">
                                <input type="checkbox" name="privacy_accept" required>
                                <span class="checkmark"></span>
                                Kişisel Verilerin Korunması Metnini Okudum ve Kabul ediyorum
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Başvuru Özeti ve Onay -->
                <div class="form-step" data-step="4">
                    <h2>Başvuru Özeti ve Onay</h2>
                    
                    <div class="summary-content">
                        <div class="summary-section">
                            <h3>Eser Linki ve Bilgileri</h3>
                            <div class="summary-item">
                                <strong>Film Bilgileri (Zorunlu)</strong>
                                <div class="summary-details" id="film-summary">
                                    <!-- Film bilgileri buraya dinamik olarak eklenecek -->
                                </div>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h3>Eser Sahibi Bilgileri</h3>
                            <div class="summary-item">
                                <strong>Yönetmen Bilgileri (Zorunlu)</strong>
                                <div class="summary-details" id="director-summary">
                                    <!-- Yönetmen bilgileri buraya dinamik olarak eklenecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="final-submit">
                        <button type="submit" class="btn-submit">Başvuruyu Tamamla</button>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-back" style="display: none;">Geri Dön</button>
                    <button type="button" class="btn-next">İleri</button>
                </div>
            </form>

            <div class="deadline-notice">
                <p>Başvuruların bitmesine son <strong>23 gün!</strong></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

