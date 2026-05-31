<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Bulk action on the WC orders list — "🃏 Export selected orders (Harlequin)".
 * Lite + Pro both expose the action; Lite is capped to CSV + 1 destination.
 *
 * @package Red_Headed_Lite
 */
class Red_Headed_Bulk_Actions {
    public function __construct() {
        add_filter( 'bulk_actions-edit-shop_order', array( $this, 'register_bulk' ) );
        add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk' ), 10, 3 );
        add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'register_bulk' ) );
        add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'handle_bulk' ), 10, 3 );
        add_action( 'admin_notices', array( $this, 'maybe_render_result' ) );
    }
    public function register_bulk( $actions ) {
        $actions['red_headed_export'] = __( '🃏 Export with Red-Headed', 'red-headed-lite' );
        return $actions;
    }
    public function handle_bulk( $redirect, $action, $ids ) {
        if ( $action !== 'red_headed_export' ) return $redirect;
        $order_ids = array_map( function ( $id ) {
            return is_object( $id ) ? (int) $id->get_id() : (int) $id;
        }, (array) $ids );
        $profile = array(
            'name'         => 'Bulk export ' . date( 'Ymd-His' ),
            'format'       => 'csv',
            'filters'      => array( 'order_ids_override' => $order_ids ),
            'columns'      => Red_Headed_Export_Engine::default_columns(),
            'destinations' => array(),  /* download from disk via Exports page */
        );
        $job = Red_Headed_Export_Engine::run( $profile, 'bulk_action' );
        return add_query_arg(
            is_wp_error( $job )
                ? array( 'red_headed_bulk' => 0, 'red_headed_err' => urlencode( $job->get_error_message() ) )
                : array( 'red_headed_bulk' => 1, 'red_headed_job' => (int) $job ),
            $redirect
        );
    }
    public function maybe_render_result() {
        if ( empty( $_GET['red_headed_bulk'] ) ) return;
        if ( $_GET['red_headed_bulk'] === '1' ) {
            $job_id = (int) $_GET['red_headed_job'];
            $url    = admin_url( 'admin.php?page=red-headed-lite-exports' );
            echo '<div class="notice notice-success is-dismissible"><p>';
            printf(
                /* translators: 1: job id, 2: link to exports list */
                esc_html__( '✓ Red-Headed export #%1$d created. %2$s', 'red-headed-lite' ),
                $job_id,
                '<a href="' . esc_url( $url ) . '" class="button button-primary" style="margin-left:6px;">' . esc_html__( 'Open Exports list', 'red-headed-lite' ) . '</a>'
            );
            echo '</p></div>';
        } else {
            $err = sanitize_text_field( wp_unslash( $_GET['red_headed_err'] ?? 'unknown' ) );
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( '⚠ Red-Headed export failed: ' . $err ) . '</p></div>';
        }
    }
}
