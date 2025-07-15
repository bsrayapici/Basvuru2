@@ .. @@
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
-                            <button type="button" class="trt-start-application-btn" id="trt-start-btn">
+                            <button type="button" class="trt-start-application-btn" id="trt-start-btn" disabled>
                                 Başvuruya Başla
                             </button>
                         </div>