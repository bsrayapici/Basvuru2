/**
 * TRT Öğrenci Yarışması Form JavaScript
 */

jQuery(document).ready(function($) {
    let currentStep = 1;
    const totalSteps = 4;
    
    // Form navigation
    $('.btn-next').on('click', function(e) {
        e.preventDefault();
        if (validateCurrentStep()) {
            nextStep();
        }
    });
    
    $('.btn-back').on('click', function(e) {
        e.preventDefault();
        prevStep();
    });
    
    // Step navigation
    function nextStep() {
        if (currentStep < totalSteps) {
            $('.form-step[data-step="' + currentStep + '"]').removeClass('active');
            $('.progress-step[data-step="' + currentStep + '"]').addClass('completed');
            
            currentStep++;
            
            $('.form-step[data-step="' + currentStep + '"]').addClass('active');
            $('.progress-step[data-step="' + currentStep + '"]').addClass('active');
            
            updateProgressBar();
            updateNavigationButtons();
            
            if (currentStep === 4) {
                generateSummary();
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            $('.form-step[data-step="' + currentStep + '"]').removeClass('active');
            $('.progress-step[data-step="' + currentStep + '"]').removeClass('active');
            
            currentStep--;
            
            $('.form-step[data-step="' + currentStep + '"]').addClass('active');
            $('.progress-step[data-step="' + currentStep + '"]').removeClass('completed');
            
            updateProgressBar();
            updateNavigationButtons();
        }
    }
    
    function updateProgressBar() {
        const progress = (currentStep / totalSteps) * 100;
        $('.progress-fill').css('width', progress + '%');
    }
    
    function updateNavigationButtons() {
        if (currentStep === 1) {
            $('.btn-back').hide();
        } else {
            $('.btn-back').show();
        }
        
        if (currentStep === totalSteps) {
            $('.btn-next').hide();
        } else {
            $('.btn-next').show();
        }
    }
    
    // Form validation
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = $('.form-step[data-step="' + currentStep + '"]');
        
        // Clear previous errors
        currentStepElement.find('.error').removeClass('error');
        currentStepElement.find('.error-message').remove();
        
        // Validate required fields
        currentStepElement.find('[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                showFieldError(field, trt_student_ajax.messages.required_field);
                isValid = false;
            } else {
                // Specific validations
                if (field.attr('type') === 'email' && !isValidEmail(value)) {
                    showFieldError(field, trt_student_ajax.messages.invalid_email);
                    isValid = false;
                }
                
                if (field.attr('type') === 'tel' && !isValidPhone(value)) {
                    showFieldError(field, trt_student_ajax.messages.invalid_phone);
                    isValid = false;
                }
                
                if (field.attr('name') === 'summary') {
                    if (value.length > 2500) {
                        showFieldError(field, 'Özet maksimum 2500 karakter olmalıdır.');
                        isValid = false;
                    }
                }
            }
        });
        
        // Step-specific validations
        if (currentStep === 2) {
            // Check if at least one director is added
            const directors = $('.dynamic-items[data-field="directors"] .dynamic-item');
            if (directors.length === 0) {
                showError('En az bir yönetmen bilgisi eklemelisiniz.');
                isValid = false;
            }
        }
        
        if (currentStep === 3) {
            // Check agreement checkboxes
            if (!$('input[name="agreement_accept"]').is(':checked')) {
                showError('Katılım sözleşmesini kabul etmelisiniz.');
                isValid = false;
            }
            
            if (!$('input[name="privacy_accept"]').is(':checked')) {
                showError('Kişisel verilerin korunması metnini kabul etmelisiniz.');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function showFieldError(field, message) {
        field.addClass('error');
        field.after('<span class="error-message">' + message + '</span>');
    }
    
    function showError(message) {
        // Show general error message
        if ($('.general-error').length === 0) {
            $('.form-step.active').prepend('<div class="general-error alert alert-danger">' + message + '</div>');
        }
        
        setTimeout(function() {
            $('.general-error').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }
    
    // Character counter for summary
    $('textarea[name="summary"]').on('input', function() {
        const currentLength = $(this).val().length;
        $('.current-count').text(currentLength);
        
        if (currentLength < 250) {
            $('.character-count').addClass('warning');
        } else if (currentLength > 1000) {
            $('.character-count').addClass('error');
        } else {
            $('.character-count').removeClass('warning error');
        }
    });
    
    // Dynamic field management
    $('.add-btn').on('click', function() {
        const fieldType = $(this).data('field');
        addDynamicField(fieldType);
    });
    
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.dynamic-item').remove();
    });
    
    function addDynamicField(fieldType) {
        const container = $('.dynamic-items[data-field="' + fieldType + '"]');
        let template = '';
        
        switch(fieldType) {
            case 'festivals':
            case 'awards':
            case 'social_media':
                template = `
                    <div class="dynamic-item">
                        <input type="text" name="${fieldType}[]" class="form-input" placeholder="Bilgi giriniz">
                        <button type="button" class="remove-btn">Kaldır</button>
                    </div>
                `;
                break;
                
            case 'directors':
            case 'producers':
            case 'writers':
            case 'sponsors':
            case 'sales_agent':
            case 'crew':
                template = `
                    <div class="dynamic-item person-item">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ad Soyad</label>
                                <input type="text" name="${fieldType}_name[]" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label>Soyad</label>
                                <input type="text" name="${fieldType}_surname[]" class="form-input">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Telefon</label>
                                <input type="tel" name="${fieldType}_phone[]" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>E-posta Adresi</label>
                                <input type="email" name="${fieldType}_email[]" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Adres</label>
                            <textarea name="${fieldType}_address[]" class="form-textarea" rows="2"></textarea>
                        </div>
                        <button type="button" class="remove-btn">Kaldır</button>
                    </div>
                `;
                break;
        }
        
        container.append(template);
    }
    
    // File upload handling
    $('.file-input').on('change', function() {
        const file = this.files[0];
        const uploadArea = $(this).siblings('.upload-area');
        
        if (file) {
            uploadArea.find('p').html(`Seçilen dosya: <strong>${file.name}</strong>`);
            uploadArea.addClass('file-selected');
        }
    });
    
    // Drag and drop for file upload
    $('.upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });
    
    $('.upload-area').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
    });
    
    $('.upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const fileInput = $(this).siblings('.file-input')[0];
            fileInput.files = files;
            $(fileInput).trigger('change');
        }
    });
    
    $('.upload-link').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.file-upload').find('.file-input').click();
    });
    
    // Generate summary for step 4
    function generateSummary() {
        // Film bilgileri özeti
        const filmSummary = $('#film-summary');
        filmSummary.empty();
        
        const filmData = {
            'Kategori': $('select[name="category"]').val(),
            'Filmin Özgün Adı': $('input[name="original_title"]').val(),
            'Filmin Türkçe Adı': $('input[name="turkish_title"]').val(),
            'Özgün Dili': $('select[name="original_language"] option:selected').text(),
            'Yapımcı Ülke': $('select[name="production_country"] option:selected').text(),
            'Süresi': $('input[name="duration"]').val() + ' Dakika',
            'Ses Bilgisi': $('input[name="audio_info"]').val(),
            'Müzik/Özgün Müzik Bilgisi': $('input[name="music_info"]').val(),
            'Yapım Formatı Ekran Oranı': $('select[name="aspect_ratio"] option:selected').text(),
            'Yapım Tarihi': $('input[name="production_date"]').val(),
            'Filmin Kısa Özeti': $('textarea[name="summary"]').val(),
            'İndirilebilir Film Linki': $('input[name="download_link"]').val(),
            'İndirilebilir Link Şifresi': $('input[name="download_password"]').val() || 'Şifre yok'
        };
        
        for (const [key, value] of Object.entries(filmData)) {
            if (value && value !== 'Seçiniz' && value !== '') {
                filmSummary.append(`<p><strong>${key}:</strong> ${value}</p>`);
            }
        }
        
        // Yönetmen bilgileri özeti
        const directorSummary = $('#director-summary');
        directorSummary.empty();
        
        const applicantData = {
            'Ad': $('input[name="applicant_name"]').val(),
            'Soyad': $('input[name="applicant_surname"]').val(),
            'Telefon': $('input[name="phone"]').val(),
            'E-posta Adresi': $('input[name="email"]').val(),
            'Adres': $('textarea[name="address"]').val(),
            'Özgeçmiş': $('textarea[name="biography"]').val(),
            'Filmografi': $('textarea[name="filmography"]').val()
        };
        
        for (const [key, value] of Object.entries(applicantData)) {
            if (value && value !== '') {
                directorSummary.append(`<p><strong>${key}:</strong> ${value}</p>`);
            }
        }
        
        // Öğrenci belgesi
        const studentDoc = $('input[name="student_document"]')[0];
        if (studentDoc && studentDoc.files.length > 0) {
            directorSummary.append(`<p><strong>Öğrenci Belgesi:</strong> ${studentDoc.files[0].name}</p>`);
        }
    }
    
    // Form submission
    $('#trt-student-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        const formData = new FormData(this);
        formData.append('action', 'trt_student_submit');
        formData.append('nonce', trt_student_ajax.nonce);
        
        // Show loading
        $('.btn-submit').prop('disabled', true).text('Gönderiliyor...');
        
        $.ajax({
            url: trt_student_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccess(trt_student_ajax.messages.success);
                    // Redirect or reset form
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    showError(response.data || trt_student_ajax.messages.error);
                }
            },
            error: function() {
                showError(trt_student_ajax.messages.error);
            },
            complete: function() {
                $('.btn-submit').prop('disabled', false).text('Başvuruyu Tamamla');
            }
        });
    });
    
    function showSuccess(message) {
        $('.form-step.active').prepend('<div class="alert alert-success">' + message + '</div>');
        $('html, body').animate({ scrollTop: 0 }, 500);
    }
});

