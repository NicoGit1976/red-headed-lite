<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Settings → Webhooks tab. Pro only.
 *
 * @package Red_Headed_Lite
 */
if ( Red_Headed_Soft_Lock::is_pro() ) {
    if ( isset( $_POST['rh_hook_add'] ) && check_admin_referer( 'rh_hook_add' ) && current_user_can( 'manage_woocommerce' ) ) {
        $url    = esc_url_raw( wp_unslash( $_POST['url']    ?? '' ) );
        $secret =     sanitize_text_field( wp_unslash( $_POST['secret'] ?? '' ) );
        $events = isset( $_POST['events'] ) ? array_map( 'sanitize_key', (array) $_POST['events'] ) : array();
        if ( Red_Headed_Webhooks::register_url( $url, $secret, $events ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '✓ Webhook saved.', 'red-headed-lite' ) . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( '⚠ Invalid URL — must be HTTPS.', 'red-headed-lite' ) . '</p></div>';
        }
    }
    if ( isset( $_POST['rh_hook_del'] ) && check_admin_referer( 'rh_hook_del' ) && current_user_can( 'manage_woocommerce' ) ) {
        Red_Headed_Webhooks::unregister_url( wp_unslash( $_POST['rh_hook_del'] ) );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '✓ Webhook removed.', 'red-headed-lite' ) . '</p></div>';
    }
}
?>
<div class="pl-pane">
    <h3 class="pl-h3">🔔 <?php esc_html_e( 'Webhooks', 'red-headed-lite' ); ?></h3>

    <?php if ( ! Red_Headed_Soft_Lock::is_pro() ) : ?>
        <div class="pl-locked-pane">
            <div class="pl-locked-icon">🔒</div>
            <h4><?php esc_html_e( 'Webhooks — Pro feature', 'red-headed-lite' ); ?></h4>
            <p class="pl-muted"><?php esc_html_e( 'Get notified by HTTP POST every time an export is generated, delivered, or fails. HMAC-signed payloads, per-endpoint event filter.', 'red-headed-lite' ); ?></p>
            <ul class="pl-list-check">
                <li>✓ <code>export.generated</code> · <code>export.delivered</code> · <code>export.failed</code></li>
                <li>✓ <?php esc_html_e( 'HMAC SHA-256 signature in X-Red_Headed_Lite-Signature', 'red-headed-lite' ); ?></li>
                <li>✓ <?php esc_html_e( 'Multiple endpoints, per-endpoint event subscription', 'red-headed-lite' ); ?></li>
            </ul>
            <a href="https://thelionfrog.com/products/plugins/woo-order-pro" target="_blank" rel="noopener" class="pl-btn pl-btn-upgrade">⚡ <?php esc_html_e( 'Upgrade to Pro', 'red-headed-lite' ); ?></a>
        </div>
    <?php else :
        $hooks = Red_Headed_Webhooks::list_urls();
    ?>
        <form method="post" class="pl-form pl-card">
            <?php wp_nonce_field( 'rh_hook_add' ); ?>
            <h4 class="pl-card-title"><?php esc_html_e( '+ Add webhook endpoint', 'red-headed-lite' ); ?></h4>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'URL (HTTPS only)', 'red-headed-lite' ); ?></span>
                <input type="url" name="url" required placeholder="https://example.com/webhooks/red-headed-lite" />
            </label>
            <label class="pl-field">
                <span class="pl-field-lbl"><?php esc_html_e( 'Secret (HMAC SHA-256)', 'red-headed-lite' ); ?></span>
                <input type="text" name="secret" placeholder="<?php esc_attr_e( 'Optional but recommended', 'red-headed-lite' ); ?>" />
            </label>
            <fieldset class="pl-field">
                <legend class="pl-field-lbl"><?php esc_html_e( 'Events', 'red-headed-lite' ); ?></legend>
                <?php foreach ( array( 'export.generated', 'export.delivered', 'export.failed' ) as $ev ) : ?>
                    <label class="pl-checkbox">
                        <input type="checkbox" name="events[]" value="<?php echo esc_attr( $ev ); ?>" checked />
                        <span><code><?php echo esc_html( $ev ); ?></code></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <p>
                <button type="submit" name="rh_hook_add" class="pl-btn pl-btn-primary"><?php esc_html_e( '💾 Save webhook', 'red-headed-lite' ); ?></button>
            </p>
        </form>

        <h4 class="pl-h3"><?php esc_html_e( 'Registered endpoints', 'red-headed-lite' ); ?></h4>
        <?php if ( empty( $hooks ) ) : ?>
            <p class="pl-muted">— <?php esc_html_e( 'None yet.', 'red-headed-lite' ); ?></p>
        <?php else : ?>
            <table class="pl-table pl-table-zebra">
                <thead><tr>
                    <th><?php esc_html_e( 'URL', 'red-headed-lite' ); ?></th>
                    <th><?php esc_html_e( 'Events', 'red-headed-lite' ); ?></th>
                    <th><?php esc_html_e( 'Secret', 'red-headed-lite' ); ?></th>
                    <th></th>
                </tr></thead>
                <tbody>
                    <?php foreach ( $hooks as $h ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $h['url'] ); ?></code></td>
                            <td><?php echo esc_html( implode( ', ', (array) ( $h['events'] ?? array() ) ) ); ?></td>
                            <td><?php echo ! empty( $h['secret'] ) ? '🔒 ' . esc_html__( 'set', 'red-headed-lite' ) : '—'; ?></td>
                            <td>
                                <form method="post" style="display:inline">
                                    <?php wp_nonce_field( 'rh_hook_del' ); ?>
                                    <button type="submit" name="rh_hook_del" value="<?php echo esc_attr( $h['url'] ); ?>" class="pl-btn pl-btn-sm pl-btn-danger" onclick="return confirm('<?php echo esc_js( __( 'Remove this webhook?', 'red-headed-lite' ) ); ?>');">×</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
