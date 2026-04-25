<?php
/**
 * Plugin Name: Nalli MegaMenu
 * Description: Advanced Elementor Header MegaMenu widget with scrolling announcement bar, sticky header, and image-based mega menu.
 * Version: 1.0.0
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: nalli-megamenu
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

define( 'NMM_VERSION', '1.0.8' );
define( 'NMM_FILE', __FILE__ );
define( 'NMM_PATH', plugin_dir_path( __FILE__ ) );
define( 'NMM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Core initialization — hooked at priority 20 so Elementor (priority 10) is already loaded.
 */
function nalli_megamenu_init() {

	if ( ! did_action( 'elementor/loaded' ) ) {
		// Elementor not yet loaded at this priority: show notice if we are in admin.
		add_action( 'admin_notices', 'nalli_megamenu_missing_elementor_notice' );
		return;
	}

	// Register assets (do NOT enqueue — let Elementor handle that via get_style/script_depends).
	add_action( 'wp_enqueue_scripts', 'nalli_megamenu_register_assets' );

	// Also register assets for the Elementor editor/preview iframe.
	add_action( 'elementor/frontend/after_register_scripts', 'nalli_megamenu_register_assets' );
	add_action( 'elementor/editor/after_enqueue_styles', 'nalli_megamenu_editor_enqueue' );
	add_action( 'elementor/preview/enqueue_styles', 'nalli_megamenu_editor_enqueue' );
	add_action( 'elementor/preview/enqueue_scripts', 'nalli_megamenu_editor_enqueue' );

	// Register custom widget category.
	add_action( 'elementor/elements/categories_registered', 'nalli_megamenu_register_category' );

	// Register widget.
	add_action( 'elementor/widgets/register', 'nalli_megamenu_register_widgets' );
}
// Priority 20 ensures Elementor (which hooks at default priority 10) is already loaded.
add_action( 'plugins_loaded', 'nalli_megamenu_init', 20 );

/**
 * Admin notice shown when Elementor is not installed/active.
 */
function nalli_megamenu_missing_elementor_notice() {
	$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'nalli-megamenu' ),
		'<strong>' . esc_html__( 'Nalli MegaMenu', 'nalli-megamenu' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'nalli-megamenu' ) . '</strong>'
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
}

/**
 * Register frontend CSS and JS (not enqueued globally — only when widget is present).
 */
function nalli_megamenu_register_assets() {
	wp_register_style(
		'nalli-megamenu-style',
		NMM_URL . 'assets/css/header-megamenu.css',
		[],
		NMM_VERSION
	);

	// Main script — depends on elementor-frontend (used when the widget is on the page).
	wp_register_script(
		'nalli-megamenu-script',
		NMM_URL . 'assets/js/header-megamenu.js',
		[ 'jquery', 'elementor-frontend' ],
		NMM_VERSION,
		true
	);

	// Standalone script for pages WITHOUT the Elementor widget (e.g. wishlist page).
	// Same file, but depends only on jQuery so it always loads correctly.
	wp_register_script(
		'nalli-megamenu-script-standalone',
		NMM_URL . 'assets/js/header-megamenu.js',
		[ 'jquery' ],
		NMM_VERSION,
		true
	);

	$wc_ajax_url = class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : admin_url( 'admin-ajax.php' );

	$localize_data = [
		'ajax_url'    => admin_url( 'admin-ajax.php' ),
		'wc_ajax_url' => $wc_ajax_url,
		'nonce'       => wp_create_nonce( 'nmm_wishlist_nonce' ),
	];

	wp_localize_script( 'nalli-megamenu-script', 'nmm_ajax', $localize_data );
	wp_localize_script( 'nalli-megamenu-script-standalone', 'nmm_ajax', $localize_data );

	// Force-enqueue the standalone script on any page that contains the wishlist shortcode.
	nalli_megamenu_maybe_enqueue_wishlist();
}

/**
 * Enqueue the standalone script + style on pages containing the wishlist shortcode.
 * This ensures the wishlist JS works even when the Elementor megamenu widget
 * is NOT present on that page (which is the common case for a dedicated wishlist page).
 */
function nalli_megamenu_maybe_enqueue_wishlist() {
	global $post;
	if ( ! is_a( $post, 'WP_Post' ) ) return;

	$has_shortcode = has_shortcode( $post->post_content, 'nmm_wishlist_page' )
		|| has_shortcode( $post->post_content, 'mm_wishlist_page' );

	if ( $has_shortcode ) {
		wp_enqueue_style( 'nalli-megamenu-style' );
		wp_enqueue_script( 'nalli-megamenu-script-standalone' );
	}
}

/**
 * Force-enqueue the stylesheet in Elementor editor/preview context.
 */
function nalli_megamenu_editor_enqueue() {
	wp_enqueue_style(
		'nalli-megamenu-style',
		NMM_URL . 'assets/css/header-megamenu.css',
		[],
		NMM_VERSION
	);
}

/**
 * Register the "Nalli MegaMenu" widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager
 */
function nalli_megamenu_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'nalli-megamenu',
		[
			'title' => esc_html__( 'Nalli MegaMenu', 'nalli-megamenu' ),
			'icon'  => 'fa fa-plug',
		]
	);
}

/**
 * Register the Header MegaMenu widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager
 */
function nalli_megamenu_register_widgets( $widgets_manager ) {
	require_once NMM_PATH . 'widgets/header-megamenu.php';
	$widgets_manager->register( new \Nalli_MegaMenu\Widgets\Header_MegaMenu() );
}

/**
 * AJAX Handler for Live Product Search
 */
add_action( 'wp_ajax_nmm_ajax_search', 'nalli_megamenu_ajax_search' );
add_action( 'wp_ajax_nopriv_nmm_ajax_search', 'nalli_megamenu_ajax_search' );

function nalli_megamenu_ajax_search() {
	if ( ! isset( $_GET['q'] ) || empty( $_GET['q'] ) ) {
		wp_send_json_success( [] );
	}

	$query = sanitize_text_field( $_GET['q'] );
	$type  = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'product';
	
	$results = [];

	// Search Categories if needed
	if ( $type === 'category' || $type === 'both' ) {
		$terms = get_terms( [
			'taxonomy'   => 'product_cat',
			'name__like' => $query,
			'hide_empty' => true,
			'number'     => 3,
		] );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image        = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' ) : '';

				$results[] = [
					'title' => $term->name . ' (Category)',
					'url'   => get_term_link( $term ),
					'image' => $image,
					'price' => '', // Categories don't have prices
				];
			}
		}
	}

	// Search Products if needed
	if ( $type === 'product' || $type === 'both' ) {
		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			's'              => $query,
		];

		$query_obj = new WP_Query( $args );

		if ( $query_obj->have_posts() ) {
			while ( $query_obj->have_posts() ) {
				$query_obj->the_post();
				global $product;
				
				$price = '';
				if ( $product ) {
					$price = $product->get_price_html();
				}

				$results[] = [
					'title' => get_the_title(),
					'url'   => get_permalink(),
					'image' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
					'price' => $price,
				];
			}
			wp_reset_postdata();
		}
	}

	wp_send_json_success( $results );
}



/**
 * Update WooCommerce Cart Count via AJAX
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'nalli_megamenu_cart_count_fragments', 10, 1 );
function nalli_megamenu_cart_count_fragments( $fragments ) {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$fragments['span.nmm-cart-count'] = '<span class="nmm-cart-count" aria-label="' . esc_attr__( 'Cart items', 'nalli-megamenu' ) . '">' . WC()->cart->get_cart_contents_count() . '</span>';
	}
	return $fragments;
}

/**
 * Built-in Wishlist Functionality
 */
add_action( 'woocommerce_after_add_to_cart_button', 'nmm_add_wishlist_button', 15 );
function nmm_add_wishlist_button() {
    global $product;
    if ( ! $product ) return;
    $product_id = $product->get_id();
    echo '<button type="button" class="nmm-wishlist-btn" data-product-id="' . esc_attr( $product_id ) . '" title="' . esc_attr__( 'Add to Wishlist', 'nalli-megamenu' ) . '">
        <svg class="nmm-heart-icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span class="nmm-wishlist-btn-text">' . esc_html__( 'Add to Wishlist', 'nalli-megamenu' ) . '</span>
    </button>';
}

/**
 * Wishlist Page Shortcode
 */
add_shortcode( 'nmm_wishlist_page', 'nmm_wishlist_page_shortcode' );
add_shortcode( 'mm_wishlist_page', 'nmm_wishlist_page_shortcode' ); // alias for backward compat
function nmm_wishlist_page_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) ) return '<p>' . esc_html__( 'WooCommerce is required for the wishlist.', 'nalli-megamenu' ) . '</p>';

    // Ensure the script is enqueued even if shortcode is loaded dynamically.
    wp_enqueue_style( 'nalli-megamenu-style' );
    wp_enqueue_script( 'nalli-megamenu-script-standalone' );

    return '<div id="nmm-wishlist-container" class="woocommerce"><div class="nmm-search-loading" style="text-align:center; padding:50px;">' . esc_html__( 'Loading wishlist...', 'nalli-megamenu' ) . '</div></div>';
}

/**
 * AJAX Handler to Render Wishlist Products
 */
add_action( 'wp_ajax_nmm_render_wishlist', 'nmm_render_wishlist_ajax' );
add_action( 'wp_ajax_nopriv_nmm_render_wishlist', 'nmm_render_wishlist_ajax' );
function nmm_render_wishlist_ajax() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        wp_send_json_error( 'WooCommerce not active' );
    }

    $ids = isset( $_POST['product_ids'] ) ? array_map( 'intval', $_POST['product_ids'] ) : [];
    
    if ( empty( $ids ) ) {
        wp_send_json_success( '
        <div class="nmm-wishlist-empty-state" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); max-width: 600px; margin: 40px auto;">
            <svg viewBox="0 0 24 24" width="64" height="64" stroke="#ddd" stroke-width="1.5" fill="none" style="margin-bottom: 20px;">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Your wishlist is empty</h3>
            <p style="color: #666; margin-bottom: 24px;">Explore our collections and add your favorite items!</p>
            <a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="button" style="background: #e02b27; color: #fff; border-radius: 4px; padding: 12px 24px;">Browse Products</a>
        </div>' );
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'post__in'       => $ids,
        'posts_per_page' => -1,
        'orderby'        => 'post__in'
    ];

    $query = new WP_Query( $args );
    
    // Add a temporary hook to inject the remove button on the product card
    $inject_remove_btn = function() {
        global $product;
        echo '<button class="nmm-wishlist-remove" data-product-id="' . esc_attr( $product->get_id() ) . '" aria-label="' . esc_attr__( 'Remove from wishlist', 'nalli-megamenu' ) . '">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        </button>';
    };
    add_action( 'woocommerce_before_shop_loop_item', $inject_remove_btn, 5 );

    ob_start();
    
    echo '<div class="nmm-wishlist-layout" style="padding: 20px 0;">';
    echo '<div class="nmm-wishlist-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px;">';
    echo '<h2 style="margin: 0; font-size: 28px; color: #333;">My Favorites</h2>';
    echo '<span style="color: #666; font-size: 16px;">' . count( $ids ) . ' items</span>';
    echo '</div>';

    if ( $query->have_posts() ) {
        woocommerce_product_loop_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();
        wp_reset_postdata();
    }
    echo '</div>';

    remove_action( 'woocommerce_before_shop_loop_item', $inject_remove_btn, 5 );

    $html = ob_get_clean();
    wp_send_json_success( $html );
}
