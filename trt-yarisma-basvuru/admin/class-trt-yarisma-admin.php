<?php
/**
 * Admin panel sınıfı - Güncellenmiş
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_post_trt_yarisma_export', array($this, 'export_applications'));
        add_action('wp_ajax_trt_yarisma_update_status', array($this, 'update_application_status'));
    }
    
    /**
     * Admin menüsünü ekle
     */
    public function add_admin_menu() {
        add_menu_page(
            'TRT Yarışma Başvuruları',
            'TRT Yarışma',
            'manage_options',
            'trt-yarisma',
            array($this, 'admin_page'),
            'dashicons-awards',
            30
        );
        
        add_submenu_page(
            'trt-yarisma',
            'Başvurular',
            'Başvurular',
            'manage_options',
            'trt-yarisma',
            array($this, 'admin_page')
        );
        
        add_submenu_page(
            'trt-yarisma',
            'Ayarlar',
            'Ayarlar',
            'manage_options',
            'trt-yarisma-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Admin init
     */
    public function admin_init() {
        register_setting('trt_yarisma_settings', 'trt_yarisma_settings');
    }
    
    /**
     * Ana admin sayfası
     */
    public function admin_page() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch ($action) {
            case 'view':
                $this->view_application();
                break;
            case 'delete':
                $this->delete_application();
                break;
            default:
                $this->list_applications();
        }
    }
    
    /**
     * Başvuruları listele - Tüm kategoriler
     */
    private function list_applications() {
        // Filtreleme parametreleri
        $kategori = isset($_GET['kategori']) ? sanitize_text_field($_GET['kategori']) : '';
        $durum = isset($_GET['durum']) ? sanitize_text_field($_GET['durum']) : '';
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        
        // Tüm kategorilerden başvuruları getir
        $all_applications = array();
        
        if (empty($kategori) || $kategori == 'proje-destek-yarismasi') {
            $apps = $this->get_applications_by_category('proje-destek-yarismasi', $durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'international-competition') {
            $apps = $this->get_international_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'ogrenci-ulusal-belgesel') {
            $apps = $this->get_student_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'profesyonel-ulusal-belgesel') {
            $apps = $this->get_professional_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        // Tarihe göre sırala
        usort($all_applications, function($a, $b) {
            return strtotime($b['basvuru_tarihi']) - strtotime($a['basvuru_tarihi']);
        });
        
        $total = count($all_applications);
        $total_pages = ceil($total / $per_page);
        
        // Sayfalama için slice
        $offset = ($page - 1) * $per_page;
        $applications = array_slice($all_applications, $offset, $per_page);
        
        ?>
        <div class="wrap">
            <h1>TRT Yarışma Başvuruları</h1>
            
            <!-- Filtreler -->
            <div class="tablenav top">
                <form method="get" action="">
                    <input type="hidden" name="page" value="trt-yarisma" />
                    
                    <select name="kategori">
                        <option value="">Tüm Kategoriler</option>
                        <option value="proje-destek-yarismasi" <?php selected($kategori, 'proje-destek-yarismasi'); ?>>Proje Destek Yarışması</option>
                        <option value="profesyonel-ulusal-belgesel" <?php selected($kategori, 'profesyonel-ulusal-belgesel'); ?>>Profesyonel Ulusal Belgesel</option>
                        <option value="ogrenci-ulusal-belgesel" <?php selected($kategori, 'ogrenci-ulusal-belgesel'); ?>>Öğrenci Ulusal Belgesel</option>
                        <option value="international-competition" <?php selected($kategori, 'international-competition'); ?>>International Competition</option>
                    </select>
                    
                    <select name="durum">
                        <option value="">Tüm Durumlar</option>
                        <option value="beklemede" <?php selected($durum, 'beklemede'); ?>>Beklemede</option>
                        <option value="inceleniyor" <?php selected($durum, 'inceleniyor'); ?>>İnceleniyor</option>
                        <option value="onaylandi" <?php selected($durum, 'onaylandi'); ?>>Onaylandı</option>
                        <option value="reddedildi" <?php selected($durum, 'reddedildi'); ?>>Reddedildi</option>
                    </select>
                    
                    <input type="submit" class="button" value="Filtrele" />
                    
                    <a href="<?php echo admin_url('admin-post.php?action=trt_yarisma_export&kategori=' . $kategori . '&durum=' . $durum); ?>" class="button button-primary">📊 Excel'e Aktar</a>
                </form>
            </div>
            
            <!-- İstatistikler -->
            <div style="background: #f1f1f1; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>Toplam Başvuru: <?php echo $total; ?></strong>
                <?php if (!empty($kategori)): ?>
                    | Kategori: <?php echo ucfirst(str_replace('-', ' ', $kategori)); ?>
                <?php endif; ?>
                <?php if (!empty($durum)): ?>
                    | Durum: <?php echo ucfirst($durum); ?>
                <?php endif; ?>
            </div>
            
            <!-- Başvuru tablosu -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Film/Program Adı</th>
                        <th>Kategori</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo $app['id']; ?></td>
                                <td>
                                    <?php 
                                    $name = '';
                                    if (isset($app['ad']) && isset($app['soyad'])) {
                                        $name = $app['ad'] . ' ' . $app['soyad'];
                                    } elseif (isset($app['yonetmen_ad']) && isset($app['yonetmen_soyad'])) {
                                        $name = $app['yonetmen_ad'] . ' ' . $app['yonetmen_soyad'];
                                    }
                                    echo esc_html($name);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $email = $app['email'] ?? $app['yonetmen_email'] ?? '';
                                    echo esc_html($email);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $title = $app['program_adi'] ?? $app['film_adi'] ?? $app['orijinal_baslik'] ?? '';
                                    echo esc_html($title);
                                    ?>
                                </td>
                                <td>
                                    <span class="category-badge category-<?php echo $app['kategori']; ?>">
                                        <?php 
                                        switch($app['kategori']) {
                                            case 'proje-destek-yarismasi':
                                                echo 'Proje Destek';
                                                break;
                                            case 'international-competition':
                                                echo 'International';
                                                break;
                                            case 'ogrenci-ulusal-belgesel':
                                                echo 'Öğrenci';
                                                break;
                                            case 'profesyonel-ulusal-belgesel':
                                                echo 'Profesyonel';
                                                break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <select onchange="updateApplicationStatus('<?php echo $app['kategori']; ?>', <?php echo $app['id']; ?>, this.value)">
                                        <option value="beklemede" <?php selected($app['durum'], 'beklemede'); ?>>Beklemede</option>
                                        <option value="inceleniyor" <?php selected($app['durum'], 'inceleniyor'); ?>>İnceleniyor</option>
                                        <option value="onaylandi" <?php selected($app['durum'], 'onaylandi'); ?>>Onaylandı</option>
                                        <option value="reddedildi" <?php selected($app['durum'], 'reddedildi'); ?>>Reddedildi</option>
                                    </select>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($app['basvuru_tarihi'])); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=trt-yarisma&action=view&id=' . $app['id'] . '&kategori=' . $app['kategori']); ?>" class="button button-small">Görüntüle</a>
                                    <a href="<?php echo admin_url('admin.php?page=trt-yarisma&action=delete&id=' . $app['id'] . '&kategori=' . $app['kategori']); ?>" class="button button-small" onclick="return confirm('Bu başvuruyu silmek istediğinizden emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Henüz başvuru bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Sayfalama -->
            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $page_links = paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $page
                        ));
                        echo $page_links;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        .category-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        .category-proje-destek-yarismasi { background: #007cba; }
        .category-international-competition { background: #00a32a; }
        .category-ogrenci-ulusal-belgesel { background: #ff6900; }
        .category-profesyonel-ulusal-belgesel { background: #8f00ff; }
        </style>
        
        <script>
        function updateApplicationStatus(category, id, status) {
            jQuery.post(ajaxurl, {
                action: 'trt_yarisma_update_status',
                category: category,
                id: id,
                status: status,
                nonce: '<?php echo wp_create_nonce('trt_yarisma_admin_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Durum güncellenirken hata oluştu.');
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * Excel export - Tüm kategoriler için
     */
    public function export_applications() {
        if (!current_user_can('manage_options')) {
            wp_die('Yetkiniz yok.');
        }
        
        $kategori = isset($_GET['kategori']) ? sanitize_text_field($_GET['kategori']) : '';
        $durum = isset($_GET['durum']) ? sanitize_text_field($_GET['durum']) : '';
        
        // Kategori bazında farklı tablolardan veri çek
        $all_applications = array();
        
        if (empty($kategori) || $kategori == 'proje-destek-yarismasi') {
            $apps = $this->get_applications_by_category('proje-destek-yarismasi', $durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'international-competition') {
            $apps = $this->get_international_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'ogrenci-ulusal-belgesel') {
            $apps = $this->get_student_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        if (empty($kategori) || $kategori == 'profesyonel-ulusal-belgesel') {
            $apps = $this->get_professional_applications($durum);
            $all_applications = array_merge($all_applications, $apps);
        }
        
        // Excel dosyası oluştur
        $filename = 'trt-yarisma-basvurular-' . date('Y-m-d-H-i') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Başlıklar
        fputcsv($output, array(
            'ID', 'Kategori', 'Durum', 'Başvuru Tarihi',
            'Film/Program Adı', 'Orijinal Başlık', 'Dil', 'Ülke', 'Süre',
            'Ad', 'Soyad', 'Telefon', 'E-posta', 'Adres',
            'Program Konusu/Özet', 'Tahmini Bütçe', 'Yapımcı Ülke',
            'Yararlanılacak Kişiler', 'Çekim Yerleri', 'Proje Sunum Linki',
            'İndirilebilir Film Linki', 'İndirme Şifresi',
            'Önceki İşler', 'Özgeçmiş', 'Filmografi', 'Projeye Yaklaşım',
            'Sözleşme Onayı', 'KVKK Onayı'
        ));
        
        // Veriler
        foreach ($all_applications as $app) {
            fputcsv($output, array(
                $app['id'],
                $app['kategori'],
                $app['durum'],
                $app['basvuru_tarihi'],
                $app['film_adi'] ?? $app['program_adi'] ?? $app['orijinal_baslik'] ?? '',
                $app['orijinal_baslik'] ?? '',
                $app['dil'] ?? '',
                $app['ulke'] ?? $app['yapimci_ulke'] ?? '',
                $app['sure'] ?? '',
                $app['ad'] ?? $app['yonetmen_ad'] ?? '',
                $app['soyad'] ?? $app['yonetmen_soyad'] ?? '',
                $app['telefon'],
                $app['email'] ?? $app['yonetmen_email'] ?? '',
                $app['adres'],
                $app['program_konusu'] ?? $app['film_ozet'] ?? '',
                $app['tahmini_butce'] ?? '',
                $app['yapimci_ulke'] ?? $app['ulke'] ?? '',
                $app['yararlanilacak_kisiler'] ?? '',
                $app['cekim_yerleri'] ?? '',
                $app['proje_sunum_linki'] ?? $app['film_linki'] ?? '',
                $app['indirilebilir_film_linki'] ?? '',
                $app['indirme_sifresi'] ?? '',
                $app['onceki_isler'] ?? '',
                $app['ozgecmis'],
                $app['filmografi'] ?? '',
                $app['projeye_yaklasim'] ?? '',
                $app['sozlesme_onay'] ? 'Evet' : 'Hayır',
                $app['kvkk_onay'] ? 'Evet' : 'Hayır'
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Proje Destek Yarışması başvurularını getir
     */
    private function get_applications_by_category($category, $durum = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        $where_sql = "WHERE 1=1";
        $where_values = array();
        
        if (!empty($durum)) {
            $where_sql .= " AND durum = %s";
            $where_values[] = $durum;
        }
        
        $query = "SELECT *, '$category' as kategori FROM $table_name $where_sql ORDER BY basvuru_tarihi DESC";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($query, $where_values), ARRAY_A);
        } else {
            return $wpdb->get_results($query, ARRAY_A);
        }
    }
    
    /**
     * International Competition başvurularını getir
     */
    private function get_international_applications($durum = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trt_international_applications';
        
        $where_sql = "WHERE 1=1";
        $where_values = array();
        
        if (!empty($durum)) {
            $where_sql .= " AND durum = %s";
            $where_values[] = $durum;
        }
        
        $query = "SELECT *, 'international-competition' as kategori FROM $table_name $where_sql ORDER BY basvuru_tarihi DESC";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($query, $where_values), ARRAY_A);
        } else {
            return $wpdb->get_results($query, ARRAY_A);
        }
    }
    
    /**
     * Öğrenci Yarışması başvurularını getir
     */
    private function get_student_applications($durum = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trt_student_applications';
        
        $where_sql = "WHERE 1=1";
        $where_values = array();
        
        if (!empty($durum)) {
            $where_sql .= " AND durum = %s";
            $where_values[] = $durum;
        }
        
        $query = "SELECT *, 'ogrenci-ulusal-belgesel' as kategori FROM $table_name $where_sql ORDER BY basvuru_tarihi DESC";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($query, $where_values), ARRAY_A);
        } else {
            return $wpdb->get_results($query, ARRAY_A);
        }
    }
    
    /**
     * Profesyonel Yarışması başvurularını getir
     */
    private function get_professional_applications($durum = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'trt_professional_applications';
        
        $where_sql = "WHERE 1=1";
        $where_values = array();
        
        if (!empty($durum)) {
            $where_sql .= " AND durum = %s";
            $where_values[] = $durum;
        }
        
        $query = "SELECT *, 'profesyonel-ulusal-belgesel' as kategori FROM $table_name $where_sql ORDER BY basvuru_tarihi DESC";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($query, $where_values), ARRAY_A);
        } else {
            return $wpdb->get_results($query, ARRAY_A);
        }
    }
    
    /**
     * Başvuru durumunu güncelle (AJAX)
     */
    public function update_application_status() {
        if (!current_user_can('manage_options')) {
            wp_die('Yetkiniz yok.');
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'trt_yarisma_admin_nonce')) {
            wp_die('Güvenlik kontrolü başarısız.');
        }
        
        $category = sanitize_text_field($_POST['category']);
        $id = intval($_POST['id']);
        $status = sanitize_text_field($_POST['status']);
        
        global $wpdb;
        
        // Kategori bazında tablo seç
        switch($category) {
            case 'proje-destek-yarismasi':
                $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
                break;
            case 'international-competition':
                $table_name = $wpdb->prefix . 'trt_international_applications';
                break;
            case 'ogrenci-ulusal-belgesel':
                $table_name = $wpdb->prefix . 'trt_student_applications';
                break;
            case 'profesyonel-ulusal-belgesel':
                $table_name = $wpdb->prefix . 'trt_professional_applications';
                break;
            default:
                wp_send_json_error('Geçersiz kategori');
                return;
        }
        
        $result = $wpdb->update(
            $table_name,
            array('durum' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success('Durum güncellendi');
        } else {
            wp_send_json_error('Güncelleme başarısız');
        }
    }
    
    // Diğer fonksiyonlar (view_application, delete_application, settings_page) aynı kalacak...
}

