<?php
/**
 * Plugin Name: Custom Excerpt Shortener WP
 * Description: Shortens the content of entries and adds a "Read more" button.
 * Version: 1.0
 * Author: Dominik
 */

if (!defined('ABSPATH')) {
    exit; 
}


function ces_add_settings() {
    add_option('ces_excerpt_length', 200);
    add_option('ces_read_more_text', 'Read more'); 
    register_setting('ces_options_group', 'ces_excerpt_length', 'intval');
    register_setting('ces_options_group', 'ces_read_more_text', 'sanitize_text_field'); 
}
add_action('admin_init', 'ces_add_settings');

function ces_add_menu() {
    add_options_page('Custom Excerpt Shortener', 'Shortening entries', 'manage_options', 'ces-settings', 'ces_settings_page');
}
add_action('admin_menu', 'ces_add_menu');


function ces_settings_page() {
    ?>
    <div class="wrap">
        <h1>Ustawienia skracania wpisów</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ces_options_group');
            do_settings_sections('ces_options_group');
            ?>
            <label for="ces_excerpt_length">Abbreviation length (characters):</label>
            <input type="number" name="ces_excerpt_length" value="<?php echo esc_attr(get_option('ces_excerpt_length', 100)); ?>" min="10" max="1000" />
            <br><br>
            <label for="ces_read_more_text">"Read more or Czytaj więcej" button text:</label>
            <input type="text" name="ces_read_more_text" value="<?php echo esc_attr(get_option('ces_read_more_text', 'Read more')); ?>" />
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


function ces_shorten_content($content) {
    if (is_single() || is_admin()) return $content; 
    
    $length = absint(get_option('ces_excerpt_length', 100));
    $read_more_text = esc_html(get_option('ces_read_more_text', 'Read more')); 
    $trimmed_content = wp_trim_words($content, $length, '...');
    
    return $trimmed_content . ' <a href="' . esc_url(get_permalink()) . '">' . $read_more_text . '</a>';
}
add_filter('the_content', 'ces_shorten_content');
