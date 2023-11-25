<?php
/*
Plugin Name: Blog Instruction
Description: A plugin to provide instructions for writing blog posts.
Author: Hridoy Varaby | Varabit
Author URI: https://facebook.com/hridoy.varaby
Version: 2.1.5
*/


// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check for required plugins on activation
function blog_instruction_check_required_plugins() {
    // Check if Classic Editor plugin is installed and active
    if (!is_plugin_active('classic-editor/classic-editor.php')) {
        // Classic Editor is not active, prompt the user to install it
        add_action('admin_notices', 'blog_instruction_install_classic_editor_notice');
    }
}

// Display notice to install Classic Editor
function blog_instruction_install_classic_editor_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <strong>Blog Instruction:</strong>
            This plugin requires the "Classic Editor" plugin to be installed and activated for full functionality.
            <a href="<?php echo esc_url(admin_url('plugin-install.php?s=Classic+Editor&tab=search&type=term')); ?>">
                Install Classic Editor now.
            </a>
        </p>
    </div>
    <?php
}

// Admin page callback
function blog_instruction_settings_page() {
    $instruction_text = get_option('blog_instruction_text', '');
    $show_instruction = get_option('blog_instruction_show', ''); // New option

    ?>
    <div class="wrap">
        <h2>Blog Instruction Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('blog_instruction_settings_group'); ?>
            <?php do_settings_sections('blog_instruction_settings_group'); ?>
            
            <label for="blog_instruction_show" style="display: block; padding-top: 30px; padding-bottom: 30px;">
                <input type="checkbox" id="blog_instruction_show" name="blog_instruction_show" <?php checked( $show_instruction, 'on' ); ?> />
                Show Instructions on Add New Post Page
            </label>

            <!-- Updated inline style for the editor section -->
            <div id="blog-instruction-editor-section" style="display: <?php echo ($show_instruction === 'on') ? 'block' : 'none'; ?>;">
                <?php blog_instruction_editor_callback(); ?>
            </div>

            <?php submit_button(); ?>
        </form>

        <!-- Include jQuery script -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            jQuery(document).ready(function($) {
                // Add a click event listener to the toggle checkbox
                $('#blog_instruction_show').on('click', function() {
                    // Check the state of the toggle
                    var isChecked = $(this).is(':checked');

                    // Update the visibility of the editor section
                    $('#blog-instruction-editor-section').toggle(isChecked);
                });
            });
        </script>
    </div>
    <?php
}



// Settings initialization
function blog_instruction_settings_init() {
    register_setting('blog_instruction_settings_group', 'blog_instruction_text');
    
    // Save the new option
    register_setting('blog_instruction_settings_group', 'blog_instruction_show');
}


// Admin menu
function blog_instruction_add_menu() {
    $allowed_roles = array('administrator', 'editor');
    $current_user = wp_get_current_user();

    if (array_intersect($allowed_roles, $current_user->roles)) {
        add_options_page(
            'Blog Instruction Settings',
            'Blog Instruction',
            'manage_options',
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
    $show_instruction = get_option('blog_instruction_show', 'off');

    if ($show_instruction === 'on') {
        $instruction_text = get_option('blog_instruction_text', '');
        echo wpautop(do_shortcode($instruction_text));
    }
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
add_action('admin_init', 'blog_instruction_check_required_plugins');
add_action('admin_init', 'blog_instruction_settings_init');
add_action('admin_menu', 'blog_instruction_add_menu');
add_action('add_meta_boxes', 'blog_instruction_add_meta_box');
add_action('admin_enqueue_scripts', 'blog_instruction_enqueue_scripts');

// Add the editor to the settings page
add_action('blog_instruction_settings_page', 'blog_instruction_editor_callback');
