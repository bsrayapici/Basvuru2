<?php
/**
 * Admin panel sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_post_trt_yarisma_export', array($this, 'export_applications'));
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
     * Başvuruları listele
     */
    private function list_applications() {
        // Filtreleme parametreleri
        $kategori = isset($_GET['kategori']) ? sanitize_text_field($_GET['kategori']) : '';
        $durum = isset($_GET['durum']) ? sanitize_text_field($_GET['durum']) : '';
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        
        // Başvuruları getir
        $args = array(
            'kategori' => $kategori,
            'durum' => $durum,
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page
        );
        
        $applications = TRT_Yarisma_Database::get_applications($args);
        
        // Toplam sayı için ayrı sorgu
        global $wpdb;
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        $where_clauses = array();
        $where_values = array();
        
        if (!empty($kategori)) {
            $where_clauses[] = "kategori = %s";
            $where_values[] = $kategori;
        }
        
        if (!empty($durum)) {
            $where_clauses[] = "durum = %s";
            $where_values[] = $durum;
        }
        
        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }
        
        $total_query = "SELECT COUNT(*) FROM $table_name $where_sql";
        if (!empty($where_values)) {
            $total = $wpdb->get_var($wpdb->prepare($total_query, $where_values));
        } else {
            $total = $wpdb->get_var($total_query);
        }
        
        $total_pages = ceil($total / $per_page);
        
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
                    
                    <a href="<?php echo admin_url('admin-post.php?action=trt_yarisma_export&kategori=' . $kategori . '&durum=' . $durum); ?>" class="button button-secondary">Excel'e Aktar</a>
                </form>
            </div>
            
            <!-- Başvuru tablosu -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Program Adı</th>
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
                                <td><?php echo $app->id; ?></td>
                                <td><?php echo esc_html($app->ad . ' ' . $app->soyad); ?></td>
                                <td><?php echo esc_html($app->email); ?></td>
                                <td><?php echo esc_html($app->program_adi); ?></td>
                                <td><?php echo esc_html($app->kategori); ?></td>
                                <td>
                                    <select onchange="updateApplicationStatus(<?php echo $app->id; ?>, this.value)">
                                        <option value="beklemede" <?php selected($app->durum, 'beklemede'); ?>>Beklemede</option>
                                        <option value="inceleniyor" <?php selected($app->durum, 'inceleniyor'); ?>>İnceleniyor</option>
                                        <option value="onaylandi" <?php selected($app->durum, 'onaylandi'); ?>>Onaylandı</option>
                                        <option value="reddedildi" <?php selected($app->durum, 'reddedildi'); ?>>Reddedildi</option>
                                    </select>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($app->basvuru_tarihi)); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=trt-yarisma&action=view&id=' . $app->id); ?>" class="button button-small">Görüntüle</a>
                                    <a href="<?php echo admin_url('admin.php?page=trt-yarisma&action=delete&id=' . $app->id); ?>" class="button button-small" onclick="return confirm('Bu başvuruyu silmek istediğinizden emin misiniz?')">Sil</a>
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
        
        <script>
        function updateApplicationStatus(id, status) {
            jQuery.post(ajaxurl, {
                action: 'trt_yarisma_update_status',
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
     * Başvuru detayını görüntüle
     */
    private function view_application() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $application = TRT_Yarisma_Database::get_application($id);
        
        if (!$application) {
            echo '<div class="wrap"><h1>Başvuru bulunamadı</h1></div>';
            return;
        }
        
        ?>
        <div class="wrap">
            <h1>Başvuru Detayı - #<?php echo $application->id; ?></h1>
            
            <a href="<?php echo admin_url('admin.php?page=trt-yarisma'); ?>" class="button">&larr; Geri Dön</a>
            
            <div style="margin-top: 20px;">
                <table class="form-table">
                    <tr>
                        <th>Başvuru ID</th>
                        <td><?php echo $application->id; ?></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><?php echo esc_html($application->kategori); ?></td>
                    </tr>
                    <tr>
                        <th>Durum</th>
                        <td><?php echo esc_html($application->durum); ?></td>
                    </tr>
                    <tr>
                        <th>Başvuru Tarihi</th>
                        <td><?php echo date('d.m.Y H:i', strtotime($application->basvuru_tarihi)); ?></td>
                    </tr>
                </table>
                
                <h2>Eser Linki ve Bilgileri</h2>
                <table class="form-table">
                    <tr>
                        <th>Program Adı</th>
                        <td><?php echo esc_html($application->program_adi); ?></td>
                    </tr>
                    <tr>
                        <th>Program Konusu</th>
                        <td><?php echo esc_html($application->program_konusu); ?></td>
                    </tr>
                    <tr>
                        <th>Tahmini Bütçe</th>
                        <td><?php echo esc_html($application->tahmini_butce); ?></td>
                    </tr>
                    <tr>
                        <th>Yapımcı Ülke</th>
                        <td><?php echo esc_html($application->yapimci_ulke); ?></td>
                    </tr>
                    <tr>
                        <th>Yararlanılacak Kişiler</th>
                        <td><?php echo nl2br(esc_html($application->yararlanilacak_kisiler)); ?></td>
                    </tr>
                    <tr>
                        <th>Çekim Yerleri</th>
                        <td><?php echo nl2br(esc_html($application->cekim_yerleri)); ?></td>
                    </tr>
                    <tr>
                        <th>Proje Sunum Linki</th>
                        <td>
                            <?php if (!empty($application->proje_sunum_linki)): ?>
                                <a href="<?php echo esc_url($application->proje_sunum_linki); ?>" target="_blank"><?php echo esc_html($application->proje_sunum_linki); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>İndirme Şifresi</th>
                        <td><?php echo esc_html($application->indirme_sifresi); ?></td>
                    </tr>
                </table>
                
                <h2>Eser Sahibi Bilgileri</h2>
                <table class="form-table">
                    <tr>
                        <th>Ad</th>
                        <td><?php echo esc_html($application->ad); ?></td>
                    </tr>
                    <tr>
                        <th>Soyad</th>
                        <td><?php echo esc_html($application->soyad); ?></td>
                    </tr>
                    <tr>
                        <th>Telefon</th>
                        <td><?php echo esc_html($application->telefon); ?></td>
                    </tr>
                    <tr>
                        <th>E-posta</th>
                        <td><?php echo esc_html($application->email); ?></td>
                    </tr>
                    <tr>
                        <th>Adres</th>
                        <td><?php echo nl2br(esc_html($application->adres)); ?></td>
                    </tr>
                    <tr>
                        <th>Önceki İşler</th>
                        <td><?php echo nl2br(esc_html($application->onceki_isler)); ?></td>
                    </tr>
                    <tr>
                        <th>Özgeçmiş</th>
                        <td><?php echo nl2br(esc_html($application->ozgecmis)); ?></td>
                    </tr>
                    <tr>
                        <th>Projeye Yaklaşım</th>
                        <td><?php echo nl2br(esc_html($application->projeye_yaklasim)); ?></td>
                    </tr>
                </table>
                
                <h2>Sözleşme Onayları</h2>
                <table class="form-table">
                    <tr>
                        <th>Sözleşme Onayı</th>
                        <td><?php echo $application->sozlesme_onay ? 'Evet' : 'Hayır'; ?></td>
                    </tr>
                    <tr>
                        <th>KVKK Onayı</th>
                        <td><?php echo $application->kvkk_onay ? 'Evet' : 'Hayır'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Başvuru sil
     */
    private function delete_application() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
        }
        
        wp_redirect(admin_url('admin.php?page=trt-yarisma'));
        exit;
    }
    
    /**
     * Ayarlar sayfası
     */
    public function settings_page() {
        if (isset($_POST['submit'])) {
            $settings = array(
                'smtp_host' => sanitize_text_field($_POST['smtp_host']),
                'smtp_port' => sanitize_text_field($_POST['smtp_port']),
                'smtp_username' => sanitize_text_field($_POST['smtp_username']),
                'smtp_password' => sanitize_text_field($_POST['smtp_password']),
                'smtp_encryption' => sanitize_text_field($_POST['smtp_encryption']),
                'from_email' => sanitize_email($_POST['from_email']),
                'from_name' => sanitize_text_field($_POST['from_name'])
            );
            
            update_option('trt_yarisma_settings', $settings);
            echo '<div class="notice notice-success"><p>Ayarlar kaydedildi.</p></div>';
        }
        
        $settings = get_option('trt_yarisma_settings', array());
        
        ?>
        <div class="wrap">
            <h1>TRT Yarışma Ayarları</h1>
            
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th colspan="2"><h2>SMTP E-posta Ayarları</h2></th>
                    </tr>
                    <tr>
                        <th>SMTP Host</th>
                        <td><input type="text" name="smtp_host" value="<?php echo esc_attr($settings['smtp_host'] ?? ''); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>SMTP Port</th>
                        <td><input type="text" name="smtp_port" value="<?php echo esc_attr($settings['smtp_port'] ?? '587'); ?>" class="small-text" /></td>
                    </tr>
                    <tr>
                        <th>SMTP Kullanıcı Adı</th>
                        <td><input type="text" name="smtp_username" value="<?php echo esc_attr($settings['smtp_username'] ?? ''); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>SMTP Şifre</th>
                        <td><input type="password" name="smtp_password" value="<?php echo esc_attr($settings['smtp_password'] ?? ''); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>Şifreleme</th>
                        <td>
                            <select name="smtp_encryption">
                                <option value="tls" <?php selected($settings['smtp_encryption'] ?? 'tls', 'tls'); ?>>TLS</option>
                                <option value="ssl" <?php selected($settings['smtp_encryption'] ?? 'tls', 'ssl'); ?>>SSL</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Gönderen E-posta</th>
                        <td><input type="email" name="from_email" value="<?php echo esc_attr($settings['from_email'] ?? get_option('admin_email')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>Gönderen Adı</th>
                        <td><input type="text" name="from_name" value="<?php echo esc_attr($settings['from_name'] ?? get_option('blogname')); ?>" class="regular-text" /></td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Excel export
     */
    public function export_applications() {
        if (!current_user_can('manage_options')) {
            wp_die('Yetkiniz yok.');
        }
        
        $kategori = isset($_GET['kategori']) ? sanitize_text_field($_GET['kategori']) : '';
        $durum = isset($_GET['durum']) ? sanitize_text_field($_GET['durum']) : '';
        
        $args = array(
            'kategori' => $kategori,
            'durum' => $durum,
            'limit' => 9999
        );
        
        $applications = TRT_Yarisma_Database::get_applications($args);
        
        // Excel dosyası oluştur
        $filename = 'trt-yarisma-basvurular-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Başlıklar
        fputcsv($output, array(
            'ID', 'Kategori', 'Durum', 'Başvuru Tarihi',
            'Program Adı', 'Program Konusu', 'Tahmini Bütçe', 'Yapımcı Ülke',
            'Ad', 'Soyad', 'Telefon', 'E-posta', 'Adres',
            'Yararlanılacak Kişiler', 'Çekim Yerleri', 'Proje Sunum Linki',
            'Önceki İşler', 'Özgeçmiş', 'Projeye Yaklaşım',
            'Sözleşme Onayı', 'KVKK Onayı'
        ));
        
        // Veriler
        foreach ($applications as $app) {
            fputcsv($output, array(
                $app->id,
                $app->kategori,
                $app->durum,
                $app->basvuru_tarihi,
                $app->program_adi,
                $app->program_konusu,
                $app->tahmini_butce,
                $app->yapimci_ulke,
                $app->ad,
                $app->soyad,
                $app->telefon,
                $app->email,
                $app->adres,
                $app->yararlanilacak_kisiler,
                $app->cekim_yerleri,
                $app->proje_sunum_linki,
                $app->onceki_isler,
                $app->ozgecmis,
                $app->projeye_yaklasim,
                $app->sozlesme_onay ? 'Evet' : 'Hayır',
                $app->kvkk_onay ? 'Evet' : 'Hayır'
            ));
        }
        
        fclose($output);
        exit;
    }
}

