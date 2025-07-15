<?php
/**
 * Profesyonel | Ulusal Belgesel Ödülleri Yarışması Shortcode sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Shortcodes_Professional {
    
    public function __construct() {
        add_shortcode('trt_professional_form', array($this, 'render_professional_form'));
    }
    
    public function render_professional_form($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Profesyonel | Ulusal Belgesel Ödülleri Yarışması'
        ), $atts);
        
        ob_start();
        ?>
        <div class="trt-yarisma-container">
            <div class="trt-yarisma-header">
                <div class="trt-logo">
                    <img src="<?php echo TRT_YARISMA_PLUGIN_URL; ?>assets/images/trt-logo.png" alt="TRT Logo" />
                </div>
                <div class="user-info">
                    <span class="user-name">Mehmet Yusuf Karaca</span>
                    <button class="logout-btn">Çıkış Yap</button>
                </div>
            </div>
            
            <div class="trt-yarisma-content">
                <h1 class="form-title"><?php echo esc_html($atts['title']); ?></h1>
                
                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-step active" data-step="1">
                        <div class="step-icon">📄</div>
                        <div class="step-label">Eser Linki ve Bilgileri</div>
                        <div class="step-number">1</div>
                        <div class="step-status">Düzenle</div>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-icon">👤</div>
                        <div class="step-label">Eser Sahibi Bilgileri</div>
                        <div class="step-number">2</div>
                        <div class="step-status">Düzenle</div>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-icon">📋</div>
                        <div class="step-label">Katılım Sözleşmesi</div>
                        <div class="step-number">3</div>
                        <div class="step-status">Düzenle</div>
                    </div>
                    <div class="progress-step" data-step="4">
                        <div class="step-icon">✓</div>
                        <div class="step-label">Başvuru Özeti ve Onay</div>
                        <div class="step-number">4</div>
                        <div class="step-status">Düzenle</div>
                    </div>
                </div>
                
                <form id="trt-professional-form" class="trt-yarisma-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('trt_professional_nonce', 'professional_nonce'); ?>
                    
                    <!-- Step 1: Film Bilgileri -->
                    <div class="form-step active" data-step="1">
                        <h2>Eser Linki ve Bilgileri</h2>
                        
                        <div class="form-section">
                            <h3>Film Bilgileri <span class="required">(Zorunlu)</span></h3>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select id="category" name="category" required>
                                    <option value="Profesyonel | Ulusal Belgesel Ödülleri Yarışması" selected>Profesyonel | Ulusal Belgesel Ödülleri Yarışması</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="original_title">Filmin Özgün Adı <span class="required">*</span></label>
                                    <input type="text" id="original_title" name="original_title" required>
                                </div>
                                <div class="form-group">
                                    <label for="turkish_title">Filmin Türkçe Adı</label>
                                    <input type="text" id="turkish_title" name="turkish_title">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="original_language">Özgün Dili <span class="required">*</span></label>
                                    <select id="original_language" name="original_language" required>
                                        <option value="">Seçiniz</option>
                                        <option value="Türkçe">Türkçe</option>
                                        <option value="İngilizce">İngilizce</option>
                                        <option value="Fransızca">Fransızca</option>
                                        <option value="Almanca">Almanca</option>
                                        <option value="İspanyolca">İspanyolca</option>
                                        <option value="İtalyanca">İtalyanca</option>
                                        <option value="Arapça">Arapça</option>
                                        <option value="Kürtçe">Kürtçe</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="production_country">Yapımcı Ülke <span class="required">*</span></label>
                                    <select id="production_country" name="production_country" required>
                                        <option value="">Seçiniz</option>
                                        <option value="Türkiye">Türkiye</option>
                                        <option value="ABD">ABD</option>
                                        <option value="İngiltere">İngiltere</option>
                                        <option value="Fransa">Fransa</option>
                                        <option value="Almanya">Almanya</option>
                                        <option value="İspanya">İspanya</option>
                                        <option value="İtalya">İtalya</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Süresi <span class="required">*</span></label>
                                    <input type="number" id="duration" name="duration" placeholder="Dakika" min="1" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="audio_info">Ses Bilgisi</label>
                                    <input type="text" id="audio_info" name="audio_info">
                                </div>
                                <div class="form-group">
                                    <label for="music_info">Müzik/Özgün Müzik Bilgisi</label>
                                    <input type="text" id="music_info" name="music_info">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="aspect_ratio">Yapım Formatı Ekran Oranı</label>
                                    <select id="aspect_ratio" name="aspect_ratio">
                                        <option value="">Seçiniz</option>
                                        <option value="16:9">16:9</option>
                                        <option value="4:3">4:3</option>
                                        <option value="21:9">21:9</option>
                                        <option value="1:1">1:1</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="production_date">Yapım Tarihi (Ay/Yıl)</label>
                                    <input type="month" id="production_date" name="production_date">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="summary">Filmin Kısa Özeti (Maksimum 2500 Karakter) <span class="required">*</span></label>
                                <textarea id="summary" name="summary" rows="6" minlength="1" maxlength="2500" required></textarea>
                                <div class="character-count">
                                    <span id="summary-count">0</span>/1000 karakter
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="download_link">İndirilebilir Film Linki <span class="required">*</span></label>
                                    <input type="url" id="download_link" name="download_link" required>
                                    <small>*Filminizi Google Drive'a yüklememiş gerektirmektedir.</small>
                                </div>
                                <div class="form-group">
                                    <label for="download_password">İndirilebilir Link Şifresi</label>
                                    <input type="text" id="download_password" name="download_password">
                                    <small>*İndirme için şifre gerekli değilse bu alanı boş bırakınız</small>
                                </div>
                            </div>
                            
                            <div class="info-box">
                                <ul>
                                    <li>Kabul edilen formatlar: mpeg2, mov, mxf, mp4</li>
                                    <li>Verdiğiniz bağlantıların indirilebilir olduğundan emin olunuz (Youtube bağlantıları kabul edilmeyecektir.)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Dynamic Sections -->
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Katıldığı Festivaller <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="festivals">+ Ekle</button>
                            </div>
                            <div id="festivals-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Aldığı Ödüller <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="awards">+ Ekle</button>
                            </div>
                            <div id="awards-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Sosyal Medya Hesapları <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="social_media">+ Ekle</button>
                            </div>
                            <div id="social_media-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>IMDB Linki <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="imdb">+ Ekle</button>
                            </div>
                            <div id="imdb-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="form-navigation">
                            <button type="button" class="btn-next">İleri</button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Eser Sahibi Bilgileri -->
                    <div class="form-step" data-step="2">
                        <h2>Eser Sahibi Bilgileri</h2>
                        
                        <div class="form-section">
                            <div class="section-header">
                                <h3>Yönetmen Bilgileri <span class="required">(En az bir yönetmen eklenmeli)</span></h3>
                                <button type="button" class="add-btn" data-target="directors">+ Ekle</button>
                            </div>
                            <div id="directors-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Başvuran Bilgileri</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="applicant_name">Ad <span class="required">*</span></label>
                                    <input type="text" id="applicant_name" name="applicant_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="applicant_surname">Soyad <span class="required">*</span></label>
                                    <input type="text" id="applicant_surname" name="applicant_surname" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Telefon <span class="required">*</span></label>
                                    <input type="tel" id="phone" name="phone" placeholder="+90 (_ _ _) _ _ _ _ _ _ _" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-Posta Adresi <span class="required">*</span></label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Adres <span class="required">*</span></label>
                                <textarea id="address" name="address" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="biography">Özgeçmiş</label>
                                <textarea id="biography" name="biography" rows="4"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="filmography">Filmografi</label>
                                <textarea id="filmography" name="filmography" rows="4"></textarea>
                            </div>
                        </div>
                        
                        <!-- Optional Person Sections -->
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Yapımcı Bilgileri <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="producers">+ Ekle</button>
                            </div>
                            <div id="producers-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Metin Yazarı Bilgileri <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="writers">+ Ekle</button>
                            </div>
                            <div id="writers-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Destekçi Kurum/Kuruluş <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="sponsors">+ Ekle</button>
                            </div>
                            <div id="sponsors-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Satış Yetkilisi <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="sales_agent">+ Ekle</button>
                            </div>
                            <div id="sales_agent-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="dynamic-section">
                            <div class="section-header">
                                <h3>Teknik Ekip <span class="optional">(Opsiyonel)</span></h3>
                                <button type="button" class="add-btn" data-target="crew">+ Ekle</button>
                            </div>
                            <div id="crew-container" class="dynamic-container"></div>
                        </div>
                        
                        <div class="form-navigation">
                            <button type="button" class="btn-prev">Geri Dön</button>
                            <button type="button" class="btn-next">İleri</button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Katılım Sözleşmesi -->
                    <div class="form-step" data-step="3">
                        <h2>Katılım Sözleşmesi</h2>
                        
                        <div class="agreement-content">
                            <h3>TARAFLAR</h3>
                            <p><strong>1.1. DÜZENLEYİCİ:</strong><br>
                            Unvan : TÜRKİYE RADYO TELEVİZYON KURUMU ("TRT")<br>
                            Adresi : TRT Genel Müdürlüğü<br>
                            E-posta : geleceginiletigimcileri@trt.net.tr<br>
                            Ticaret Sicil No : 13446<br>
                            Vergi Dairesi/No : Ankara Kurumlar Vergi Dairesi / 8790032867.12. KATILIMCI ("KATILIMCI")</p>
                            
                            <p>Grup içinde katılım olması halinde;<br>
                            İlgili Grup İsmi :<br>
                            Grup Temsilcisi :<br>
                            Grup yedek temsilcisi :<br>
                            18 Yaşından Küçükler İçin Katılımcının Yasal Velisinin;<br>
                            Ad Soyad : ("KATILIMCI VELİSİ")<br>
                            Adresi :<br>
                            Telefon : E-Posta : TC Kimlik :</p>
                            
                            <p><strong>1.3.</strong> Her iki taraf 1.1 ve 1.2. maddelerinde belirtilen adreslerini tebligat adresleri olarak kabul etmişlerdir. Adres değişikliklerini usulüne uygun şekilde karşı tarafa tebliğ edilmedikçe yukarıda bildirilen adrese yapılacak tebligat ilgili tarafa yapılmış sayılır.</p>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="agreement_accept" name="agreement_accept" required>
                                <span class="checkmark"></span>
                                Katılım Sözleşmesini Okudum ve Kabul Ediyorum. <span class="required">*</span>
                            </label>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="privacy_accept" name="privacy_accept" required>
                                <span class="checkmark"></span>
                                Kişisel Verilerin Korunması Metnini Okudum ve Kabul ediyorum <span class="required">*</span>
                            </label>
                        </div>
                        
                        <div class="form-navigation">
                            <button type="button" class="btn-prev">Geri Dön</button>
                            <button type="button" class="btn-next">İleri</button>
                        </div>
                    </div>
                    
                    <!-- Step 4: Başvuru Özeti -->
                    <div class="form-step" data-step="4">
                        <h2>Başvuru Özeti ve Onay</h2>
                        
                        <div class="summary-content">
                            <h3>Eser Linki ve Bilgileri</h3>
                            <div id="summary-film-info" class="summary-section"></div>
                            
                            <h3>Eser Sahibi Bilgileri</h3>
                            <div id="summary-applicant-info" class="summary-section"></div>
                        </div>
                        
                        <div class="form-navigation">
                            <button type="button" class="btn-prev">Geri Dön</button>
                            <button type="submit" class="btn-submit">Başvuruyu Tamamla</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="trt-loading" class="loading-overlay" style="display: none;">
            <div class="loading-spinner"></div>
            <p>Başvurunuz gönderiliyor...</p>
        </div>
        
        <div id="trt-message" class="message-overlay" style="display: none;">
            <div class="message-content">
                <div class="message-icon"></div>
                <h3 class="message-title"></h3>
                <p class="message-text"></p>
                <button class="message-close">Tamam</button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

