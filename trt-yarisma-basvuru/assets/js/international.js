/**
 * TRT International Competition Form JavaScript
 */

let trtCurrentStep = 1;
let trtDynamicSections = {
    festivals: [],
    prizes: [],
    socialMedia: [],
    imdbLink: []
};

// Initialize form when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    trtInitializeForm();
});

function trtInitializeForm() {
    // Character count for textareas
    const summaryTextarea = document.getElementById('trt-summary');
    const bioTextarea = document.getElementById('trt-director-bio');
    
    if (summaryTextarea) {
        summaryTextarea.addEventListener('input', function() {
            document.getElementById('trt-summary-count').textContent = this.value.length;
        });
    }
    
    if (bioTextarea) {
        bioTextarea.addEventListener('input', function() {
            document.getElementById('trt-director-bio-count').textContent = this.value.length;
        });
    }
    
    // Update progress bar
    trtUpdateProgressBar();
}

function trtUpdateProgressBar() {
    const steps = document.querySelectorAll('.trt-progress-step');
    
    steps.forEach((step, index) => {
        if (index < trtCurrentStep) {
            step.classList.add('trt-active');
        } else {
            step.classList.remove('trt-active');
        }
    });
}

function trtValidateStep(step) {
    const errors = {};
    
    if (step === 1) {
        const originalTitle = document.getElementById('trt-original-title').value.trim();
        const originalLanguage = document.getElementById('trt-original-language').value;
        const productionCountry = document.getElementById('trt-production-country').value;
        const duration = document.getElementById('trt-duration').value.trim();
        const summary = document.getElementById('trt-summary').value.trim();
        const downloadLink = document.getElementById('trt-download-link').value.trim();
        
        if (!originalTitle) errors.originalTitle = 'Original Title is required';
        if (!originalLanguage) errors.originalLanguage = 'Original Language is required';
        if (!productionCountry) errors.productionCountry = 'Production Country is required';
        if (!duration) errors.duration = 'Duration is required';
        if (!summary) errors.summary = 'Film Summary is required';
        if (summary.length > 2500) {
            errors.summary = 'Film Summary must be maximum 2500 characters';
        }
        if (!downloadLink) errors.downloadLink = 'Downloadable Film Link is required';
    }
    
    if (step === 2) {
        const directorName = document.getElementById('trt-director-name').value.trim();
        const directorSurname = document.getElementById('trt-director-surname').value.trim();
        const directorPhone = document.getElementById('trt-director-phone').value.trim();
        const directorEmail = document.getElementById('trt-director-email').value.trim();
        const directorAddress = document.getElementById('trt-director-address').value.trim();
        const directorBio = document.getElementById('trt-director-bio').value.trim();
        
        if (!directorName) errors.directorName = 'Director Name is required';
        if (!directorSurname) errors.directorSurname = 'Director Surname is required';
        if (!directorPhone) errors.directorPhone = 'Phone is required';
        if (!directorEmail) errors.directorEmail = 'Email Address is required';
        if (!directorAddress) errors.directorAddress = 'Address is required';
        if (!directorBio) errors.directorBio = 'Biography is required';
        if (directorBio.length > 2500) {
            errors.directorBio = 'Biography must be maximum 2500 characters';
        }
    }
    
    if (step === 3) {
        const agreementAccepted = document.getElementById('trt-agreement-accepted').checked;
        const privacyAccepted = document.getElementById('trt-privacy-accepted').checked;
        
        if (!agreementAccepted) errors.agreementAccepted = 'You must accept the Participation Agreement';
        if (!privacyAccepted) errors.privacyAccepted = 'You must accept the Personal Data Protection Text';
    }
    
    // Clear previous errors
    document.querySelectorAll('.trt-error-text').forEach(el => el.textContent = '');
    
    // Show new errors
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById('trt-' + field.replace(/([A-Z])/g, '-$1').toLowerCase() + '-error');
        if (errorElement) {
            errorElement.textContent = errors[field];
        }
    });
    
    return Object.keys(errors).length === 0;
}

function trtNextStep() {
    if (trtValidateStep(trtCurrentStep)) {
        if (trtCurrentStep < 4) {
            // Hide current step
            document.getElementById('trt-step-' + trtCurrentStep).style.display = 'none';
            
            // Move to next step
            trtCurrentStep++;
            
            // Show next step
            document.getElementById('trt-step-' + trtCurrentStep).style.display = 'block';
            
            // Update navigation buttons
            trtUpdateNavigation();
            
            // Update progress bar
            trtUpdateProgressBar();
            
            // Generate summary if on step 4
            if (trtCurrentStep === 4) {
                trtGenerateSummary();
            }
            
            // Scroll to top
            window.scrollTo(0, 0);
        }
    }
}

function trtPrevStep() {
    if (trtCurrentStep > 1) {
        // Hide current step
        document.getElementById('trt-step-' + trtCurrentStep).style.display = 'none';
        
        // Move to previous step
        trtCurrentStep--;
        
        // Show previous step
        document.getElementById('trt-step-' + trtCurrentStep).style.display = 'block';
        
        // Update navigation buttons
        trtUpdateNavigation();
        
        // Update progress bar
        trtUpdateProgressBar();
        
        // Scroll to top
        window.scrollTo(0, 0);
    }
}

function trtUpdateNavigation() {
    const prevBtn = document.getElementById('trt-prev-btn');
    const nextBtn = document.getElementById('trt-next-btn');
    const submitBtn = document.getElementById('trt-submit-btn');
    
    // Show/hide previous button
    if (trtCurrentStep > 1) {
        prevBtn.style.display = 'inline-block';
    } else {
        prevBtn.style.display = 'none';
    }
    
    // Show/hide next/submit buttons
    if (trtCurrentStep < 4) {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    } else {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    }
}

function trtGenerateSummary() {
    const summaryContent = document.getElementById('trt-summary-content');
    
    const originalTitle = document.getElementById('trt-original-title').value;
    const originalLanguage = document.getElementById('trt-original-language').value;
    const productionCountry = document.getElementById('trt-production-country').value;
    const duration = document.getElementById('trt-duration').value;
    const directorName = document.getElementById('trt-director-name').value;
    const directorSurname = document.getElementById('trt-director-surname').value;
    const directorEmail = document.getElementById('trt-director-email').value;
    
    summaryContent.innerHTML = `
        <div class="trt-summary-item">
            <strong>Original Title:</strong> ${originalTitle}
        </div>
        <div class="trt-summary-item">
            <strong>Original Language:</strong> ${originalLanguage}
        </div>
        <div class="trt-summary-item">
            <strong>Production Country:</strong> ${productionCountry}
        </div>
        <div class="trt-summary-item">
            <strong>Duration:</strong> ${duration} minutes
        </div>
        <div class="trt-summary-item">
            <strong>Director:</strong> ${directorName} ${directorSurname}
        </div>
        <div class="trt-summary-item">
            <strong>Email:</strong> ${directorEmail}
        </div>
    `;
}

function trtAddDynamicItem(section) {
    const container = document.getElementById('trt-' + section + '-container');
    const emptyDiv = container.querySelector('.trt-dynamic-empty');
    
    // Remove empty message if exists
    if (emptyDiv) {
        emptyDiv.remove();
    }
    
    // Create new item
    const itemId = Date.now();
    const itemDiv = document.createElement('div');
    itemDiv.className = 'trt-dynamic-item';
    itemDiv.setAttribute('data-id', itemId);
    
    let placeholder = '';
    let inputType = 'text';
    
    switch(section) {
        case 'festivals':
            placeholder = 'Festival name';
            break;
        case 'prizes':
            placeholder = 'Award name';
            break;
        case 'socialMedia':
            placeholder = 'Social media link';
            inputType = 'url';
            break;
        case 'imdbLink':
            placeholder = 'IMDB link';
            inputType = 'url';
            break;
    }
    
    itemDiv.innerHTML = `
        <input type="${inputType}" placeholder="${placeholder}" name="${section}[]">
        <button type="button" class="trt-remove-button" onclick="trtRemoveDynamicItem('${section}', ${itemId})">×</button>
    `;
    
    container.appendChild(itemDiv);
}

function trtRemoveDynamicItem(section, itemId) {
    const container = document.getElementById('trt-' + section + '-container');
    const item = container.querySelector(`[data-id="${itemId}"]`);
    
    if (item) {
        item.remove();
        
        // Add empty message if no items left
        const remainingItems = container.querySelectorAll('.trt-dynamic-item');
        if (remainingItems.length === 0) {
            let emptyText = '';
            switch(section) {
                case 'festivals':
                    emptyText = 'No festival added yet';
                    break;
                case 'prizes':
                    emptyText = 'No award added yet';
                    break;
                case 'socialMedia':
                    emptyText = 'No social media account added yet';
                    break;
                case 'imdbLink':
                    emptyText = 'IMDB link not added';
                    break;
            }
            
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'trt-dynamic-empty';
            emptyDiv.textContent = emptyText;
            container.appendChild(emptyDiv);
        }
    }
}

function trtSubmitForm() {
    if (trtValidateStep(trtCurrentStep)) {
        // Collect form data
        const formData = new FormData();
        
        // Add all form fields
        const inputs = document.querySelectorAll('#trt-step-1 input, #trt-step-1 select, #trt-step-1 textarea, #trt-step-2 input, #trt-step-2 textarea, #trt-step-3 input');
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                formData.append(input.name, input.checked ? '1' : '0');
            } else {
                formData.append(input.name, input.value);
            }
        });
        
        // Add dynamic sections
        Object.keys(trtDynamicSections).forEach(section => {
            const items = document.querySelectorAll(`#trt-${section}-container input`);
            items.forEach(item => {
                if (item.value.trim()) {
                    formData.append(`${section}[]`, item.value);
                }
            });
        });
        
        formData.append('action', 'trt_submit_international_application');
        formData.append('nonce', trt_ajax.nonce);
        
        // Submit via AJAX
        fetch(trt_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Application submitted successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the application.');
        });
    }
}

