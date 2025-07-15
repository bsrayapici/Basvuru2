<?php
/**
 * International Competition Shortcode sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Shortcodes_International {
    
    public function __construct() {
        add_shortcode('trt_international_form', array($this, 'render_form'));
    }
    
    /**
     * International Competition form shortcode'u
     */
    public function render_form($atts) {
        $atts = shortcode_atts(array(
            'kategori' => 'international-competition'
        ), $atts);
        
        ob_start();
        
        // CSS ve JS dosyalarını yükle
        wp_enqueue_style('trt-yarisma-frontend');
        wp_enqueue_script('trt-yarisma-international');
        
        ?>
        <div class="trt-yarisma-app">
            <div class="trt-application-container">
                <!-- Header -->
                <header class="trt-app-header">
                    <div class="trt-header-content">
                        <img src="<?php echo TRT_YARISMA_PLUGIN_URL . 'assets/images/trt-logo.png'; ?>" 
                             alt="TRT Logo" 
                             class="trt-logo">
                        <div class="trt-header-text">
                            <h1>TRT International Documentary Competition</h1>
                            <p>Application System</p>
                        </div>
                    </div>
                </header>

                <!-- Form Container -->
                <div class="trt-form-container">
                    <button class="trt-back-button" onclick="window.location.reload();">
                        ← Back to Start Screen
                    </button>
                    
                    <!-- Progress Bar -->
                    <div class="trt-progress-container">
                        <div class="trt-progress-bar">
                            <div class="trt-progress-step trt-active">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">✓</span>
                                </div>
                                <div class="trt-step-label">Start Application</div>
                            </div>
                            <div class="trt-progress-step trt-active">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">1</span>
                                </div>
                                <div class="trt-step-label">Work Link and Details</div>
                            </div>
                            <div class="trt-progress-step">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">2</span>
                                </div>
                                <div class="trt-step-label">Director Information</div>
                            </div>
                            <div class="trt-progress-step">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">3</span>
                                </div>
                                <div class="trt-step-label">Participation Agreement</div>
                            </div>
                            <div class="trt-progress-step">
                                <div class="trt-step-circle">
                                    <span class="trt-step-number">4</span>
                                </div>
                                <div class="trt-step-label">Application Summary and Confirmation</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="trt-form-content">
                        <div class="trt-form-step" id="trt-step-1">
                            <h2>Work Link and Details</h2>
                            
                            <div class="trt-form-section">
                                <h3>Film Information (Required)</h3>
                                
                                <div class="trt-form-group">
                                    <label>Category</label>
                                    <select name="category" id="trt-category">
                                        <option value="International Competition">International Competition</option>
                                    </select>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Original Title *</label>
                                        <input type="text" name="original_title" id="trt-original-title" required>
                                        <span class="trt-error-text" id="trt-original-title-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Original Title in English</label>
                                        <input type="text" name="original_title_english" id="trt-original-title-english">
                                    </div>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Original Language *</label>
                                        <select name="original_language" id="trt-original-language" required>
                                            <option value="">Select</option>
                                            <option value="english">English</option>
                                            <option value="turkish">Turkish</option>
                                            <option value="french">French</option>
                                            <option value="german">German</option>
                                            <option value="spanish">Spanish</option>
                                            <option value="arabic">Arabic</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <span class="trt-error-text" id="trt-original-language-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Production Country *</label>
                                        <select name="production_country" id="trt-production-country" required>
                                            <option value="">Select</option>
                                            <option value="turkey">Turkey</option>
                                            <option value="usa">USA</option>
                                            <option value="uk">United Kingdom</option>
                                            <option value="france">France</option>
                                            <option value="germany">Germany</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <span class="trt-error-text" id="trt-production-country-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Duration *</label>
                                        <input type="text" name="duration" id="trt-duration" placeholder="Minutes" required>
                                        <span class="trt-error-text" id="trt-duration-error"></span>
                                    </div>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Audio Information</label>
                                        <input type="text" name="audio_info" id="trt-audio-info">
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Music/Original Music Information</label>
                                        <input type="text" name="music_info" id="trt-music-info">
                                    </div>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Aspect Ratio</label>
                                        <select name="aspect_ratio" id="trt-aspect-ratio">
                                            <option value="">Select</option>
                                            <option value="16:9">16:9</option>
                                            <option value="4:3">4:3</option>
                                            <option value="21:9">21:9</option>
                                        </select>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Production Date</label>
                                        <input type="date" name="production_date" id="trt-production-date">
                                    </div>
                                </div>

                                <div class="trt-form-group">
                                    <label>Film Summary (Maximum 2500 Characters) *</label>
                                    <textarea name="summary" id="trt-summary" rows="5" maxlength="2500" required></textarea>
                                    <div class="trt-character-count">
                                        <span id="trt-summary-count">0</span>/1000 characters
                                    </div>
                                    <span class="trt-error-text" id="trt-summary-error"></span>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Downloadable Film Link *</label>
                                        <input type="url" name="download_link" id="trt-download-link" required>
                                        <span class="trt-error-text" id="trt-download-link-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Downloadable Link Password</label>
                                        <input type="text" name="download_password" id="trt-download-password">
                                    </div>
                                </div>

                                <div class="trt-info-box">
                                    <p>• Accepted formats: mpeg2, mov, mxf, mp4</p>
                                    <p>• Make sure your link is downloadable (YouTube links will not be accepted)</p>
                                </div>
                            </div>

                            <!-- Dynamic Sections -->
                            <div class="trt-dynamic-sections">
                                <div class="trt-dynamic-section">
                                    <h4>
                                        Participated Festivals (Optional)
                                        <button type="button" class="trt-add-button" onclick="trtAddDynamicItem('festivals')">Add</button>
                                    </h4>
                                    <div id="trt-festivals-container" class="trt-dynamic-container">
                                        <div class="trt-dynamic-empty">No festival added yet</div>
                                    </div>
                                </div>

                                <div class="trt-dynamic-section">
                                    <h4>
                                        Awards Received (Optional)
                                        <button type="button" class="trt-add-button" onclick="trtAddDynamicItem('prizes')">Add</button>
                                    </h4>
                                    <div id="trt-prizes-container" class="trt-dynamic-container">
                                        <div class="trt-dynamic-empty">No award added yet</div>
                                    </div>
                                </div>

                                <div class="trt-dynamic-section">
                                    <h4>
                                        Social Media Accounts (Optional)
                                        <button type="button" class="trt-add-button" onclick="trtAddDynamicItem('socialMedia')">Add</button>
                                    </h4>
                                    <div id="trt-socialMedia-container" class="trt-dynamic-container">
                                        <div class="trt-dynamic-empty">No social media account added yet</div>
                                    </div>
                                </div>

                                <div class="trt-dynamic-section">
                                    <h4>
                                        IMDB Link (Optional)
                                        <button type="button" class="trt-add-button" onclick="trtAddDynamicItem('imdbLink')">Add</button>
                                    </h4>
                                    <div id="trt-imdbLink-container" class="trt-dynamic-container">
                                        <div class="trt-dynamic-empty">IMDB link not added</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Director Information -->
                        <div class="trt-form-step" id="trt-step-2" style="display: none;">
                            <h2>Director Information</h2>
                            
                            <div class="trt-form-section">
                                <h3>Director Details (Required)</h3>
                                
                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Name *</label>
                                        <input type="text" name="director_name" id="trt-director-name" required>
                                        <span class="trt-error-text" id="trt-director-name-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Surname *</label>
                                        <input type="text" name="director_surname" id="trt-director-surname" required>
                                        <span class="trt-error-text" id="trt-director-surname-error"></span>
                                    </div>
                                </div>

                                <div class="trt-form-row">
                                    <div class="trt-form-group">
                                        <label>Phone *</label>
                                        <input type="tel" name="director_phone" id="trt-director-phone" required>
                                        <span class="trt-error-text" id="trt-director-phone-error"></span>
                                    </div>
                                    <div class="trt-form-group">
                                        <label>Email Address *</label>
                                        <input type="email" name="director_email" id="trt-director-email" required>
                                        <span class="trt-error-text" id="trt-director-email-error"></span>
                                    </div>
                                </div>

                                <div class="trt-form-group">
                                    <label>Address *</label>
                                    <textarea name="director_address" id="trt-director-address" rows="3" required></textarea>
                                    <span class="trt-error-text" id="trt-director-address-error"></span>
                                </div>

                                <div class="trt-form-group">
                                    <label>Biography (Maximum 2500 Characters) *</label>
                                    <textarea name="director_bio" id="trt-director-bio" rows="5" maxlength="2500" required></textarea>
                                    <div class="trt-character-count">
                                        <span id="trt-director-bio-count">0</span>/1000 characters
                                    </div>
                                    <span class="trt-error-text" id="trt-director-bio-error"></span>
                                </div>

                                <div class="trt-form-group">
                                    <label>Filmography</label>
                                    <textarea name="director_filmography" id="trt-director-filmography" rows="4"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Participation Agreement -->
                        <div class="trt-form-step" id="trt-step-3" style="display: none;">
                            <h2>Participation Agreement</h2>
                            
                            <div class="trt-form-section">
                                <h3>Agreements (Required)</h3>
                                
                                <div class="trt-agreement-section">
                                    <div class="trt-agreement-text">
                                        <h4>Participation Agreement</h4>
                                        <p>By participating in this competition, I agree to all terms and conditions...</p>
                                    </div>
                                    <div class="trt-form-group">
                                        <label class="trt-checkbox-label">
                                            <input type="checkbox" name="agreement_accepted" id="trt-agreement-accepted" required>
                                            <span class="trt-checkbox-custom"></span>
                                            I accept the Participation Agreement *
                                        </label>
                                        <span class="trt-error-text" id="trt-agreement-accepted-error"></span>
                                    </div>
                                </div>

                                <div class="trt-agreement-section">
                                    <div class="trt-agreement-text">
                                        <h4>Personal Data Protection</h4>
                                        <p>I consent to the processing of my personal data...</p>
                                    </div>
                                    <div class="trt-form-group">
                                        <label class="trt-checkbox-label">
                                            <input type="checkbox" name="privacy_accepted" id="trt-privacy-accepted" required>
                                            <span class="trt-checkbox-custom"></span>
                                            I accept the Personal Data Protection Text *
                                        </label>
                                        <span class="trt-error-text" id="trt-privacy-accepted-error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Application Summary -->
                        <div class="trt-form-step" id="trt-step-4" style="display: none;">
                            <h2>Application Summary and Confirmation</h2>
                            
                            <div class="trt-form-section">
                                <h3>Application Summary</h3>
                                
                                <div id="trt-summary-content">
                                    <!-- Summary will be populated by JavaScript -->
                                </div>
                                
                                <div class="trt-confirmation-text">
                                    <p>Please review your application carefully. Once submitted, you will not be able to make changes.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div class="trt-form-navigation">
                        <button type="button" class="trt-btn-secondary" id="trt-prev-btn" onclick="trtPrevStep()" style="display: none;">
                            Back
                        </button>
                        
                        <button type="button" class="trt-btn-primary" id="trt-next-btn" onclick="trtNextStep()">
                            Next
                        </button>
                        
                        <button type="button" class="trt-btn-primary" id="trt-submit-btn" onclick="trtSubmitForm()" style="display: none;">
                            Submit & Complete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
}