<?php
/**
 * Veritabanı işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Database {
    
    /**
     * Veritabanı tablolarını oluştur
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Başvurular tablosu
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            kategori varchar(100) NOT NULL,
            durum varchar(20) DEFAULT 'beklemede',
            basvuru_tarihi datetime DEFAULT CURRENT_TIMESTAMP,
            
            -- Eser Linki ve Bilgileri
            program_adi varchar(255) NOT NULL,
            program_konusu text,
            tahmini_butce varchar(100),
            yapimci_ulke varchar(100),
            yararlanilacak_kisiler text,
            cekim_yerleri text,
            proje_sunum_linki varchar(500),
            indirme_sifresi varchar(100),
            
            -- Eser Sahibi Bilgileri
            ad varchar(100) NOT NULL,
            soyad varchar(100) NOT NULL,
            telefon varchar(20),
            email varchar(100) NOT NULL,
            adres text,
            onceki_isler text,
            ozgecmis text,
            projeye_yaklasim text,
            
            -- Sözleşme Onayları
            sozlesme_onay tinyint(1) DEFAULT 0,
            kvkk_onay tinyint(1) DEFAULT 0,
            
            PRIMARY KEY (id),
            KEY kategori (kategori),
            KEY durum (durum),
            KEY basvuru_tarihi (basvuru_tarihi)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Kategoriler tablosu
        $categories_table = $wpdb->prefix . 'trt_yarisma_kategoriler';
        
        $sql_categories = "CREATE TABLE $categories_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            kategori_adi varchar(255) NOT NULL,
            kategori_slug varchar(100) NOT NULL,
            aktif tinyint(1) DEFAULT 1,
            sira_no int DEFAULT 0,
            aciklama text,
            olusturma_tarihi datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY kategori_slug (kategori_slug)
        ) $charset_collate;";
        
        dbDelta($sql_categories);
        
        // Varsayılan kategorileri ekle
        self::insert_default_categories();
    }
    
    /**
     * Varsayılan kategorileri ekle
     */
    private static function insert_default_categories() {
        global $wpdb;
        
        $categories_table = $wpdb->prefix . 'trt_yarisma_kategoriler';
        
        $default_categories = array(
            array(
                'kategori_adi' => 'Profesyonel | Ulusal Belgesel Ödülleri Yarışması',
                'kategori_slug' => 'profesyonel-ulusal-belgesel',
                'sira_no' => 1
            ),
            array(
                'kategori_adi' => 'Öğrenci | Ulusal Belgesel Ödülleri Yarışması',
                'kategori_slug' => 'ogrenci-ulusal-belgesel',
                'sira_no' => 2
            ),
            array(
                'kategori_adi' => 'International Competition',
                'kategori_slug' => 'international-competition',
                'sira_no' => 3
            ),
            array(
                'kategori_adi' => 'Proje Destek Yarışması',
                'kategori_slug' => 'proje-destek-yarismasi',
                'sira_no' => 4
            )
        );
        
        foreach ($default_categories as $category) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $categories_table WHERE kategori_slug = %s",
                $category['kategori_slug']
            ));
            
            if (!$exists) {
                $wpdb->insert($categories_table, $category);
            }
        }
    }
    
    /**
     * Başvuru kaydet
     */
    public static function save_application($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        // Veriyi sanitize et
        $sanitized_data = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized_data[$key] = sanitize_textarea_field(implode("\n", $value));
            } else {
                $sanitized_data[$key] = sanitize_text_field($value);
            }
        }
        
        $result = $wpdb->insert($table_name, $sanitized_data);
        
        if ($result !== false) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Başvuruları listele
     */
    public static function get_applications($args = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        $defaults = array(
            'kategori' => '',
            'durum' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'basvuru_tarihi',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_clauses = array();
        $where_values = array();
        
        if (!empty($args['kategori'])) {
            $where_clauses[] = "kategori = %s";
            $where_values[] = $args['kategori'];
        }
        
        if (!empty($args['durum'])) {
            $where_clauses[] = "durum = %s";
            $where_values[] = $args['durum'];
        }
        
        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }
        
        $sql = "SELECT * FROM $table_name $where_sql ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];
        
        return $wpdb->get_results($wpdb->prepare($sql, $where_values));
    }
    
    /**
     * Başvuru detayını getir
     */
    public static function get_application($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Başvuru durumunu güncelle
     */
    public static function update_application_status($id, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'trt_yarisma_basvurular';
        
        return $wpdb->update(
            $table_name,
            array('durum' => sanitize_text_field($status)),
            array('id' => intval($id)),
            array('%s'),
            array('%d')
        );
    }
}

