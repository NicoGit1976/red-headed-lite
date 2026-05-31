<?php
/**
 * Red-Headed Lite — Uninstall
 *
 * Fired when the plugin is deleted from wp-admin > Plugins > Delete.
 * Default behaviour: full data cleanup. Users can opt out via the
 * "Clean on uninstall" toggle in Settings > General > Data hygiene.
 *
 * Owned data: identical option set + rh_profiles / rh_jobs tables as
 * Red-Headed Pro (Lite → Pro upgrade preserves history).
 *
 * Tables are preserved if the sister edition (red-headed-pro) is still
 * installed.
 *
 * @package RedHeadedLite
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

wp_clear_scheduled_hook( 'red_headed_cron_tick' );

$clean = (int) get_option( 'red_headed_uninstall_clean', 1 );
if ( ! $clean ) {
    return;
}

global $wpdb;

$options = [
    'red_headed_settings',
    'red_headed_webhooks',
    'red_headed_decimal_separator',
    'red_headed_default_email_body',
    'red_headed_default_email_subject',
    'red_headed_default_email_to',
    'red_headed_default_filename_pattern',
    'red_headed_default_sftp_host',
    'red_headed_default_sftp_pass_enc',
    'red_headed_default_sftp_path',
    'red_headed_default_sftp_port',
    'red_headed_default_sftp_user',
    'red_headed_email_body',
    'red_headed_email_subject',
    'red_headed_notify_on_failure',
    'red_headed_notify_recipients',
    'red_headed_notify_subject',
    'red_headed_register_wc_status_exported',
    'red_headed_retention_days',
    'red_headed_uninstall_clean',
    'red_headed_db_version',
];
foreach ( $options as $opt ) {
    delete_option( $opt );
}

$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( 'red_headed_last_run_' ) . '%'
    )
);

$sister = WP_PLUGIN_DIR . '/red-headed-pro/red-headed-pro.php';
if ( ! file_exists( $sister ) ) {
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'rh_jobs' );
    $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'rh_profiles' );
}
