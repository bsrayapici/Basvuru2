<?php
/**
 * Profesyonel Yarışması Veritabanı işlemleri sınıfı
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRT_Yarisma_Database_Professional {
    
    private $table_submissions;
    private $table_directors;
    private $table_additional_info;
    private $table_person_roles;
    
    public function __construct() {
        global $wpdb;
        $this->table_submissions = $wpdb->prefix . 'trt_professional_submissions';
        $this->table_directors = $wpdb->prefix . 'trt_professional_directors';
        $this->table_additional_info = $wpdb->prefix . 'trt_professional_additional_info';
        $this->table_person_roles = $wpdb->prefix . 'trt_professional_person_roles';
    }
    
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Main submissions table
        $sql1 = "CREATE TABLE {$this->table_submissions} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            category varchar(100) NOT NULL,
            original_title varchar(255) NOT NULL,
            turkish_title varchar(255),
            original_language varchar(100) NOT NULL,
            production_country varchar(100) NOT NULL,
            duration int(11) NOT NULL,
            audio_info varchar(255),
            music_info varchar(255),
            aspect_ratio varchar(50),
            production_date varchar(20),
            summary text NOT NULL,
            download_link varchar(500) NOT NULL,
            download_password varchar(100),
            imdb_link varchar(500),
            applicant_name varchar(100) NOT NULL,
            applicant_surname varchar(100) NOT NULL,
            phone varchar(50) NOT NULL,
            email varchar(100) NOT NULL,
            address text NOT NULL,
            biography text,
            filmography text,
            agreement_accept tinyint(1) NOT NULL DEFAULT 0,
            privacy_accept tinyint(1) NOT NULL DEFAULT 0,
            status varchar(50) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Directors table
        $sql2 = "CREATE TABLE {$this->table_directors} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            submission_id mediumint(9) NOT NULL,
            name varchar(100) NOT NULL,
            surname varchar(100),
            phone varchar(50),
            email varchar(100),
            address text,
            PRIMARY KEY (id),
            KEY submission_id (submission_id)
        ) $charset_collate;";
        
        // Additional info table (festivals, awards, social media)
        $sql3 = "CREATE TABLE {$this->table_additional_info} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            submission_id mediumint(9) NOT NULL,
            info_type varchar(50) NOT NULL,
            info_value text NOT NULL,
            PRIMARY KEY (id),
            KEY submission_id (submission_id)
        ) $charset_collate;";
        
        // Person roles table (producers, writers, etc.)
        $sql4 = "CREATE TABLE {$this->table_person_roles} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            submission_id mediumint(9) NOT NULL,
            role_type varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            surname varchar(100),
            phone varchar(50),
            email varchar(100),
            address text,
            PRIMARY KEY (id),
            KEY submission_id (submission_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
    }
    
    public function save_submission($data) {
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Insert main submission
            $submission_data = array(
                'category' => $data['category'],
                'original_title' => $data['original_title'],
                'turkish_title' => $data['turkish_title'],
                'original_language' => $data['original_language'],
                'production_country' => $data['production_country'],
                'duration' => $data['duration'],
                'audio_info' => $data['audio_info'],
                'music_info' => $data['music_info'],
                'aspect_ratio' => $data['aspect_ratio'],
                'production_date' => $data['production_date'],
                'summary' => $data['summary'],
                'download_link' => $data['download_link'],
                'download_password' => $data['download_password'],
                'imdb_link' => $data['imdb_link'],
                'applicant_name' => $data['applicant_name'],
                'applicant_surname' => $data['applicant_surname'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'biography' => $data['biography'],
                'filmography' => $data['filmography'],
                'agreement_accept' => $data['agreement_accept'],
                'privacy_accept' => $data['privacy_accept']
            );
            
            $result = $wpdb->insert($this->table_submissions, $submission_data);
            
            if ($result === false) {
                throw new Exception('Submission insert failed');
            }
            
            $submission_id = $wpdb->insert_id;
            
            // Insert directors
            if (!empty($data['directors'])) {
                foreach ($data['directors'] as $director) {
                    $director_data = array(
                        'submission_id' => $submission_id,
                        'name' => $director['name'],
                        'surname' => $director['surname'],
                        'phone' => $director['phone'],
                        'email' => $director['email'],
                        'address' => $director['address']
                    );
                    
                    $result = $wpdb->insert($this->table_directors, $director_data);
                    if ($result === false) {
                        throw new Exception('Director insert failed');
                    }
                }
            }
            
            // Insert additional info
            $additional_fields = array('festivals', 'awards', 'social_media');
            foreach ($additional_fields as $field) {
                if (!empty($data[$field])) {
                    foreach ($data[$field] as $value) {
                        if (!empty($value)) {
                            $info_data = array(
                                'submission_id' => $submission_id,
                                'info_type' => $field,
                                'info_value' => $value
                            );
                            
                            $result = $wpdb->insert($this->table_additional_info, $info_data);
                            if ($result === false) {
                                throw new Exception('Additional info insert failed');
                            }
                        }
                    }
                }
            }
            
            // Insert person roles
            $person_roles = array('producers', 'writers', 'sponsors', 'sales_agent', 'crew');
            foreach ($person_roles as $role) {
                if (!empty($data[$role])) {
                    foreach ($data[$role] as $person) {
                        if (!empty($person['name'])) {
                            $person_data = array(
                                'submission_id' => $submission_id,
                                'role_type' => $role,
                                'name' => $person['name'],
                                'surname' => $person['surname'],
                                'phone' => $person['phone'],
                                'email' => $person['email'],
                                'address' => $person['address']
                            );
                            
                            $result = $wpdb->insert($this->table_person_roles, $person_data);
                            if ($result === false) {
                                throw new Exception('Person role insert failed');
                            }
                        }
                    }
                }
            }
            
            // Commit transaction
            $wpdb->query('COMMIT');
            
            return $submission_id;
            
        } catch (Exception $e) {
            // Rollback transaction
            $wpdb->query('ROLLBACK');
            error_log('TRT Professional Submission Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_submission($id) {
        global $wpdb;
        
        $submission = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_submissions} WHERE id = %d",
            $id
        ), ARRAY_A);
        
        if (!$submission) {
            return false;
        }
        
        // Get directors
        $directors = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_directors} WHERE submission_id = %d",
            $id
        ), ARRAY_A);
        
        $submission['directors'] = $directors;
        
        // Get additional info
        $additional_info = $wpdb->get_results($wpdb->prepare(
            "SELECT info_type, info_value FROM {$this->table_additional_info} WHERE submission_id = %d",
            $id
        ), ARRAY_A);
        
        foreach ($additional_info as $info) {
            if (!isset($submission[$info['info_type']])) {
                $submission[$info['info_type']] = array();
            }
            $submission[$info['info_type']][] = $info['info_value'];
        }
        
        // Get person roles
        $person_roles = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_person_roles} WHERE submission_id = %d",
            $id
        ), ARRAY_A);
        
        foreach ($person_roles as $person) {
            if (!isset($submission[$person['role_type']])) {
                $submission[$person['role_type']] = array();
            }
            $submission[$person['role_type']][] = array(
                'name' => $person['name'],
                'surname' => $person['surname'],
                'phone' => $person['phone'],
                'email' => $person['email'],
                'address' => $person['address']
            );
        }
        
        return $submission;
    }
    
    public function get_all_submissions($status = null, $limit = 20, $offset = 0) {
        global $wpdb;
        
        $where = '';
        if ($status) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        $submissions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_submissions}{$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ), ARRAY_A);
        
        return $submissions;
    }
    
    public function update_submission_status($id, $status) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_submissions,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
    
    public function delete_submission($id) {
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Delete from all related tables
            $wpdb->delete($this->table_directors, array('submission_id' => $id));
            $wpdb->delete($this->table_additional_info, array('submission_id' => $id));
            $wpdb->delete($this->table_person_roles, array('submission_id' => $id));
            $wpdb->delete($this->table_submissions, array('id' => $id));
            
            $wpdb->query('COMMIT');
            return true;
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }
    
    public function get_submissions_count($status = null) {
        global $wpdb;
        
        $where = '';
        if ($status) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_submissions}{$where}");
    }
}

new TRT_Yarisma_Database_Professional();

