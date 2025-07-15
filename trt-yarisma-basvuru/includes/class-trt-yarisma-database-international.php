<?php
/**
 * International Competition Veritabanı işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Database_International {
    
    private $table_name;
    private $festivals_table;
    private $prizes_table;
    private $social_media_table;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'trt_international_applications';
        $this->festivals_table = $wpdb->prefix . 'trt_international_festivals';
        $this->prizes_table = $wpdb->prefix . 'trt_international_prizes';
        $this->social_media_table = $wpdb->prefix . 'trt_international_social_media';
    }
    
    /**
     * Veritabanı tablolarını oluştur
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ana başvuru tablosu
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            category varchar(100) NOT NULL,
            original_title varchar(255) NOT NULL,
            original_title_english varchar(255),
            original_language varchar(100) NOT NULL,
            production_country varchar(100) NOT NULL,
            duration int(11) NOT NULL,
            audio_information text,
            music_information text,
            aspect_ratio varchar(50),
            production_date date,
            short_summary text NOT NULL,
            downloadable_link varchar(500) NOT NULL,
            downloadable_password varchar(255),
            imdb_link varchar(500),
            director_name varchar(255) NOT NULL,
            director_surname varchar(255) NOT NULL,
            director_phone varchar(50),
            director_email varchar(255) NOT NULL,
            director_address text,
            director_biography text,
            director_filmography text,
            participation_agreement tinyint(1) NOT NULL DEFAULT 0,
            data_protection tinyint(1) NOT NULL DEFAULT 0,
            application_date datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(50) DEFAULT 'pending',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Festivals tablosu
        $sql_festivals = "CREATE TABLE $this->festivals_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            application_id mediumint(9) NOT NULL,
            festival_name varchar(255) NOT NULL,
            festival_year int(4),
            PRIMARY KEY (id),
            KEY application_id (application_id)
        ) $charset_collate;";
        
        // Prizes tablosu
        $sql_prizes = "CREATE TABLE $this->prizes_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            application_id mediumint(9) NOT NULL,
            prize_name varchar(255) NOT NULL,
            prize_event varchar(255),
            PRIMARY KEY (id),
            KEY application_id (application_id)
        ) $charset_collate;";
        
        // Social Media tablosu
        $sql_social = "CREATE TABLE $this->social_media_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            application_id mediumint(9) NOT NULL,
            platform varchar(100) NOT NULL,
            url varchar(500) NOT NULL,
            PRIMARY KEY (id),
            KEY application_id (application_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql_festivals);
        dbDelta($sql_prizes);
        dbDelta($sql_social);
    }
    
    /**
     * International başvuru kaydet
     */
    public function save_international_application($data) {
        global $wpdb;
        
        // Ana başvuru verilerini kaydet
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'category' => $data['category'],
                'original_title' => $data['original_title'],
                'original_title_english' => $data['original_title_english'],
                'original_language' => $data['original_language'],
                'production_country' => $data['production_country'],
                'duration' => intval($data['duration']),
                'audio_information' => $data['audio_information'],
                'music_information' => $data['music_information'],
                'aspect_ratio' => $data['aspect_ratio'],
                'production_date' => $data['production_date'] ? $data['production_date'] : null,
                'short_summary' => $data['short_summary'],
                'downloadable_link' => $data['downloadable_link'],
                'downloadable_password' => $data['downloadable_password'],
                'imdb_link' => $data['imdb_link'],
                'director_name' => $data['director_name'],
                'director_surname' => $data['director_surname'],
                'director_phone' => $data['director_phone'],
                'director_email' => $data['director_email'],
                'director_address' => $data['director_address'],
                'director_biography' => $data['director_biography'],
                'director_filmography' => $data['director_filmography'],
                'participation_agreement' => $data['participation_agreement'],
                'data_protection' => $data['data_protection'],
                'application_date' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s',
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
                '%s', '%d', '%d', '%s'
            )
        );
        
        if ($result === false) {
            return false;
        }
        
        $application_id = $wpdb->insert_id;
        
        // Festival verilerini kaydet
        if (!empty($data['festival_name']) && is_array($data['festival_name'])) {
            foreach ($data['festival_name'] as $index => $festival_name) {
                if (!empty($festival_name)) {
                    $festival_year = isset($data['festival_year'][$index]) ? intval($data['festival_year'][$index]) : null;
                    
                    $wpdb->insert(
                        $this->festivals_table,
                        array(
                            'application_id' => $application_id,
                            'festival_name' => $festival_name,
                            'festival_year' => $festival_year
                        ),
                        array('%d', '%s', '%d')
                    );
                }
            }
        }
        
        // Prize verilerini kaydet
        if (!empty($data['prize_name']) && is_array($data['prize_name'])) {
            foreach ($data['prize_name'] as $index => $prize_name) {
                if (!empty($prize_name)) {
                    $prize_event = isset($data['prize_event'][$index]) ? $data['prize_event'][$index] : '';
                    
                    $wpdb->insert(
                        $this->prizes_table,
                        array(
                            'application_id' => $application_id,
                            'prize_name' => $prize_name,
                            'prize_event' => $prize_event
                        ),
                        array('%d', '%s', '%s')
                    );
                }
            }
        }
        
        // Social Media verilerini kaydet
        if (!empty($data['social_platform']) && is_array($data['social_platform'])) {
            foreach ($data['social_platform'] as $index => $platform) {
                if (!empty($platform) && !empty($data['social_url'][$index])) {
                    $wpdb->insert(
                        $this->social_media_table,
                        array(
                            'application_id' => $application_id,
                            'platform' => $platform,
                            'url' => $data['social_url'][$index]
                        ),
                        array('%d', '%s', '%s')
                    );
                }
            }
        }
        
        return $application_id;
    }
    
    /**
     * International başvuru getir
     */
    public function get_international_application($id) {
        global $wpdb;
        
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE id = %d",
            $id
        ), ARRAY_A);
        
        if ($application) {
            // Festival verilerini getir
            $festivals = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $this->festivals_table WHERE application_id = %d",
                $id
            ), ARRAY_A);
            
            // Prize verilerini getir
            $prizes = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $this->prizes_table WHERE application_id = %d",
                $id
            ), ARRAY_A);
            
            // Social Media verilerini getir
            $social_media = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $this->social_media_table WHERE application_id = %d",
                $id
            ), ARRAY_A);
            
            $application['festivals'] = $festivals;
            $application['prizes'] = $prizes;
            $application['social_media'] = $social_media;
        }
        
        return $application;
    }
    
    /**
     * Tüm international başvuruları getir
     */
    public function get_all_international_applications($status = null, $limit = 50, $offset = 0) {
        global $wpdb;
        
        $where_clause = '';
        if ($status) {
            $where_clause = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        $applications = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $this->table_name $where_clause ORDER BY application_date DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ), ARRAY_A);
        
        return $applications;
    }
    
    /**
     * International başvuru durumunu güncelle
     */
    public function update_international_application_status($id, $status) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * International başvuru sil
     */
    public function delete_international_application($id) {
        global $wpdb;
        
        // İlişkili verileri sil
        $wpdb->delete($this->festivals_table, array('application_id' => $id), array('%d'));
        $wpdb->delete($this->prizes_table, array('application_id' => $id), array('%d'));
        $wpdb->delete($this->social_media_table, array('application_id' => $id), array('%d'));
        
        // Ana kaydı sil
        return $wpdb->delete($this->table_name, array('id' => $id), array('%d'));
    }
    
    /**
     * International başvuru sayısını getir
     */
    public function get_international_applications_count($status = null) {
        global $wpdb;
        
        $where_clause = '';
        if ($status) {
            $where_clause = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $this->table_name $where_clause");
    }
}

