/**
 * TRT Profesyonel Yarışması Form JavaScript
 */

jQuery(document).ready(function($) {
    let currentStep = 1;
    const totalSteps = 4;
    
    // Initialize form
    initializeForm();
    
    function initializeForm() {
        // Character counter for summary
        $('#summary').on('input', function() {
            const length = $(this).val().length;
            $('#summary-count').text(length);
            
            if (length < 250) {
                $(this).addClass('invalid');
                $('#summary-count').addClass('invalid');
            } else {
                $(this).removeClass('invalid');
                $('#summary-count').removeClass('invalid');
            }
        });
        
        // Phone number formatting
        $('#phone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.startsWith('90')) {
                value = value.substring(2);
            }
            
            if (value.length > 0) {
                value = '+90 (' + value.substring(0, 3) + ') ' + 
                       value.substring(3, 6) + ' ' + 
                       value.substring(6, 8) + ' ' + 
                       value.substring(8, 10);
            }
            
            $(this).val(value);
        });
        
        // Dynamic section handlers
        $('.add-btn').on('click', function() {
            const target = $(this).data('target');
            addDynamicField(target);
        });
        
        // Navigation handlers
        $('.btn-next').on('click', function() {
            if (validateCurrentStep()) {
                nextStep();
            }
        });
        
        $('.btn-prev').on('click', function() {
            prevStep();
        });
        
        // Form submission
        $('#trt-professional-form').on('submit', function(e) {
            e.preventDefault();
            if (validateAllSteps()) {
                submitForm();
            }
        });
        
        // Add initial director field
        addDynamicField('directors');
    }
    
    function addDynamicField(type) {
        const container = $('#' + type + '-container');
        const index = container.children().length;
        let html = '';
        
        switch(type) {
            case 'festivals':
            case 'awards':
                html = `
                    <div class="dynamic-item" data-index="${index}">
                        <div class="form-group">
                            <input type="text" name="${type}[]" placeholder="${type === 'festivals' ? 'Festival Adı' : 'Ödül Adı'}" />
                            <button type="button" class="remove-btn">Kaldır</button>
                        </div>
                    </div>
                `;
                break;
                
            case 'social_media':
                html = `
                    <div class="dynamic-item" data-index="${index}">
                        <div class="form-group">
                            <input type="url" name="${type}[]" placeholder="Sosyal Medya Linki" />
                            <button type="button" class="remove-btn">Kaldır</button>
                        </div>
                    </div>
                `;
                break;
                
            case 'imdb':
                html = `
                    <div class="dynamic-item" data-index="${index}">
                        <div class="form-group">
                            <input type="url" name="${type}_link" placeholder="IMDB Linki" />
                            <button type="button" class="remove-btn">Kaldır</button>
                        </div>
                    </div>
                `;
                break;
                
            case 'directors':
            case 'producers':
            case 'writers':
            case 'sponsors':
            case 'sales_agent':
            case 'crew':
                const isRequired = type === 'directors' ? ' required' : '';
                const requiredMark = type === 'directors' ? ' <span class="required">*</span>' : '';
                
                html = `
                    <div class="dynamic-item person-item" data-index="${index}">
                        <div class="person-header">
                            <h4>${getPersonTitle(type)} ${index + 1}</h4>
                            ${type !== 'directors' || index > 0 ? '<button type="button" class="remove-btn">Kaldır</button>' : ''}
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ad${requiredMark}</label>
                                <input type="text" name="${type}_name[]"${isRequired} />
                            </div>
                            <div class="form-group">
                                <label>Soyad</label>
                                <input type="text" name="${type}_surname[]" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Telefon</label>
                                <input type="tel" name="${type}_phone[]" placeholder="+90 (_ _ _) _ _ _ _ _ _ _" />
                            </div>
                            <div class="form-group">
                                <label>E-posta Adresi</label>
                                <input type="email" name="${type}_email[]" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Adres</label>
                            <textarea name="${type}_address[]" rows="2"></textarea>
                        </div>
                    </div>
                `;
                break;
        }
        
        container.append(html);
        
        // Add remove handler
        container.find('.remove-btn').last().on('click', function() {
            $(this).closest('.dynamic-item').remove();
            updatePersonNumbers(type);
        });
        
        // Add phone formatting for person fields
        container.find('input[type="tel"]').last().on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.startsWith('90')) {
                value = value.substring(2);
            }
            
            if (value.length > 0) {
                value = '+90 (' + value.substring(0, 3) + ') ' + 
                       value.substring(3, 6) + ' ' + 
                       value.substring(6, 8) + ' ' + 
                       value.substring(8, 10);
            }
            
            $(this).val(value);
        });
    }
    
    function getPersonTitle(type) {
        const titles = {
            'directors': 'Yönetmen',
            'producers': 'Yapımcı',
            'writers': 'Metin Yazarı',
            'sponsors': 'Destekçi Kurum',
            'sales_agent': 'Satış Yetkilisi',
            'crew': 'Teknik Ekip'
        };
        return titles[type] || type;
    }
    
    function updatePersonNumbers(type) {
        $(`#${type}-container .person-item`).each(function(index) {
            $(this).find('.person-header h4').text(getPersonTitle(type) + ' ' + (index + 1));
            $(this).attr('data-index', index);
        });
    }
    
    function validateCurrentStep() {
        const step = $('.form-step[data-step="' + currentStep + '"]');
        let isValid = true;
        
        // Clear previous errors
        step.find('.error').removeClass('error');
        step.find('.error-message').remove();
        
        // Validate required fields
        step.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('error');
                isValid = false;
            }
        });
        
        // Step-specific validations
        if (currentStep === 1) {
            // Summary length validation
            const summary = $('#summary').val();
            if (summary.length > 2500) {
                $('#summary').addClass('error');
                isValid = false;
            }
            
            // URL validation
            const downloadLink = $('#download_link').val();
            if (downloadLink && !isValidUrl(downloadLink)) {
                $('#download_link').addClass('error');
                isValid = false;
            }
        }
        
        if (currentStep === 2) {
            // At least one director required
            const directors = $('#directors-container .person-item');
            if (directors.length === 0) {
                showError('En az bir yönetmen bilgisi eklemelisiniz.');
                isValid = false;
            } else {
                // Validate director names
                let hasValidDirector = false;
                directors.each(function() {
                    const name = $(this).find('input[name="directors_name[]"]').val();
                    if (name && name.trim()) {
                        hasValidDirector = true;
                    }
                });
                
                if (!hasValidDirector) {
                    showError('En az bir yönetmen adı girmelisiniz.');
                    isValid = false;
                }
            }
            
            // Email validation
            const email = $('#email').val();
            if (email && !isValidEmail(email)) {
                $('#email').addClass('error');
                isValid = false;
            }
        }
        
        if (currentStep === 3) {
            // Agreement checkboxes
            if (!$('#agreement_accept').is(':checked')) {
                $('#agreement_accept').closest('.checkbox-group').addClass('error');
                isValid = false;
            }
            
            if (!$('#privacy_accept').is(':checked')) {
                $('#privacy_accept').closest('.checkbox-group').addClass('error');
                isValid = false;
            }
        }
        
        if (!isValid) {
            showError('Lütfen tüm zorunlu alanları doldurunuz.');
        }
        
        return isValid;
    }
    
    function validateAllSteps() {
        for (let i = 1; i <= totalSteps; i++) {
            currentStep = i;
            if (!validateCurrentStep()) {
                currentStep = i;
                showStep(i);
                return false;
            }
        }
        return true;
    }
    
    function nextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            
            if (currentStep === 4) {
                generateSummary();
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }
    
    function showStep(step) {
        // Hide all steps
        $('.form-step').removeClass('active');
        $('.progress-step').removeClass('active completed');
        
        // Show current step
        $('.form-step[data-step="' + step + '"]').addClass('active');
        
        // Update progress bar
        for (let i = 1; i <= step; i++) {
            if (i < step) {
                $('.progress-step[data-step="' + i + '"]').addClass('completed');
            } else if (i === step) {
                $('.progress-step[data-step="' + i + '"]').addClass('active');
            }
        }
        
        // Scroll to top
        $('html, body').animate({
            scrollTop: $('.trt-yarisma-container').offset().top - 50
        }, 300);
    }
    
    function generateSummary() {
        // Film bilgileri özeti
        const filmInfo = {
            'Kategori': $('#category').val(),
            'Film Adı': $('#original_title').val(),
            'Türkçe Adı': $('#turkish_title').val() || '-',
            'Özgün Dili': $('#original_language').val(),
            'Yapımcı Ülke': $('#production_country').val(),
            'Süre': $('#duration').val() + ' dakika',
            'Ses Bilgisi': $('#audio_info').val() || '-',
            'Müzik Bilgisi': $('#music_info').val() || '-',
            'Ekran Oranı': $('#aspect_ratio').val() || '-',
            'Yapım Tarihi': $('#production_date').val() || '-',
            'Film Özeti': $('#summary').val(),
            'İndirme Linki': $('#download_link').val(),
            'Link Şifresi': $('#download_password').val() || '-'
        };
        
        let filmHtml = '';
        for (const [key, value] of Object.entries(filmInfo)) {
            if (value && value !== '-') {
                filmHtml += `<div class="summary-item"><strong>${key}:</strong> ${value}</div>`;
            }
        }
        
        $('#summary-film-info').html(filmHtml);
        
        // Başvuran bilgileri özeti
        const applicantInfo = {
            'Ad Soyad': $('#applicant_name').val() + ' ' + $('#applicant_surname').val(),
            'Telefon': $('#phone').val(),
            'E-posta': $('#email').val(),
            'Adres': $('#address').val(),
            'Özgeçmiş': $('#biography').val() || '-',
            'Filmografi': $('#filmography').val() || '-'
        };
        
        let applicantHtml = '';
        for (const [key, value] of Object.entries(applicantInfo)) {
            if (value && value !== '-') {
                applicantHtml += `<div class="summary-item"><strong>${key}:</strong> ${value}</div>`;
            }
        }
        
        // Yönetmen bilgileri
        const directors = [];
        $('#directors-container .person-item').each(function() {
            const name = $(this).find('input[name="directors_name[]"]').val();
            const surname = $(this).find('input[name="directors_surname[]"]').val();
            if (name) {
                directors.push(name + (surname ? ' ' + surname : ''));
            }
        });
        
        if (directors.length > 0) {
            applicantHtml += `<div class="summary-item"><strong>Yönetmen(ler):</strong> ${directors.join(', ')}</div>`;
        }
        
        $('#summary-applicant-info').html(applicantHtml);
    }
    
    function submitForm() {
        showLoading(true);
        
        const formData = new FormData($('#trt-professional-form')[0]);
        formData.append('action', 'trt_professional_submit');
        
        $.ajax({
            url: trt_professional_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showLoading(false);
                
                if (response.success) {
                    showMessage('success', 'Başvuru Başarılı!', response.data);
                    $('#trt-professional-form')[0].reset();
                    currentStep = 1;
                    showStep(1);
                } else {
                    showMessage('error', 'Hata!', response.data || 'Bir hata oluştu.');
                }
            },
            error: function() {
                showLoading(false);
                showMessage('error', 'Hata!', 'Sunucu hatası oluştu. Lütfen tekrar deneyiniz.');
            }
        });
    }
    
    function showLoading(show) {
        if (show) {
            $('#trt-loading').fadeIn();
        } else {
            $('#trt-loading').fadeOut();
        }
    }
    
    function showMessage(type, title, message) {
        const messageEl = $('#trt-message');
        const iconEl = messageEl.find('.message-icon');
        
        messageEl.find('.message-title').text(title);
        messageEl.find('.message-text').text(message);
        
        iconEl.removeClass('success error').addClass(type);
        
        messageEl.fadeIn();
        
        messageEl.find('.message-close').off('click').on('click', function() {
            messageEl.fadeOut();
        });
    }
    
    function showError(message) {
        showMessage('error', 'Hata!', message);
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
});

