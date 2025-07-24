<?php

if (!defined('WPINC')) {
    die;
}


define('WP_FEEDBACK_FORM_VERSION', '1.0.0');
define('WP_FEEDBACK_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_FEEDBACK_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));


require_once WP_FEEDBACK_FORM_PLUGIN_DIR . 'public/feedback-display.php';
require_once WP_FEEDBACK_FORM_PLUGIN_DIR . 'admin/feedback-admin.php';


register_activation_hook(__FILE__, 'wp_feedback_form_activate');

function wp_feedback_form_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
  
    $table_name = $wpdb->prefix . 'feedback_submissions';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        rating int(1) NOT NULL,
        message text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


register_deactivation_hook(__FILE__, 'wp_feedback_form_deactivate');

function wp_feedback_form_deactivate() {
    
}


function wp_feedback_form_enqueue_scripts() {
    wp_enqueue_style('wp-feedback-form-style', 
        WP_FEEDBACK_FORM_PLUGIN_URL . 'css/style.css',
        array(),
        WP_FEEDBACK_FORM_VERSION
    );
    
    wp_enqueue_script('wp-feedback-form-script',
        WP_FEEDBACK_FORM_PLUGIN_URL . 'js/script.js',
        array('jquery'),
        WP_FEEDBACK_FORM_VERSION,
        true
    );
    
    wp_localize_script('wp-feedback-form-script', 'wpFeedbackForm', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_feedback_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'wp_feedback_form_enqueue_scripts');
