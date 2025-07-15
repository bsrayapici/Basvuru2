<?php
/**
 * Plugin Name: TRT Yarışma Başvuru Sistemi
 * Plugin URI: https://example.com
 * Description: TRT yarışmaları için çok adımlı başvuru formu sistemi
 * Version: 4.5.5
 * Author: Manus AI
 * License: GPL v2 or later
 * Text Domain: trt-yarisma-basvuru
 */

// Doğrudan erişimi engelle
if (!defined('ABSPATH')) {
    exit;
}

// Plugin sabitleri
define('TRT_YARISMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRT_YARISMA_PATH', plugin_dir_path(__FILE__));
define('TRT_YARISMA_VERSION', '4.5.5');

// Ana sınıfı yükle
require_once TRT_YARISMA_PATH . 'includes/class-trt-yarisma-main.php';

// Plugin aktivasyon/deaktivasyon hook'ları
register_activation_hook(__FILE__, array('TRT_Yarisma_Main', 'activate'));
register_deactivation_hook(__FILE__, array('TRT_Yarisma_Main', 'deactivate'));

// Plugin'i başlat
function trt_yarisma_init() {
    new TRT_Yarisma_Main();
}
add_action('plugins_loaded', 'trt_yarisma_init');

