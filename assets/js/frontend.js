/**
 * TRT Yarışma Başvuru Sistemi - Frontend JavaScript
 */

// Güvenli başlatma
(function() {
    'use strict';
    
    // jQuery kontrolü
    if (typeof jQuery === 'undefined') {
        console.error('TRT Yarışma: jQuery bulunamadı');
        return;
    }

    // Ana uygulama
    (function($) {
        let selectedCategory = '';
        let currentStep = 1;
        let formData = {};

        // Sayfa yüklendiğinde
        $(document).ready(function() {
            try {
                initializeApp();
            } catch (error) {
                console.error('TRT Yarışma başlatma hatası:', error);
            }
        });

        function initializeApp() {
            // Event listener'ları ekle
            bindEvents();
            
            // Başlangıç ekranını göster
            showStartScreen();
        }

        function bindEvents() {
            // Kategori seçimi
            $(document).on('change', 'input[name="trt_category"]', function() {
                selectedCategory = $(this).val();
                updateCategorySelection();
                updateLanguage();
                
                // Başvuruya başla butonunu aktif et
                $('#trt-start-btn').prop('disabled', false);
            });

            // Başvuruya başla butonu
            $(document).on('click', '#trt-start-btn', function() {
                startApplication();
            });

            // Geri butonu
            $(document).on('click', '#trt-back-btn', function() {
                goBackToStart();
            });

            // Form navigation
            $(document).on('click', '#trt-next-btn', function() {
                nextStep();
            });

            $(document).on('click', '#trt-prev-btn', function() {
                prevStep();
            });

            // Form submit
            $(document).on('submit', '#trt-application-form', function(e) {
                e.preventDefault();
                submitForm();
            });
        }

        function updateCategorySelection() {
            // Seçili kategoriyi görsel olarak güncelle
            $('.trt-category-option').removeClass('selected');
            $('input[name="trt_category"]:checked').closest('.trt-category-option').addClass('selected');
            
            console.log('Kategori seçildi:', selectedCategory);
        }

        function updateLanguage() {
            const isInternational = selectedCategory === 'international';
            
            if (isInternational) {
                // İngilizce metinleri güncelle
                $('#trt-main-title').text('TRT International Documentary Competition');
                $('#trt-main-subtitle').text('Application System');
                $('#trt-instructions-title').text('Application Instructions');
                $('#trt-key-info-title').text('Key Information for Applicants');
                $('#trt-instruction-1').html('<strong>1.</strong> It is not allowed to submit the same work, either in its entirety or in parts, to multiple categories.');
                $('#trt-instruction-2').html('<strong>2.</strong> We will inform you throughout the competition process via the e-mail address you provided. Therefore, please regularly check your e-mail inbox (including spam, junk, and other similar folders).');
                $('#trt-category-title').text('Select your application category');
                $('#trt-deadline-text').html('Applications close in <strong>23 days!</strong>');
                $('#trt-start-btn').text('Start Application');
                $('#trt-back-btn').text('Back to Start Screen');
            } else {
                // Türkçe metinleri geri yükle
                $('#trt-main-title').text('TRT Yarışma Başvuru Sistemi');
                $('#trt-main-subtitle').text('Başvuru Sistemi');
                $('#trt-instructions-title').text('Başvuru Yönergeleri');
                $('#trt-key-info-title').text('Başvuru Sürecinde Bilinmesi Gerekenler');
                $('#trt-instruction-1').html('<strong>1.</strong> Aynı eserin tamamı veya parçaları ile birden fazla kategoride başvuru yapılamaz.');
                $('#trt-instruction-2').html('<strong>2.</strong> Yarışma süreci boyunca belirttiğiniz e-posta adresi üzerinden sizi bilgilendireceğiz. Bu nedenle sisteme giriş yaptığınız e-posta adresini (spam, istenmeyen posta, junk vb. olarak adlandırılan klasörler dahil) aralıklarla kontrol ediniz.');
                $('#trt-category-title').text('Başvuru yapmak istediğiniz kategoriyi seçiniz.');
                $('#trt-deadline-text').html('Başvuruların bitmesine son <strong>23 gün!</strong>');
                $('#trt-start-btn').text('Başvuruya Başla');
                $('#trt-back-btn').text('Başlangıç Ekranına Dön');
            }
        }

        function startApplication() {
            if (!selectedCategory) {
                alert('Lütfen bir kategori seçiniz.');
                return;
            }

            console.log('Başvuru başlatılıyor, kategori:', selectedCategory);

            // Kategori bazında farklı shortcode'lara yönlendir
            switch(selectedCategory) {
                case 'international':
                    loadInternationalForm();
                    break;
                case 'professional':
                    loadProfessionalForm();
                    break;
                case 'student':
                    loadStudentForm();
                    break;
                case 'project-support':
                    loadProjectSupportForm();
                    break;
                default:
                    alert('Geçersiz kategori seçimi');
                    return;
            }
        }

        function loadInternationalForm() {
            // International form için yönlendirme veya form yükleme
            window.location.href = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'form=international';
        }

        function loadProfessionalForm() {
            // Professional form için yönlendirme veya form yükleme
            window.location.href = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'form=professional';
        }

        function loadStudentForm() {
            // Student form için yönlendirme veya form yükleme
            window.location.href = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'form=student';
        }

        function loadProjectSupportForm() {
            // Project support form yükleme
            showFormScreen();
            updateFormTitle();
            loadStep(1);
        }

        function goBackToStart() {
            // Başlangıç ekranını göster
            showStartScreen();
            
            // Seçimi temizle
            selectedCategory = '';
            $('input[name="trt_category"]').prop('checked', false);
            $('.trt-category-option').removeClass('selected');
            $('#trt-start-btn').prop('disabled', true);
            
            // Sayfa en üste scroll
            window.scrollTo(0, 0);
            
            // Dili sıfırla
            updateLanguage();
        }

        function showStartScreen() {
            $('#trt-start-screen').show();
            $('#trt-form-screen').hide();
        }

        function showFormScreen() {
            $('#trt-start-screen').hide();
            $('#trt-form-screen').show();
        }

        function updateFormTitle() {
            const isInternational = selectedCategory === 'international';
            let title = '';
            let subtitle = '';

            switch (selectedCategory) {
                case 'professional':
                    title = 'TRT Profesyonel Ulusal Belgesel Ödülleri Yarışması';
                    subtitle = 'Başvuru Sistemi';
                    break;
                case 'student':
                    title = 'TRT Öğrenci Ulusal Belgesel Ödülleri Yarışması';
                    subtitle = 'Başvuru Sistemi';
                    break;
                case 'international':
                    title = 'TRT International Documentary Competition';
                    subtitle = 'Application System';
                    break;
                case 'project-support':
                    title = 'TRT Proje Destek Yarışması';
                    subtitle = 'Başvuru Sistemi';
                    break;
            }

            $('#trt-form-title').text(title);
            $('#trt-form-subtitle').text(subtitle);
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < 4) {
                    currentStep++;
                    loadStep(currentStep);
                    updateProgressBar();
                    window.scrollTo(0, 0);
                } else {
                    // Son adım - formu gönder
                    submitForm();
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                loadStep(currentStep);
                updateProgressBar();
                window.scrollTo(0, 0);
            }
        }

        function loadStep(step) {
            currentStep = step;
            updateProgressBar();
            updateNavigationButtons();
            
            // Form içeriğini yükle
            const content = getStepContent(step);
            $('#trt-form-content').html(content);
            
            // Adım özel JavaScript'lerini çalıştır
            initializeStepEvents(step);
        }

        function updateProgressBar() {
            $('.trt-progress-step').removeClass('trt-active');
            
            // 0. adım (Başvuruya Başla) her zaman aktif
            $('.trt-progress-step[data-step="0"]').addClass('trt-active');
            
            // Mevcut adıma kadar olan adımları aktif yap
            for (let i = 1; i <= currentStep; i++) {
                $('.trt-progress-step[data-step="' + i + '"]').addClass('trt-active');
            }
        }

        function updateNavigationButtons() {
            // Geri butonu
            if (currentStep > 1) {
                $('#trt-prev-btn').show();
            } else {
                $('#trt-prev-btn').hide();
            }

            // İleri butonu
            if (currentStep < 4) {
                $('#trt-next-btn').text(selectedCategory === 'international' ? 'Next' : 'İleri').show();
            } else {
                $('#trt-next-btn').text(selectedCategory === 'international' ? 'Complete and Save' : 'Tamamla ve Kaydet').show();
            }
        }

        function getStepContent(step) {
            const isInternational = selectedCategory === 'international';
            
            switch (step) {
                case 1:
                    return getStep1Content(isInternational);
                case 2:
                    return getStep2Content(isInternational);
                case 3:
                    return getStep3Content(isInternational);
                case 4:
                    return getStep4Content(isInternational);
                default:
                    return '<p>Hata: Geçersiz adım</p>';
            }
        }

        function getStep1Content(isInternational) {
            const title = isInternational ? 'Work Link and Details' : 'Eser Linki ve Bilgileri';
            const filmInfoTitle = isInternational ? 'Film Information (Required)' : 'Film Bilgileri (Zorunlu)';
            
            return `
                <div class="trt-form-step">
                    <h2>${title}</h2>
                    
                    <div class="trt-form-section">
                        <h3>${filmInfoTitle}</h3>
                        
                        <div class="trt-form-group">
                            <label>${isInternational ? 'Category' : 'Kategori'}</label>
                            <select id="trt-category-select" disabled>
                                <option value="${selectedCategory}">${getCategoryDisplayName(selectedCategory)}</option>
                            </select>
                        </div>

                        <div class="trt-form-row">
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Original Title of the Film *' : 'Filmin Özgün Adı *'}</label>
                                <input type="text" id="trt-original-title" required>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Turkish Title of the Film' : 'Filmin Türkçe Adı'}</label>
                                <input type="text" id="trt-turkish-title">
                            </div>
                        </div>

                        <div class="trt-form-row">
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Original Language *' : 'Özgün Dili *'}</label>
                                <select id="trt-original-language" required>
                                    <option value="">${isInternational ? 'Select' : 'Seçiniz'}</option>
                                    <option value="turkish">${isInternational ? 'Turkish' : 'Türkçe'}</option>
                                    <option value="english">${isInternational ? 'English' : 'İngilizce'}</option>
                                    <option value="arabic">${isInternational ? 'Arabic' : 'Arapça'}</option>
                                    <option value="french">${isInternational ? 'French' : 'Fransızca'}</option>
                                    <option value="german">${isInternational ? 'German' : 'Almanca'}</option>
                                    <option value="other">${isInternational ? 'Other' : 'Diğer'}</option>
                                </select>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Production Country *' : 'Yapımcı Ülke *'}</label>
                                <select id="trt-production-country" required>
                                    <option value="">${isInternational ? 'Select' : 'Seçiniz'}</option>
                                    <option value="turkey">${isInternational ? 'Turkey' : 'Türkiye'}</option>
                                    <option value="usa">${isInternational ? 'USA' : 'ABD'}</option>
                                    <option value="uk">${isInternational ? 'United Kingdom' : 'İngiltere'}</option>
                                    <option value="france">${isInternational ? 'France' : 'Fransa'}</option>
                                    <option value="germany">${isInternational ? 'Germany' : 'Almanya'}</option>
                                    <option value="other">${isInternational ? 'Other' : 'Diğer'}</option>
                                </select>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Duration *' : 'Süre *'}</label>
                                <input type="text" id="trt-duration" placeholder="${isInternational ? 'Minutes' : 'Dakika'}" required>
                            </div>
                        </div>

                        <div class="trt-form-group">
                            <label>${isInternational ? 'Brief Synopsis of the Film (Maximum 2500 Characters) *' : 'Filmin Kısa Özeti (Maksimum 2500 Karakter) *'}</label>
                            <textarea id="trt-synopsis" rows="5" maxlength="2500" required></textarea>
                            <div class="trt-character-count">
                                <span id="trt-synopsis-count">0</span>/2500 ${isInternational ? 'characters' : 'karakter'}
                            </div>
                        </div>

                        <div class="trt-form-row">
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Downloadable Film Link *' : 'İndirilebilir Film Linki *'}</label>
                                <input type="url" id="trt-download-link" required>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Download Link Password' : 'İndirilebilir Link Şifresi'}</label>
                                <input type="text" id="trt-download-password">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function getStep2Content(isInternational) {
            const title = isInternational ? 'Work Owner Information' : 'Eser Sahibi Bilgileri';
            const directorTitle = isInternational ? 'Director Information (Required)' : 'Yönetmen Bilgileri (Zorunlu)';
            
            return `
                <div class="trt-form-step">
                    <h2>${title}</h2>
                    
                    <div class="trt-form-section">
                        <h3>${directorTitle}</h3>
                        
                        <div class="trt-form-row">
                            <div class="trt-form-group">
                                <label>${isInternational ? 'First Name *' : 'Ad *'}</label>
                                <input type="text" id="trt-director-name" required>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Last Name *' : 'Soyad *'}</label>
                                <input type="text" id="trt-director-surname" required>
                            </div>
                        </div>

                        <div class="trt-form-row">
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Phone *' : 'Telefon *'}</label>
                                <input type="tel" id="trt-director-phone" required>
                            </div>
                            <div class="trt-form-group">
                                <label>${isInternational ? 'Email Address *' : 'E-posta Adresi *'}</label>
                                <input type="email" id="trt-director-email" required>
                            </div>
                        </div>

                        <div class="trt-form-group">
                            <label>${isInternational ? 'Address *' : 'Adres *'}</label>
                            <textarea id="trt-director-address" rows="3" required></textarea>
                        </div>

                        <div class="trt-form-group">
                            <label>${isInternational ? 'Biography (Maximum 2500 Characters) *' : 'Özgeçmiş (Maksimum 2500 Karakter) *'}</label>
                            <textarea id="trt-director-bio" rows="5" maxlength="2500" required></textarea>
                            <div class="trt-character-count">
                                <span id="trt-bio-count">0</span>/2500 ${isInternational ? 'characters' : 'karakter'}
                            </div>
                        </div>

                        <div class="trt-form-group">
                            <label>${isInternational ? 'Filmography' : 'Filmografi'}</label>
                            <textarea id="trt-director-filmography" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function getStep3Content(isInternational) {
            const title = isInternational ? 'Participation Agreement' : 'Katılım Sözleşmesi';
            
            return `
                <div class="trt-form-step">
                    <h2>${title}</h2>
                    
                    <div class="trt-agreement-content">
                        <h3>${isInternational ? 'PARTIES' : 'TARAFLAR'}</h3>
                        <p><strong>1.1. ${isInternational ? 'ORGANIZER' : 'DÜZENLEYEN'}:</strong></p>
                        <p>${isInternational ? 'Title' : 'Unvanı'}: ${isInternational ? 'TURKISH RADIO AND TELEVISION CORPORATION ("TRT")' : 'TÜRKİYE RADYO TELEVİZYON KURUMU ("TRT")'}</p>
                        <p>${isInternational ? 'Address' : 'Adresi'}: TRT ${isInternational ? 'General Directorate' : 'Genel Müdürlüğü'}</p>
                        <p>E-posta: geleceginiletisilmcileri@trt.net.tr</p>
                        <p>${isInternational ? 'Trade Registry No' : 'Ticaret Sicil No'}: 13446</p>
                        <p>${isInternational ? 'Tax Office/No' : 'Vergi Dairesi/No'}: Ankara ${isInternational ? 'Corporate Tax Office' : 'Kurumlar Vergi Dairesi'} / 8790032867</p>
                        
                        <p><strong>1.2. ${isInternational ? 'PARTICIPANT ("PARTICIPANT")' : 'KATILIMCI ("KATILIMCI")'}</strong></p>
                        <p>${isInternational ? 'For group participation;' : 'Grup katılımı durumunda;'}</p>
                        <p>${isInternational ? 'Related Group Name:' : 'İlgili Grup Adı:'}</p>
                        <p>${isInternational ? 'Group Representative:' : 'Grup Temsilcisi:'}</p>
                        <p>${isInternational ? 'For Participants Under 18, Legal Guardian:' : '18 Yaş Altı Reşit Olmayan Katılım İçin Yasal Vasi:'}</p>
                        <p>${isInternational ? 'Name Surname: ("PARTICIPANT GUARDIAN")' : 'Ad Soyad: ("KATILIMCI VASİSİ")'}</p>
                        <p>${isInternational ? 'Address:' : 'Adres:'}</p>
                        <p>${isInternational ? 'Phone: Email: ID No:' : 'Telefon: E-posta: TC Kimlik No:'}</p>
                    </div>

                    <div class="trt-form-group">
                        <label class="trt-checkbox-label">
                            <input type="checkbox" id="trt-agreement-accepted" required>
                            ${isInternational ? 'I have read and accept the Participation Agreement.' : 'Katılım Sözleşmesini okudum ve kabul ediyorum.'}
                        </label>
                    </div>

                    <div class="trt-form-group">
                        <label class="trt-checkbox-label">
                            <input type="checkbox" id="trt-data-protection-accepted" required>
                            ${isInternational ? 'I have read and accept the Personal Data Protection Text.' : 'Kişisel Verilerin Korunması Metnini okudum ve kabul ediyorum.'}
                        </label>
                    </div>
                </div>
            `;
        }

        function getStep4Content(isInternational) {
            const title = isInternational ? 'Application Summary and Confirmation' : 'Başvuru Özeti ve Onayı';
            
            return `
                <div class="trt-form-step">
                    <h2>${title}</h2>
                    
                    <div class="trt-summary-section">
                        <h3>${isInternational ? 'Work Link and Information' : 'Eser Linki ve Bilgileri'}</h3>
                        <div class="trt-summary-item">
                            <strong>${isInternational ? 'Film Information (Required)' : 'Film Bilgileri (Zorunlu)'}</strong>
                            <p>${isInternational ? 'Category' : 'Kategori'}: ${getCategoryDisplayName(selectedCategory)}</p>
                            <p>${isInternational ? 'Original Title' : 'Filmin Özgün Adı'}: <span id="summary-original-title">-</span></p>
                            <p>${isInternational ? 'Turkish Title' : 'Filmin Türkçe Adı'}: <span id="summary-turkish-title">-</span></p>
                            <p>${isInternational ? 'Original Language' : 'Özgün Dili'}: <span id="summary-original-language">-</span></p>
                            <p>${isInternational ? 'Production Country' : 'Yapımcı Ülke'}: <span id="summary-production-country">-</span></p>
                            <p>${isInternational ? 'Duration' : 'Süre'}: <span id="summary-duration">-</span> ${isInternational ? 'minutes' : 'dakika'}</p>
                            <p>${isInternational ? 'Synopsis' : 'Film Özeti'}: <span id="summary-synopsis">-</span></p>
                            <p>${isInternational ? 'Download Link' : 'İndirilebilir Link'}: <span id="summary-download-link">-</span></p>
                        </div>
                    </div>

                    <div class="trt-summary-section">
                        <h3>${isInternational ? 'Work Owner Information' : 'Eser Sahibi Bilgileri'}</h3>
                        <div class="trt-summary-item">
                            <strong>${isInternational ? 'Director Information (Required)' : 'Yönetmen Bilgileri (Zorunlu)'}</strong>
                            <p>${isInternational ? 'Name' : 'Ad'}: <span id="summary-director-name">-</span></p>
                            <p>${isInternational ? 'Surname' : 'Soyad'}: <span id="summary-director-surname">-</span></p>
                            <p>${isInternational ? 'Phone' : 'Telefon'}: <span id="summary-director-phone">-</span></p>
                            <p>${isInternational ? 'Email' : 'E-posta'}: <span id="summary-director-email">-</span></p>
                            <p>${isInternational ? 'Address' : 'Adres'}: <span id="summary-director-address">-</span></p>
                            <p>${isInternational ? 'Biography' : 'Özgeçmiş'}: <span id="summary-director-bio">-</span></p>
                            <p>${isInternational ? 'Filmography' : 'Filmografi'}: <span id="summary-director-filmography">-</span></p>
                        </div>
                    </div>
                </div>
            `;
        }

        function getCategoryDisplayName(category) {
            switch (category) {
                case 'professional':
                    return 'Ulusal Profesyonel Kategori';
                case 'student':
                    return 'Ulusal Öğrenci Kategorisi';
                case 'international':
                    return 'Uluslararası Profesyonel Kategori';
                case 'project-support':
                    return 'Proje Destek Kategorisi';
                default:
                    return category;
            }
        }

        function initializeStepEvents(step) {
            switch (step) {
                case 1:
                    // Karakter sayacı
                    $('#trt-synopsis').on('input', function() {
                        const count = $(this).val().length;
                        $('#trt-synopsis-count').text(count);
                    });
                    break;
                case 2:
                    // Karakter sayacı
                    $('#trt-director-bio').on('input', function() {
                        const count = $(this).val().length;
                        $('#trt-bio-count').text(count);
                    });
                    break;
                case 4:
                    // Özet bilgilerini doldur
                    fillSummary();
                    break;
            }
        }

        function fillSummary() {
            // Form verilerini özet sayfasına aktar
            $('#summary-original-title').text($('#trt-original-title').val() || '-');
            $('#summary-turkish-title').text($('#trt-turkish-title').val() || '-');
            $('#summary-original-language').text($('#trt-original-language option:selected').text() || '-');
            $('#summary-production-country').text($('#trt-production-country option:selected').text() || '-');
            $('#summary-duration').text($('#trt-duration').val() || '-');
            $('#summary-synopsis').text($('#trt-synopsis').val() || '-');
            $('#summary-download-link').text($('#trt-download-link').val() || '-');
            
            $('#summary-director-name').text($('#trt-director-name').val() || '-');
            $('#summary-director-surname').text($('#trt-director-surname').val() || '-');
            $('#summary-director-phone').text($('#trt-director-phone').val() || '-');
            $('#summary-director-email').text($('#trt-director-email').val() || '-');
            $('#summary-director-address').text($('#trt-director-address').val() || '-');
            $('#summary-director-bio').text($('#trt-director-bio').val() || '-');
            $('#summary-director-filmography').text($('#trt-director-filmography').val() || '-');
        }

        function validateCurrentStep() {
            let isValid = true;
            const isInternational = selectedCategory === 'international';

            // Hata mesajlarını temizle
            $('.trt-error-text').remove();
            $('.error').removeClass('error');

            switch (currentStep) {
                case 1:
                    // Zorunlu alanları kontrol et
                    if (!$('#trt-original-title').val().trim()) {
                        showFieldError('#trt-original-title', isInternational ? 'Original title is required' : 'Filmin özgün adı zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-original-language').val()) {
                        showFieldError('#trt-original-language', isInternational ? 'Original language selection is required' : 'Özgün dili seçimi zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-production-country').val()) {
                        showFieldError('#trt-production-country', isInternational ? 'Production country selection is required' : 'Yapımcı ülke seçimi zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-duration').val().trim()) {
                        showFieldError('#trt-duration', isInternational ? 'Duration is required' : 'Süre bilgisi zorunludur');
                        isValid = false;
                    }
                    
                    const synopsis = $('#trt-synopsis').val().trim();
                    if (!synopsis) {
                        showFieldError('#trt-synopsis', isInternational ? 'Film synopsis is required' : 'Film kısa özeti zorunludur');
                        isValid = false;
                    } else if (synopsis.length > 2500) {
                        showFieldError('#trt-synopsis', isInternational ? 'Synopsis must be maximum 2500 characters' : 'Film özeti maksimum 2500 karakter olmalıdır');
                        isValid = false;
                    }
                    
                    if (!$('#trt-download-link').val().trim()) {
                        showFieldError('#trt-download-link', isInternational ? 'Download link is required' : 'İndirilebilir film linki zorunludur');
                        isValid = false;
                    }
                    break;

                case 2:
                    if (!$('#trt-director-name').val().trim()) {
                        showFieldError('#trt-director-name', isInternational ? 'Director name is required' : 'Yönetmen adı zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-director-surname').val().trim()) {
                        showFieldError('#trt-director-surname', isInternational ? 'Director surname is required' : 'Yönetmen soyadı zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-director-phone').val().trim()) {
                        showFieldError('#trt-director-phone', isInternational ? 'Phone is required' : 'Telefon zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-director-email').val().trim()) {
                        showFieldError('#trt-director-email', isInternational ? 'Email is required' : 'E-posta zorunludur');
                        isValid = false;
                    }
                    if (!$('#trt-director-address').val().trim()) {
                        showFieldError('#trt-director-address', isInternational ? 'Address is required' : 'Adres zorunludur');
                        isValid = false;
                    }
                    
                    const bio = $('#trt-director-bio').val().trim();
                    if (!bio) {
                        showFieldError('#trt-director-bio', isInternational ? 'Biography is required' : 'Özgeçmiş zorunludur');
                        isValid = false;
                    } else if (bio.length > 2500) {
                        showFieldError('#trt-director-bio', isInternational ? 'Biography must be maximum 2500 characters' : 'Özgeçmiş maksimum 2500 karakter olmalıdır');
                        isValid = false;
                    }
                    break;

                case 3:
                    if (!$('#trt-agreement-accepted').is(':checked')) {
                        showFieldError('#trt-agreement-accepted', isInternational ? 'You must accept the participation agreement' : 'Katılım sözleşmesini kabul etmelisiniz');
                        isValid = false;
                    }
                    if (!$('#trt-data-protection-accepted').is(':checked')) {
                        showFieldError('#trt-data-protection-accepted', isInternational ? 'You must accept the GDPR text' : 'KVKK metnini kabul etmelisiniz');
                        isValid = false;
                    }
                    break;
            }

            return isValid;
        }

        function showFieldError(fieldSelector, message) {
            const field = $(fieldSelector);
            field.addClass('error');
            field.after('<span class="trt-error-text">' + message + '</span>');
        }

        function submitForm() {
            const isInternational = selectedCategory === 'international';
            
            // Form verilerini topla
            const formData = {
                category: selectedCategory,
                originalTitle: $('#trt-original-title').val(),
                turkishTitle: $('#trt-turkish-title').val(),
                originalLanguage: $('#trt-original-language').val(),
                productionCountry: $('#trt-production-country').val(),
                duration: $('#trt-duration').val(),
                synopsis: $('#trt-synopsis').val(),
                downloadLink: $('#trt-download-link').val(),
                downloadPassword: $('#trt-download-password').val(),
                directorName: $('#trt-director-name').val(),
                directorSurname: $('#trt-director-surname').val(),
                directorPhone: $('#trt-director-phone').val(),
                directorEmail: $('#trt-director-email').val(),
                directorAddress: $('#trt-director-address').val(),
                directorBio: $('#trt-director-bio').val(),
                directorFilmography: $('#trt-director-filmography').val(),
                agreementAccepted: $('#trt-agreement-accepted').is(':checked'),
                dataProtectionAccepted: $('#trt-data-protection-accepted').is(':checked')
            };

            // AJAX ile form gönder
            $.ajax({
                url: trt_yarisma_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'trt_yarisma_submit_form',
                    nonce: trt_yarisma_ajax.nonce,
                    form_data: formData
                },
                beforeSend: function() {
                    $('#trt-next-btn').prop('disabled', true).text(isInternational ? 'Submitting...' : 'Gönderiliyor...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(isInternational ? 'Your application has been submitted successfully!' : 'Başvurunuz başarıyla gönderildi!');
                        // Başlangıç ekranına dön
                        goBackToStart();
                    } else {
                        alert(isInternational ? 'An error occurred: ' + response.data : 'Bir hata oluştu: ' + response.data);
                    }
                },
                error: function() {
                    alert(isInternational ? 'A connection error occurred.' : 'Bağlantı hatası oluştu.');
                },
                complete: function() {
                    $('#trt-next-btn').prop('disabled', false).text(isInternational ? 'Complete and Save' : 'Tamamla ve Kaydet');
                }
            });
        }

    })(jQuery);

})();