<?php
/*
Plugin Name: Blog Instruction
Description: A plugin to provide instructions for writing blog posts.
Author: Hridoy Varaby
Version: 2.0
*/

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Admin page callback
function blog_instruction_settings_page() {
    ?>
    <div class="wrap">
        <h2>Blog Instruction Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('blog_instruction_settings_group'); ?>
            <?php do_settings_sections('blog_instruction_settings_group'); ?>
            <?php blog_instruction_editor_callback(); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


// Settings initialization
function blog_instruction_settings_init() {
    register_setting('blog_instruction_settings_group', 'blog_instruction_text');
}

// Admin menu
function blog_instruction_add_menu() {
    $allowed_roles = array('administrator', 'editor');
    $current_user = wp_get_current_user();

    if (array_intersect($allowed_roles, $current_user->roles)) {
        add_options_page(
            'Blog Instruction Settings',
            'Blog Instruction',
            'read',
            'blog_instruction_settings',
            'blog_instruction_settings_page'
        );
    }
}

add_action('admin_menu', 'blog_instruction_add_menu');


// Editor for instructions on the settings page
function blog_instruction_editor_callback() {
    $content = get_option('blog_instruction_text', ''); // Retrieve saved content
    wp_editor($content, 'blog_instruction_text', array('textarea_name' => 'blog_instruction_text'));
}

// Meta box for instructions on the Add New Post page
function blog_instruction_meta_box() {
    $instruction_text = get_option('blog_instruction_text', ''); // Retrieve saved content

    // Apply formatting and shortcodes
    $formatted_text = wpautop(do_shortcode($instruction_text));

    echo $formatted_text;
}


// Add meta box to post editor screen
function blog_instruction_add_meta_box() {
    add_meta_box('blog_instruction_meta_box', 'Instruction', 'blog_instruction_meta_box', 'post', 'side', 'high');
}

// Enqueue scripts and styles
function blog_instruction_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('editor');
    wp_enqueue_script('quicktags');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('wp-ajax-response');
    wp_enqueue_script('wp-lists');
    wp_enqueue_script('wp-pointer');
    wp_enqueue_script('colorpicker');
    wp_enqueue_style('colors');
    wp_enqueue_style('media');
}

// Hook actions and filters
add_action('admin_menu', 'blog_instruction_add_menu');
add_action('admin_init', 'blog_instruction_settings_init');
add_action('add_meta_boxes', 'blog_instruction_add_meta_box');
add_action('admin_enqueue_scripts', 'blog_instruction_enqueue_scripts');

// Add the editor to the settings page
add_action('blog_instruction_settings_page', 'blog_instruction_editor_callback');
