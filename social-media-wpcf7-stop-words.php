<?php
/*
  Plugin Name: Social Media WPCF7 Stop Words
  Description: A plugin developed by Social Media Ltd, to prevent form submission when the message contains custom predefined words.
  Version:     1.1.3
  Author:      Social Media Ltd
  Author URL:  http://social-media.co.uk
  License:     GPL2
 */

defined('ABSPATH') or die('No script kiddies please!');

register_activation_hook(__FILE__, 'smWpCfSwCheckInstallation');

function smWpCfSwCheckInstallation() {
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php'))
        wp_die(__('Contact Form 7 needs to be installed and activated before activating this plugin'));

    global $wpdb;

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}smwpcfsw_blocked (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time int NOT NULL,
                email varchar(255) DEFAULT NULL,
                message text NOT NULL,
                PRIMARY KEY id (id)
            ) {$wpdb->get_charset_collate()};";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

add_action('admin_menu', 'smWpCfSwMenu');

function smWpCfSwMenu() {
    add_menu_page(__('Blocked messages'), __('WPCF7 Stop Words'), 'manage_options', 'wpcf7-stop-words', 'smWpcf7BlockedMessagesPage', 'dashicons-dismiss', 80);
	add_submenu_page('wpcf7-stop-words', __('Settings'), __('Settings'), 'manage_options', 'wpcf7-stop-words-options', 'smWpCfSwOptions');
}

function smWpCfSwOptions() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if (filter_input(INPUT_POST, 'Submit') != null) {
        update_option('sm_wpcf7_words', json_encode(explode(PHP_EOL, filter_input(INPUT_POST, 'words', FILTER_SANITIZE_STRING))));
        update_option('sm_wpcf7_prune', filter_input(INPUT_POST, 'prune', FILTER_SANITIZE_NUMBER_INT));

        echo '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
    }

    $words = json_decode(get_option('sm_wpcf7_words'), true);
    $prune = get_option('sm_wpcf7_prune');

    echo '<div class="wrap">';
    echo '<h1>' . __('WPCF7 Stop Words') . '</h1>';
    echo '<form method="post" role="form">';
    echo '<p>';
    echo __("Words (place each word or phrase on a separate line");
    echo '</p>';
    echo '<textarea name="words" rows="7" cols="100">' . implode(PHP_EOL, (array) $words) . '</textarea>';
    echo '<p>';
    echo __("Delete stored blocked messages");
    echo '</p>';
    echo '<select name="prune">';
    echo '<option ' . ($prune == -1 ? 'selected="selected"' : null) . ' value="-1">Never</option>';
    echo '<option ' . ($prune == 1 ? 'selected="selected"' : null) . ' value="1">Every month</option>';
    echo '<option ' . ($prune == 3 ? 'selected="selected"' : null) . ' value="3">Every 3 months</option>';
    echo '<option ' . ($prune == 6 ? 'selected="selected"' : null) . ' value="6">Every 6 months</option>';
    echo '</select>';
    echo '<p class="submit">';
    echo '<input type="submit" name="Submit" class="button-primary" value="' . __('Save Changes') . '" />';
    echo '</p>';
    echo '</form>';
    echo '</div>';
}

function smWpcf7BlockedMessagesPage() {
    global $wpdb;
    $messages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}smwpcfsw_blocked ORDER BY time DESC");

    require plugin_dir_path(__FILE__) . "/blocked-messages-page.php";
}

add_filter('wpcf7_validate_textarea', 'smWpcf7FilterContent', 20, 2);

function smWpcf7FilterContent($result, $tag) {
    global $wpdb;
    $tag = new WPCF7_Shortcode($tag);
    
    $message = isset($_POST["{$tag->name}"]) ? trim($_POST["{$tag->name}"]) : '';
    $words = get_option('sm_wpcf7_words');
	if ($words)
		$words = json_decode($words, true);

    $invalid = false;
	foreach ($words as $word) {
		if (preg_match("#{$word}#iu", $message)) {
            $result->invalidate($tag, "We believe this is a SPAM message! We are not willing to accept SPAM messages");
            $invalid = true;
			break;
		}
    }

	if ($invalid) {
		$wpdb->insert("{$wpdb->prefix}smwpcfsw_blocked", array(
            'time' => time(),
            'email' => filter_input(INPUT_POST, 'your-email', FILTER_SANITIZE_EMAIL),
			'message' => $message
		));
	}

    return $result;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'smWpcf7ActionLinks');

function smWpcf7ActionLinks($links) {
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=wpcf7-stop-words-options')) . '">Settings</a>';
    return $links;
}

add_action('init', 'smWpcf7PruneMessages');

function smWpcf7PruneMessages() {
    $period = get_option('sm_wpcf7_prune', -1);
    if ($period == -1)
        return;
    
    global $wpdb;
    $timestamp = strtotime("- {$period} months");
    $wpdb->query("DELETE FROM {$wpdb->prefix}smwpcfsw_blocked WHERE time <= {$timestamp}");
}
?>