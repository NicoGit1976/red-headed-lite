<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Settings → General tab. Global plugin behavior.
 *
 * @package Red_Headed_Lite
 */
if ( isset( $_POST['rh_general_save'] ) && check_admin_referer( 'rh_general_save' ) && current_user_can( 'manage_woocommerce' ) ) {
    update_option( 'red_headed_retention_days', max( 0, (int) ( $_POST['retention_days'] ?? 30 ) ) );
    update_option( 'red_headed_default_filename_pattern', sanitize_text_field( $_POST['filename_pattern'] ?? 'orders-{{date}}-{{time}}' ) );
    update_option( 'red_headed_decimal_separator', $_POST['decimal_sep'] === 'comma' ? 'comma' : 'dot' );
    update_option( 'red_headed_email_subject', sanitize_text_field( $_POST['email_subject'] ?? '' ) );
    update_option( 'red_headed_email_body',    wp_kses_post(        $_POST['email_body']    ?? '' ) );
    update_option( 'red_headed_notify_on_failure',         ! empty( $_POST['notify_on_failure'] ) ? 1 : 0 );
    update_option( 'red_headed_notify_recipients',         sanitize_text_field( $_POST['notify_recipients']     ?? '' ) );
    update_option( 'red_headed_notify_subject',            sanitize_text_field( $_POST['notify_subject']        ?? '' ) );
    update_option( 'red_headed_register_wc_status_exported', ! empty( $_POST['register_wc_status_exported'] ) ? 1 : 0 );
    update_option( 'red_headed_uninstall_clean', ! empty( $_POST['uninstall_clean'] ) ? 1 : 0 );
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '✓ Settings saved.', 'red-headed-lite' ) . '</p></div>';
}

if ( isset( $_POST['rh_purge_jobs'] ) && check_admin_referer( 'rh_purge_jobs' ) && current_user_can( 'manage_woocommerce' ) ) {
    global $wpdb;
    $jobs_tbl = $wpdb->prefix . 'rh_jobs';
    $deleted = (int) $wpdb->query( "DELETE FROM {$jobs_tbl} WHERE status = 'success' AND finished_at < DATE_SUB(NOW(), INTERVAL 90 DAY)" );
    echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( esc_html__( '✓ Purged %d old jobs.', 'red-headed-lite' ), $deleted ) . '</p></div>';
}
?>
<div class="pl-pane">
    <h3 class="pl-h3">⚙️ <?php esc_html_e( 'General settings', 'red-headed-lite' ); ?></h3>

    <form method="post" class="pl-form">
        <?php wp_nonce_field( 'rh_general_save' ); ?>

        <fieldset class="pl-card">
            <legend class="pl-card-title"><?php esc_html_e( 'File defaults', 'red-headed-lite' ); ?></legend>

            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Filename pattern', 'red-headed-lite' ); ?></span>
                <input type="text" name="filename_pattern" value="<?php echo esc_attr( get_option( 'red_headed_default_filename_pattern', 'orders-{{date}}-{{time}}' ) ); ?>" />
                <small class="pl-muted"><?php esc_html_e( 'Tokens: {{date}} {{time}} {{records}} {{format}} {{profile}}', 'red-headed-lite' ); ?></small>
            </label>

            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Decimal separator', 'red-headed-lite' ); ?></span>
                <select name="decimal_sep">
                    <option value="dot"   <?php selected( get_option( 'red_headed_decimal_separator', 'dot' ), 'dot' ); ?>>. (<?php esc_html_e( 'dot — international', 'red-headed-lite' ); ?>)</option>
                    <option value="comma" <?php selected( get_option( 'red_headed_decimal_separator', 'dot' ), 'comma' ); ?>>, (<?php esc_html_e( 'comma — France/EU', 'red-headed-lite' ); ?>)</option>
                </select>
            </label>

            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Retention (days)', 'red-headed-lite' ); ?></span>
                <input type="number" min="0" name="retention_days" value="<?php echo (int) get_option( 'red_headed_retention_days', 30 ); ?>" />
                <small class="pl-muted"><?php esc_html_e( 'Files older than this are auto-deleted from /uploads/red-headed-lite/exports. 0 = keep forever.', 'red-headed-lite' ); ?></small>
            </label>
        </fieldset>

        <fieldset class="pl-card">
            <legend class="pl-card-title">🏷️ <?php esc_html_e( 'Custom order status', 'red-headed-lite' ); ?> <?php echo wp_kses_post( Red_Headed_Soft_Lock::badge() ); ?></legend>
            <p class="pl-muted"><?php esc_html_e( 'Add an "Exported" status to WooCommerce. Use it as the post-export action target so already-exported orders are easy to filter out.', 'red-headed-lite' ); ?></p>
            <label class="pl-checkbox">
                <input type="checkbox" name="register_wc_status_exported" value="1"
                    <?php checked( (int) get_option( 'red_headed_register_wc_status_exported', 0 ), 1 ); ?>
                    <?php disabled( ! Red_Headed_Soft_Lock::is_available( 'wc_status_exported' ) ); ?> />
                <span><?php esc_html_e( 'Register the "📦 Exported" custom WooCommerce order status (wc-rh-exported)', 'red-headed-lite' ); ?></span>
            </label>
        </fieldset>

        <fieldset class="pl-card">
            <legend class="pl-card-title">🔔 <?php esc_html_e( 'Failed-export notifications', 'red-headed-lite' ); ?></legend>
            <p class="pl-muted">
                <?php esc_html_e( 'Get an email when an export job fails (run error or destination delivery error).', 'red-headed-lite' ); ?>
            </p>
            <label class="pl-checkbox">
                <input type="checkbox" name="notify_on_failure" value="1" <?php checked( (int) get_option( 'red_headed_notify_on_failure', 0 ), 1 ); ?> />
                <span><?php esc_html_e( 'Send a notification email on failure', 'red-headed-lite' ); ?></span>
            </label>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Recipients (comma-separated)', 'red-headed-lite' ); ?></span>
                <input type="text" name="notify_recipients" value="<?php echo esc_attr( get_option( 'red_headed_notify_recipients', get_option( 'admin_email' ) ) ); ?>" placeholder="ops@example.com, dev@example.com" />
            </label>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Subject (optional)', 'red-headed-lite' ); ?></span>
                <input type="text" name="notify_subject" value="<?php echo esc_attr( get_option( 'red_headed_notify_subject', '⚠ Red-Headed export failed — job #{{job_id}}' ) ); ?>" />
                <small class="pl-muted"><?php esc_html_e( 'Tokens: {{job_id}} {{profile}} {{site}}', 'red-headed-lite' ); ?></small>
            </label>
        </fieldset>

        <fieldset class="pl-card">
            <legend class="pl-card-title">✉️ <?php esc_html_e( 'Default email template', 'red-headed-lite' ); ?></legend>
            <p class="pl-muted">
                <?php esc_html_e( 'Translatable via PolyLang & WPML — use the “Red_Headed_Lite” string group in the language admin.', 'red-headed-lite' ); ?>
            </p>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Subject', 'red-headed-lite' ); ?></span>
                <input type="text" name="email_subject" value="<?php echo esc_attr( get_option( 'red_headed_email_subject', '🃏 Red-Headed export — {{filename}}' ) ); ?>" />
            </label>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Body', 'red-headed-lite' ); ?></span>
                <textarea name="email_body" rows="5"><?php echo esc_textarea( get_option( 'red_headed_email_body', "Hi,\n\nYour Red-Headed export is ready: {{filename}} ({{records}} orders).\n\n— The Lion Frog" ) ); ?></textarea>
            </label>
        </fieldset>

        <fieldset class="pl-card">
            <legend class="pl-card-title">🧹 <?php esc_html_e( 'Data hygiene', 'red-headed-lite' ); ?></legend>
            <label class="pl-checkbox">
                <input type="checkbox" name="uninstall_clean" value="1" <?php checked( (int) get_option( 'red_headed_uninstall_clean', 1 ), 1 ); ?> />
                <span><?php esc_html_e( 'Clean on uninstall', 'red-headed-lite' ); ?></span>
            </label>
            <p class="pl-muted">
                <?php esc_html_e( 'When ON (default), deleting Red-Headed from Plugins removes its options (settings, webhooks), the rh_profiles + rh_jobs tables and the red_headed_cron_tick cron. Tables are preserved if the sister edition (Pro/Lite) is still installed. Turn OFF if you plan to upgrade to Pro or reinstall later.', 'red-headed-lite' ); ?>
            </p>
        </fieldset>

        <p>
            <button type="submit" name="rh_general_save" class="pl-btn pl-btn-primary"><?php esc_html_e( '💾 Save settings', 'red-headed-lite' ); ?></button>
        </p>
    </form>

    <hr style="margin:24px 0;border:none;border-top:1px solid var(--pl-border)" />

    <fieldset class="pl-card">
        <legend class="pl-card-title">🧹 <?php esc_html_e( 'Maintenance', 'red-headed-lite' ); ?></legend>
        <form method="post" style="margin:0">
            <?php wp_nonce_field( 'rh_purge_jobs' ); ?>
            <p><?php esc_html_e( 'Delete success jobs older than 90 days from the log.', 'red-headed-lite' ); ?></p>
            <button type="submit" name="rh_purge_jobs" class="pl-btn" onclick="return confirm('<?php echo esc_js( __( 'Purge old job rows? This cannot be undone.', 'red-headed-lite' ) ); ?>');"><?php esc_html_e( 'Purge old jobs', 'red-headed-lite' ); ?></button>
        </form>
    </fieldset>

    <fieldset class="pl-card">
        <legend class="pl-card-title">ℹ️ <?php esc_html_e( 'About', 'red-headed-lite' ); ?></legend>
        <p><strong>Red-Headed</strong> v<?php echo esc_html( RED_HEADED_VERSION ); ?> — <?php echo esc_html( strtoupper( Red_Headed_Soft_Lock::edition() ) ); ?></p>
        <p class="pl-muted"><?php esc_html_e( '🃏 Order Export pour WooCommerce — by The Lion Frog.', 'red-headed-lite' ); ?></p>
        <ul class="pl-muted">
            <li>📚 <?php esc_html_e( 'Docs', 'red-headed-lite' ); ?>: <a href="https://thelionfrog.com/docs/red-headed-lite" target="_blank" rel="noopener">thelionfrog.com/docs/red-headed-lite</a></li>
            <li>🐛 <?php esc_html_e( 'Support', 'red-headed-lite' ); ?>: <a href="https://thelionfrog.com/support" target="_blank" rel="noopener">thelionfrog.com/support</a></li>
        </ul>
    </fieldset>
</div>
