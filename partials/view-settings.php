<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Settings — tab dispatcher (Profiles / Destinations / Cron / Webhooks / General).
 *
 * @package Red_Headed_Lite
 */
$tabs = array(
    'profiles'     => array( 'icon' => '📁', 'label' => __( 'Profiles',     'red-headed-lite' ), 'lock' => null ),
    'destinations' => array( 'icon' => '📡', 'label' => __( 'Destinations', 'red-headed-lite' ), 'lock' => null ),
    'cron'         => array( 'icon' => '⏰', 'label' => __( 'Cron',         'red-headed-lite' ), 'lock' => 'cron' ),
    'webhooks'     => array( 'icon' => '🔔', 'label' => __( 'Webhooks',     'red-headed-lite' ), 'lock' => 'webhooks' ),
    'general'      => array( 'icon' => '⚙️', 'label' => __( 'General',      'red-headed-lite' ), 'lock' => null ),
);
$active = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'profiles';
if ( ! isset( $tabs[ $active ] ) ) $active = 'profiles';
?>
<?php
/* Charte v1 §4 — canonical Hub cockpit (header + .fh-tab nav + 2-col grid).
   Inner sub-tabs (.pl-tabs) keep the pill pattern, active = orange (charte §5B). */
Red_Headed_Admin::open_cockpit_shell( 'red-headed-lite-settings' );
FH_UI_Helper::open_cockpit_main();
?>

    <section class="pl-section">
        <h2 class="pl-h2"><?php esc_html_e( '⚙️ Settings', 'red-headed-lite' ); ?></h2>

        <nav class="pl-tabs" role="tablist">
            <?php foreach ( $tabs as $slug => $meta ) :
                $url = admin_url( 'admin.php?page=red-headed-lite-settings-' . $slug );
                $cur = $slug === $active ? 'pl-tab-active' : '';
                $locked = $meta['lock'] && Red_Headed_Soft_Lock::is_locked( $meta['lock'] );
            ?>
                <a href="<?php echo esc_url( $url ); ?>" class="pl-tab <?php echo $cur; ?> <?php echo $locked ? 'pl-tab-locked' : ''; ?>">
                    <span class="pl-tab-icon"><?php echo $meta['icon']; ?></span>
                    <span class="pl-tab-label"><?php echo esc_html( $meta['label'] ); ?></span>
                    <?php if ( $locked ) echo wp_kses_post( Red_Headed_Soft_Lock::badge() ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="pl-tab-pane">
            <?php
            $part = RED_HEADED_PATH . 'partials/settings/tab-' . $active . '.php';
            if ( file_exists( $part ) ) include $part;
            ?>
        </div>
    </section>
<?php
/* ── SIDEBAR : CTA / navigation only. ── */
FH_UI_Helper::close_cockpit_main();
FH_UI_Helper::open_cockpit_sidebar( __( 'Quick actions', 'red-headed-lite' ) );
FH_UI_Helper::render_sidebar_card( __( 'Quick actions', 'red-headed-lite' ), array(
    array( 'icon' => '📊', 'label' => __( 'Dashboard', 'red-headed-lite' ), 'url' => admin_url( 'admin.php?page=red-headed-lite' ) ),
    array( 'icon' => '📦', 'label' => __( 'Exports', 'red-headed-lite' ),   'url' => admin_url( 'admin.php?page=red-headed-lite-exports' ) ),
    array( 'icon' => '🛒', 'label' => __( 'WC Orders', 'red-headed-lite' ), 'url' => admin_url( 'edit.php?post_type=shop_order' ) ),
) );
FH_UI_Helper::close_cockpit_sidebar();
FH_UI_Helper::close_cockpit();
?>
