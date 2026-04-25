<?php
/**
 * Widget Class: Header MegaMenu
 * Part of the Nalli MegaMenu Elementor Addon.
 *
 * @package Nalli_MegaMenu
 */

namespace Nalli_MegaMenu\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( '\\Nalli_MegaMenu\\Widgets\\Header_MegaMenu' ) ) {
	return;
}

/**
 * Header_MegaMenu widget class.
 */
class Header_MegaMenu extends \Elementor\Widget_Base {

	/* ------------------------------------------------------------------
	 * Identity
	 * ------------------------------------------------------------------ */

	public function get_name() {
		return 'nalli_header_megamenu';
	}

	public function get_title() {
		return esc_html__( 'Header MegaMenu', 'nalli-megamenu' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'nalli-megamenu' ];
	}

	public function get_keywords() {
		return [ 'header', 'menu', 'mega', 'navigation', 'nalli' ];
	}

	public function get_style_depends() {
		return [ 'nalli-megamenu-style' ];
	}

	public function get_script_depends() {
		return [ 'nalli-megamenu-script' ];
	}

	/* ------------------------------------------------------------------
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {

		$this->register_content_announcement_bar();
		$this->register_content_header_layout();
		$this->register_content_nav_items();
		$this->register_content_megamenu_items();
		$this->register_content_megamenu_tabs();
		$this->register_content_utility_icons();
		$this->register_style_announcement_bar();
		$this->register_style_header();
		$this->register_style_megamenu();
		$this->register_style_search_box();
		$this->register_style_mobile_drawer();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 1: Announcement Bar
	 * =================================================================== */

	private function register_content_announcement_bar() {

		$this->start_controls_section(
			'section_announcement',
			[
				'label' => esc_html__( 'Announcement Bar', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ann_enable',
			[
				'label'        => esc_html__( 'Show Announcement Bar', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'nalli-megamenu' ),
				'label_off'    => esc_html__( 'No', 'nalli-megamenu' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'ann_text',
			[
				'label'       => esc_html__( 'Message', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Free shipping on orders above ₹2999', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'ann_link',
			[
				'label'         => esc_html__( 'Link (optional)', 'nalli-megamenu' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'show_external' => true,
				'default'       => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'ann_items',
			[
				'label'       => esc_html__( 'Ticker Messages', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'ann_text' => esc_html__( 'Free shipping on orders above ₹2999', 'nalli-megamenu' ) ],
					[ 'ann_text' => esc_html__( 'New Collection: Kanjivaram Wedding Silks', 'nalli-megamenu' ) ],
					[ 'ann_text' => esc_html__( 'Authentic Handloom Sarees Since 1928', 'nalli-megamenu' ) ],
				],
				'title_field' => '{{{ ann_text }}}',
				'condition'   => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'ann_speed',
			[
				'label'      => esc_html__( 'Scroll Speed (ms)', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 1000, 'max' => 10000, 'step' => 500 ],
				],
				'default'    => [ 'size' => 4000 ],
				'condition'  => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'ann_bg_color',
			[
				'label'     => esc_html__( 'Bar Background', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#8B1C2C',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-ann-bg: {{VALUE}};' ],
				'condition' => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'ann_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-ann-color: {{VALUE}};' ],
				'condition' => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'ann_text_typography',
				'label'     => esc_html__( 'Typography', 'nalli-megamenu' ),
				'selector'  => '{{WRAPPER}} .nmm-ann-item',
				'condition' => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'ann_padding',
			[
				'label'      => esc_html__( 'Bar Padding', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-ann-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'condition'  => [ 'ann_enable' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 2: Header Layout
	 * =================================================================== */

	private function register_content_header_layout() {

		$this->start_controls_section(
			'section_header_layout',
			[
				'label' => esc_html__( 'Header Layout', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'logo_image',
			[
				'label'   => esc_html__( 'Logo Image', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'logo_url',
			[
				'label'       => esc_html__( 'Logo Link', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'default'     => [ 'url' => '' ],
			]
		);

		$this->add_responsive_control(
			'logo_width',
			[
				'label'      => esc_html__( 'Logo Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 300 ] ],
				'default'    => [ 'size' => 100, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-logo-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'header_sticky',
			[
				'label'        => esc_html__( 'Sticky Header', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'nalli-megamenu' ),
				'label_off'    => esc_html__( 'No', 'nalli-megamenu' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'header_shadow',
			[
				'label'        => esc_html__( 'Drop Shadow on Scroll', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'header_sticky' => 'yes' ],
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label'     => esc_html__( 'Header Background', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-header-bg: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'header_height',
			[
				'label'      => esc_html__( 'Header Height', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 40, 'max' => 160 ] ],
				'default'    => [ 'size' => 70, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-header-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => esc_html__( 'Header Inner Padding', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-header-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'force_full_width',
			[
				'label'        => esc_html__( 'Force Full Viewport Width', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'nalli-megamenu' ),
				'label_off'    => esc_html__( 'No', 'nalli-megamenu' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Enable this to bypass container padding and force the widget to span the entire screen width.', 'nalli-megamenu' ),
				'prefix_class' => 'nmm-force-fw-',
			]
		);

		$this->add_control(
			'header_z_index',
			[
				'label'     => esc_html__( 'Z-Index', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 9999,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-header-z-index: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 3: Navigation Menu Items
	 * =================================================================== */

	private function register_content_nav_items() {

		$this->start_controls_section(
			'section_nav_items',
			[
				'label' => esc_html__( 'Navigation Menu Items', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'nav_label',
			[
				'label'       => esc_html__( 'Label', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Collections', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'nav_url',
			[
				'label'         => esc_html__( 'Link (optional)', 'nalli-megamenu' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => [ 'url' => '' ],
			]
		);

		$repeater->add_control(
			'nav_highlight',
			[
				'label'        => esc_html__( 'Highlight Accent', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater->add_control(
			'nav_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#8B1C2C',
				'condition' => [ 'nav_highlight' => 'yes' ],
			]
		);

		$repeater->add_control(
			'has_megamenu',
			[
				'label'        => esc_html__( 'Has Mega Menu?', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'nav_items',
			[
				'label'       => esc_html__( 'Menu Items', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'nav_label' => 'Sarees',      'has_megamenu' => 'yes' ],
					[ 'nav_label' => 'Collections', 'has_megamenu' => 'yes' ],
					[ 'nav_label' => 'Occasions',   'has_megamenu' => 'yes', 'nav_highlight' => 'yes' ],
					[ 'nav_label' => 'New Arrivals','has_megamenu' => '' ],
					[ 'nav_label' => 'Sale',        'has_megamenu' => '', 'nav_highlight' => 'yes' ],
				],
				'title_field' => '{{{ nav_label }}}',
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 4a: MegaMenu Items (flat repeater)
	 * =================================================================== */

	private function register_content_megamenu_items() {

		$this->start_controls_section(
			'section_megamenu_items',
			[
				'label' => esc_html__( 'MegaMenu Items', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'megamenu_items_note',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => '<div style="background:#fef9e7;padding:10px 12px;border-left:3px solid #d4a017;border-radius:3px;font-size:12px;line-height:1.5;">
					<strong style="display:block;margin-bottom:4px;">📌 How to add categories:</strong>
					<strong>Parent Menu</strong> → Type the exact nav item label (e.g. <code>Sarees</code>, <code>Collections</code>)<br>
					<strong>Tab Key</strong> → Type a tab slug (e.g. <code>wedding</code>, <code>silk</code>) to group this card under that tab.<br>
					<em>You can add unlimited categories to any menu and any tab!</em>
				</div>',
				'content_classes' => 'elementor-descriptor',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mm_parent_label',
			[
				'label'       => esc_html__( 'Parent Menu (Nav Label)', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Sarees',
				'description' => esc_html__( 'Type the EXACT label from Navigation Menu Items (e.g. Sarees, Collections, Occasions).', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'mm_tab_key',
			[
				'label'       => esc_html__( 'Tab Key (slug)', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'all',
				'description' => esc_html__( 'Must match a Filter Key in MegaMenu Sidebar Tabs. Use "all" to show in all tabs.', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'mm_image',
			[
				'label'   => esc_html__( 'Category Image', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$repeater->add_control(
			'mm_label',
			[
				'label'       => esc_html__( 'Category Label', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bridal', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'mm_url',
			[
				'label'         => esc_html__( 'Link', 'nalli-megamenu' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
				'default'       => [ 'url' => '#' ],
			]
		);

		$repeater->add_control(
			'mm_badge',
			[
				'label'   => esc_html__( 'Badge Text (optional)', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'mm_badge_color',
			[
				'label'   => esc_html__( 'Badge Color', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#8B1C2C',
			]
		);

		$this->add_control(
			'megamenu_items',
			[
				'label'       => esc_html__( 'Category Cards', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					// ── Sarees → Wedding ──
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'wedding',  'mm_label' => 'Bridal',       'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'wedding',  'mm_label' => 'Haldi',        'mm_url' => [ 'url' => '#' ], 'mm_badge' => 'New' ],
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'wedding',  'mm_label' => 'Mehendi',      'mm_url' => [ 'url' => '#' ] ],
					// ── Sarees → Festive ──
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'festive',  'mm_label' => 'Pongal',       'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'festive',  'mm_label' => 'Diwali',       'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'festive',  'mm_label' => 'Onam',         'mm_url' => [ 'url' => '#' ] ],
					// ── Sarees → Events ──
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'events',   'mm_label' => 'Engagement',   'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Sarees', 'mm_tab_key' => 'events',   'mm_label' => 'Reception',    'mm_url' => [ 'url' => '#' ], 'mm_badge' => 'Trending' ],
					// ── Collections → Silk ──
					[ 'mm_parent_label' => 'Collections', 'mm_tab_key' => 'silk',    'mm_label' => 'Kanjivaram',  'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Collections', 'mm_tab_key' => 'silk',    'mm_label' => 'Banarasi',    'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Collections', 'mm_tab_key' => 'silk',    'mm_label' => 'Mysore Silk',  'mm_url' => [ 'url' => '#' ] ],
					// ── Collections → Cotton ──
					[ 'mm_parent_label' => 'Collections', 'mm_tab_key' => 'cotton',  'mm_label' => 'Chettinad',   'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Collections', 'mm_tab_key' => 'cotton',  'mm_label' => 'Mangalgiri',  'mm_url' => [ 'url' => '#' ], 'mm_badge' => 'New' ],
					// ── Occasions → Party ──
					[ 'mm_parent_label' => 'Occasions', 'mm_tab_key' => 'party',    'mm_label' => 'Evening Wear', 'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Occasions', 'mm_tab_key' => 'party',    'mm_label' => 'Cocktail',    'mm_url' => [ 'url' => '#' ] ],
					// ── Occasions → Traditional ──
					[ 'mm_parent_label' => 'Occasions', 'mm_tab_key' => 'traditional', 'mm_label' => 'Pooja',      'mm_url' => [ 'url' => '#' ] ],
					[ 'mm_parent_label' => 'Occasions', 'mm_tab_key' => 'traditional', 'mm_label' => 'Temple',     'mm_url' => [ 'url' => '#' ] ],
				],
				'title_field' => '🏷️ {{{ mm_parent_label }}} → {{{ mm_tab_key }}} → {{{ mm_label }}}',
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 4b: MegaMenu Tabs (left sidebar)
	 * =================================================================== */

	private function register_content_megamenu_tabs() {

		$this->start_controls_section(
			'section_megamenu_tabs',
			[
				'label' => esc_html__( 'MegaMenu Sidebar Tabs', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$this->add_control(
			'megamenu_tabs_note',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => '<div style="background:#eaf7ed;padding:10px 12px;border-left:3px solid #2e7d32;border-radius:3px;font-size:12px;line-height:1.5;">
					<strong style="display:block;margin-bottom:4px;">📌 How tabs work:</strong>
					<strong>Parent Menu</strong> → Same nav label as above (e.g. <code>Sarees</code>)<br>
					<strong>Filter Key</strong> → The slug that links to category cards (e.g. <code>wedding</code>)<br>
					<em>No tabs? Categories show without sidebar filtering.</em>
				</div>',
				'content_classes' => 'elementor-descriptor',
			]
		);

		$repeater->add_control(
			'tab_parent_nav',
			[
				'label'       => esc_html__( 'Parent Menu (Nav Label)', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Sarees',
				'description' => esc_html__( 'EXACT label from Navigation Menu Items.', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_label',
			[
				'label'       => esc_html__( 'Tab Display Name', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Wedding', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_filter_key',
			[
				'label'       => esc_html__( 'Filter Key (slug)', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'wedding',
				'description' => esc_html__( 'This slug links tabs to category cards. Must match the "Tab Key" field on category cards.', 'nalli-megamenu' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'megamenu_tabs',
			[
				'label'       => esc_html__( 'Sidebar Tabs', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					// ── Sarees tabs ──
					[ 'tab_label' => 'Wedding',     'tab_parent_nav' => 'Sarees',      'tab_filter_key' => 'wedding' ],
					[ 'tab_label' => 'Festive',     'tab_parent_nav' => 'Sarees',      'tab_filter_key' => 'festive' ],
					[ 'tab_label' => 'Events',      'tab_parent_nav' => 'Sarees',      'tab_filter_key' => 'events'  ],
					// ── Collections tabs ──
					[ 'tab_label' => 'Silk',        'tab_parent_nav' => 'Collections', 'tab_filter_key' => 'silk'    ],
					[ 'tab_label' => 'Cotton',      'tab_parent_nav' => 'Collections', 'tab_filter_key' => 'cotton'  ],
					// ── Occasions tabs ──
					[ 'tab_label' => 'Party Wear',  'tab_parent_nav' => 'Occasions',   'tab_filter_key' => 'party'   ],
					[ 'tab_label' => 'Traditional', 'tab_parent_nav' => 'Occasions',   'tab_filter_key' => 'traditional' ],
				],
				'title_field' => '📂 {{{ tab_parent_nav }}} → {{{ tab_label }}} ({{{ tab_filter_key }}})',
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * CONTENT TAB — Section 5: Utility Icons
	 * =================================================================== */

	private function register_content_utility_icons() {

		$this->start_controls_section(
			'section_utility_icons',
			[
				'label' => esc_html__( 'Header Utility Icons', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// --- Currency ---
		$this->add_control(
			'currency_select_enable',
			[
				'label'        => esc_html__( 'Show Currency Selector', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'currency_label',
			[
				'label'     => esc_html__( 'Currency Label', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'INR',
				'condition' => [ 'currency_select_enable' => 'yes' ],
			]
		);

		$this->add_control(
			'currency_flag',
			[
				'label'     => esc_html__( 'Flag Image', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [ 'url' => '' ],
				'condition' => [ 'currency_select_enable' => 'yes' ],
			]
		);

		// --- Search ---
		$this->add_control(
			'show_search',
			[
				'label'        => esc_html__( 'Show Search', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'search_shortcode',
			[
				'label'       => esc_html__( 'Search Form Shortcode', 'nalli-megamenu' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Optional: Enter a shortcode (e.g. [fibosearch]) to use a custom search plugin. Leave empty to use the default WooCommerce search.', 'nalli-megamenu' ),
				'condition'   => [ 'show_search' => 'yes' ],
			]
		);

		$this->add_control(
			'search_type',
			[
				'label'   => esc_html__( 'Search Query Type', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'product',
				'options' => [
					'product'  => esc_html__( 'Products Only', 'nalli-megamenu' ),
					'category' => esc_html__( 'Categories Only', 'nalli-megamenu' ),
					'both'     => esc_html__( 'Products & Categories', 'nalli-megamenu' ),
				],
				'condition' => [ 'show_search' => 'yes', 'search_shortcode' => '' ],
			]
		);

		// --- Account ---
		$this->add_control(
			'show_account',
			[
				'label'        => esc_html__( 'Show Account', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'account_url',
			[
				'label'     => esc_html__( 'Account URL', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_account' => 'yes' ],
			]
		);

		// --- Wishlist ---
		$this->add_control(
			'show_wishlist',
			[
				'label'        => esc_html__( 'Show Wishlist', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'custom_wishlist_icon',
			[
				'label'            => esc_html__( 'Custom Wishlist Icon', 'nalli-megamenu' ),
				'type'             => \Elementor\Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'fas fa-heart',
					'library' => 'fa-solid',
				],
				'condition'        => [ 'show_wishlist' => 'yes' ],
			]
		);

		$this->add_control(
			'wishlist_url',
			[
				'label'     => esc_html__( 'Wishlist URL', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_wishlist' => 'yes' ],
			]
		);

		// --- Cart ---
		$this->add_control(
			'show_cart',
			[
				'label'        => esc_html__( 'Show Cart', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'cart_url',
			[
				'label'     => esc_html__( 'Cart URL', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [ 'show_cart' => 'yes' ],
			]
		);

		// --- Icon Appearance ---
		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Global Icon Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-icon-color: {{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'wishlist_icon_color',
			[
				'label'     => esc_html__( 'Wishlist Icon Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wishlist' => 'color: {{VALUE}};' ],
				'description' => esc_html__( 'Leave empty to use the Global Icon Color.', 'nalli-megamenu' ),
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 14, 'max' => 40 ] ],
				'default'    => [ 'size' => 22, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-icon-size: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'utility_gap',
			[
				'label'      => esc_html__( 'Icons Spacing (Gap)', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-utils' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * STYLE TAB — Announcement Bar
	 * =================================================================== */

	private function register_style_announcement_bar() {

		$this->start_controls_section(
			'style_section_ann',
			[
				'label' => esc_html__( 'Announcement Bar', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nmm_ann_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-ann-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_ann_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-ann-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'nmm_ann_typography',
				'label'    => esc_html__( 'Typography', 'nalli-megamenu' ),
				'selector' => '{{WRAPPER}} .nmm-ann-item',
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * STYLE TAB — Header
	 * =================================================================== */

	private function register_style_header() {

		$this->start_controls_section(
			'style_section_header',
			[
				'label' => esc_html__( 'Header', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_border_bottom',
			[
				'label'        => esc_html__( 'Bottom Border', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'header_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => [ '{{WRAPPER}} .nmm-header' => 'border-bottom-color: {{VALUE}};' ],
				'condition' => [ 'header_border_bottom' => 'yes' ],
			]
		);

		$this->add_control(
			'header_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 1, 'max' => 8 ] ],
				'default'    => [ 'size' => 1 ],
				'selectors'  => [ '{{WRAPPER}} .nmm-header' => 'border-bottom: {{SIZE}}{{UNIT}} solid;' ],
				'condition'  => [ 'header_border_bottom' => 'yes' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'nmm_nav_font',
				'label'     => esc_html__( 'Nav Link Typography', 'nalli-megamenu' ),
				'selector'  => '{{WRAPPER}} .nmm-nav-link',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_nav_color',
			[
				'label'     => esc_html__( 'Nav Link Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-nav-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_nav_hover_color',
			[
				'label'     => esc_html__( 'Nav Link Hover Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-nav-hover: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_nav_active_color',
			[
				'label'     => esc_html__( 'Nav Active Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-nav-active: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'nmm_nav_spacing',
			[
				'label'      => esc_html__( 'Nav Item Gap', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 80 ] ],
				'default'    => [ 'size' => 28 ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-nav-spacing: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * STYLE TAB — MegaMenu
	 * =================================================================== */

	private function register_style_megamenu() {

		$this->start_controls_section(
			'style_section_megamenu',
			[
				'label' => esc_html__( 'MegaMenu Panel', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'panel_placement',
			[
				'label'   => esc_html__( 'Panel Placement', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'full' => esc_html__( 'Full Width (Header Aligned)', 'nalli-megamenu' ),
					'item' => esc_html__( 'Dropdown (Menu Item Aligned)', 'nalli-megamenu' ),
				],
				'prefix_class' => 'nmm-placement-',
			]
		);

		$this->add_control(
			'nmm_mm_bg_color',
			[
				'label'     => esc_html__( 'Panel Background', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-bg: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'nmm_mm_panel_width',
			[
				'label'      => esc_html__( 'Panel Max Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 300, 'max' => 3000 ] ],
				'default'    => [ 'size' => 1050, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'nmm_mm_border_radius',
			[
				'label'      => esc_html__( 'Panel Border Radius', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-mega-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_mm_shadow',
			[
				'label'        => esc_html__( 'Box Shadow', 'nalli-megamenu' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		// --- Left Tab Sidebar ---
		$this->add_control(
			'nmm_mm_tab_bg',
			[
				'label'     => esc_html__( 'Tab Sidebar Background', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#f9f5f3',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-tab-bg: {{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_mm_tab_active_color',
			[
				'label'     => esc_html__( 'Active Tab Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#8B1C2C',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-tab-active-color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'nmm_mm_tab_typography',
				'label'    => esc_html__( 'Tab Typography', 'nalli-megamenu' ),
				'selector' => '{{WRAPPER}} .nmm-tab-item',
			]
		);

		// --- Category Grid ---
		$this->add_responsive_control(
			'nmm_mm_cols',
			[
				'label'     => esc_html__( 'Grid Columns', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '4',
				'options'   => [
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-cols: {{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'nmm_mm_image_height',
			[
				'label'      => esc_html__( 'Image Height', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 60, 'max' => 400 ] ],
				'default'    => [ 'size' => 240, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-image-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_mm_image_border_radius',
			[
				'label'      => esc_html__( 'Image Border Radius', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'size' => 4, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-img-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_mm_gap',
			[
				'label'      => esc_html__( 'Grid Gap', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 48 ] ],
				'default'    => [ 'size' => 12, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-mm-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'nmm_mm_label_typography',
				'label'    => esc_html__( 'Card Label Typography', 'nalli-megamenu' ),
				'selector' => '{{WRAPPER}} .nmm-cat-label',
			]
		);

		$this->add_control(
			'nmm_mm_label_color',
			[
				'label'     => esc_html__( 'Card Label Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-cat-label' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_mm_label_hover_color',
			[
				'label'     => esc_html__( 'Card Label Hover Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .nmm-cat-card:hover .nmm-cat-label' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * STYLE TAB — Search Box
	 * =================================================================== */

	private function register_style_search_box() {

		$this->start_controls_section(
			'style_section_search',
			[
				'label' => esc_html__( 'Search Box', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'search_bg_color',
			[
				'label'     => esc_html__( 'Dropdown Background Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nmm-search-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_input_bg',
			[
				'label'     => esc_html__( 'Input Background Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nmm-search-input-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_text_color',
			[
				'label'     => esc_html__( 'Input Text Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nmm-search-field' => 'color: {{VALUE}};',
					'{{WRAPPER}} .nmm-search-field::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_border_color',
			[
				'label'     => esc_html__( 'Input Border Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nmm-search-input-wrap' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_focus_border_color',
			[
				'label'     => esc_html__( 'Input Focus Border Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nmm-search-input-wrap:focus-within' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_dropdown_width',
			[
				'label'      => esc_html__( 'Dropdown Max Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 1000 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .nmm-search-dropdown' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * STYLE TAB — Mobile Drawer
	 * =================================================================== */

	private function register_style_mobile_drawer() {

		$this->start_controls_section(
			'style_section_drawer',
			[
				'label' => esc_html__( 'Mobile Drawer', 'nalli-megamenu' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nmm_mobile_breakpoint',
			[
				'label'   => esc_html__( 'Mobile Breakpoint', 'nalli-megamenu' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'tablet',
				'options' => [
					'tablet' => esc_html__( 'Tablet (≤1024px)', 'nalli-megamenu' ),
					'mobile' => esc_html__( 'Mobile (≤768px)', 'nalli-megamenu' ),
				],
			]
		);

		/* ── Drawer Panel ── */
		$this->add_control(
			'_heading_drawer_panel',
			[
				'label'     => esc_html__( '⟶ Drawer Panel', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_drawer_bg',
			[
				'label'     => esc_html__( 'Background Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .nmm-wrapper'       => '--nmm-drawer-bg: {{VALUE}};',
					'{{WRAPPER}} .nmm-drawer-panel'  => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'nmm_drawer_width',
			[
				'label'      => esc_html__( 'Drawer Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 200, 'max' => 600 ] ],
				'default'    => [ 'size' => 320, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_drawer_overlay_color',
			[
				'label'     => esc_html__( 'Overlay Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.45)',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-overlay: {{VALUE}};' ],
			]
		);

		/* ── Nav Link Colors ── */
		$this->add_control(
			'_heading_drawer_nav',
			[
				'label'     => esc_html__( '⟶ Nav Link Colours', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_drawer_nav_color',
			[
				'label'     => esc_html__( 'Nav Link Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-nav-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_drawer_nav_hover',
			[
				'label'     => esc_html__( 'Nav Link Hover / Active Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#8B1C2C',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-nav-hover: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_drawer_sub_color',
			[
				'label'     => esc_html__( 'Sub-item & Category Label Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#555555',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-sub-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_drawer_sub_hover',
			[
				'label'     => esc_html__( 'Sub-item Hover Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#8B1C2C',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-sub-hover: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'nmm_drawer_nav_typography',
				'label'    => esc_html__( 'Nav Link Typography', 'nalli-megamenu' ),
				'selector' => '{{WRAPPER}} .nmm-drawer-acc-trigger, {{WRAPPER}} .nmm-drawer-plain-link, {{WRAPPER}} .nmm-drawer-nav > li > a',
			]
		);

		/* ── Separator & Close ── */
		$this->add_control(
			'_heading_drawer_details',
			[
				'label'     => esc_html__( '⟶ Separator & Close Button', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_drawer_separator_color',
			[
				'label'     => esc_html__( 'Separator Line Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-separator-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_drawer_close_color',
			[
				'label'     => esc_html__( 'Close (X) Button Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-drawer-close-color: {{VALUE}};' ],
			]
		);

		/* ── Hamburger Icon ── */
		$this->add_control(
			'_heading_hamburger',
			[
				'label'     => esc_html__( '⟶ Hamburger Icon', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nmm_hamburger_color',
			[
				'label'     => esc_html__( 'Bar Color', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_hamburger_bg',
			[
				'label'     => esc_html__( 'Button Background', 'nalli-megamenu' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'nmm_hamburger_radius',
			[
				'label'      => esc_html__( 'Button Border Radius', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 4, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_hamburger_bar_width',
			[
				'label'      => esc_html__( 'Bar Width', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 48 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-bar-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_hamburger_bar_height',
			[
				'label'      => esc_html__( 'Bar Thickness', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 1, 'max' => 6 ] ],
				'default'    => [ 'size' => 2, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-bar-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'nmm_hamburger_gap',
			[
				'label'      => esc_html__( 'Bar Spacing (gap)', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 2, 'max' => 12 ] ],
				'default'    => [ 'size' => 5, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .nmm-wrapper' => '--nmm-hamburger-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'nmm_drawer_padding',
			[
				'label'      => esc_html__( 'Drawer Inner Padding', 'nalli-megamenu' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'separator'  => 'before',
				'default'    => [
					'top'    => '24',
					'right'  => '16',
					'bottom' => '40',
					'left'   => '16',
					'unit'   => 'px',
				],
				'selectors'  => [ '{{WRAPPER}} .nmm-drawer-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ===================================================================
	 * END OF CONTROLS
	 * =================================================================== */

	/* ===================================================================
	 * render() — PHP frontend output
	 * =================================================================== */

	protected function render() {

		$s = $this->get_settings_for_display();

		// Data attributes for JS behavior.
		$sticky = ( ! empty( $s['header_sticky'] ) && 'yes' === $s['header_sticky'] ) ? '1' : '0';
		$shadow = ( ! empty( $s['header_shadow'] ) && 'yes' === $s['header_shadow'] )  ? '1' : '0';
		$speed  = ! empty( $s['ann_speed']['size'] ) ? (int) $s['ann_speed']['size'] : 4000;
		$bp_map = [ 'tablet' => 1024, 'mobile' => 768 ];
		$bp_key = ( ! empty( $s['nmm_mobile_breakpoint'] ) && isset( $bp_map[ $s['nmm_mobile_breakpoint'] ] ) ) ? $s['nmm_mobile_breakpoint'] : 'tablet';
		$bp     = $bp_map[ $bp_key ];

		// Build inline CSS speed variable.
		$speed_css = '--nmm-ann-speed:' . $speed . 'ms;';

		// Logo.
		$logo_url_raw = ! empty( $s['logo_url']['url'] ) ? $s['logo_url']['url'] : esc_url( home_url( '/' ) );
		$logo_img     = ! empty( $s['logo_image']['url'] ) ? $s['logo_image']['url'] : '';

		// Nav items.
		$nav_items     = ! empty( $s['nav_items'] )     ? $s['nav_items']     : [];
		$mega_items    = ! empty( $s['megamenu_items'] ) ? $s['megamenu_items'] : [];
		$mega_tabs     = ! empty( $s['megamenu_tabs'] )  ? $s['megamenu_tabs']  : [];

		// Utility flags.
		$show_curr     = ( ! empty( $s['currency_select_enable'] ) && 'yes' === $s['currency_select_enable'] );
		$show_search   = ( ! empty( $s['show_search'] )   && 'yes' === $s['show_search'] );
		$show_account  = ( ! empty( $s['show_account'] )  && 'yes' === $s['show_account'] );
		$show_wishlist = ( ! empty( $s['show_wishlist'] )  && 'yes' === $s['show_wishlist'] );
		$show_cart     = ( ! empty( $s['show_cart'] )      && 'yes' === $s['show_cart'] );

		// Helper: build link attributes safely.
		$link_attr = function ( $url_control ) {
			$out = '';
			if ( ! empty( $url_control['url'] ) ) {
				$out .= ' href="' . esc_url( $url_control['url'] ) . '"';
			}
			if ( ! empty( $url_control['is_external'] ) ) {
				$out .= ' target="_blank" rel="noopener noreferrer"';
			}
			if ( ! empty( $url_control['nofollow'] ) ) {
				$out .= ' rel="nofollow"';
			}
			return $out;
		};

		// Helper: filter megamenu items by parent nav label.
		$items_for_nav = function ( $nav_label ) use ( $mega_items ) {
			return array_filter( $mega_items, function ( $item ) use ( $nav_label ) {
				return isset( $item['mm_parent_label'] ) && $item['mm_parent_label'] === $nav_label;
			} );
		};

		// Helper: filter tabs by parent nav label.
		$tabs_for_nav = function ( $nav_label ) use ( $mega_tabs ) {
			return array_filter( $mega_tabs, function ( $tab ) use ( $nav_label ) {
				return isset( $tab['tab_parent_nav'] ) && $tab['tab_parent_nav'] === $nav_label;
			} );
		};
		?>
		<div class="nmm-wrapper"
		     data-sticky="<?php echo esc_attr( $sticky ); ?>"
		     data-shadow="<?php echo esc_attr( $shadow ); ?>"
		     data-breakpoint="<?php echo esc_attr( $bp ); ?>"
		     style="<?php echo esc_attr( $speed_css ); ?>">

			<?php if ( ! empty( $s['ann_enable'] ) && 'yes' === $s['ann_enable'] ) : ?>
			<!-- Announcement Bar -->
			<div class="nmm-ann-bar" data-speed="<?php echo esc_attr( $speed ); ?>">
				<div class="nmm-ann-ticker">
					<div class="nmm-ann-track">
						<?php
						$ann_items = ! empty( $s['ann_items'] ) ? $s['ann_items'] : [];
						$total_ann = count( $ann_items );
						foreach ( $ann_items as $i => $item ) :
							$has_link = ! empty( $item['ann_link']['url'] );
							?>
							<span class="nmm-ann-item">
								<?php if ( $has_link ) : ?>
									<a<?php echo $link_attr( $item['ann_link'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $item['ann_text'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $item['ann_text'] ); ?>
								<?php endif; ?>
							</span>
							<?php if ( $i < $total_ann - 1 ) : ?>
								<span class="nmm-ann-sep" aria-hidden="true">✦</span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<!-- Header -->
			<header class="nmm-header" role="banner">
				<div class="nmm-header-inner">

					<!-- Logo -->
					<div class="nmm-logo">
						<a href="<?php echo esc_url( $logo_url_raw ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'nalli-megamenu' ); ?>">
							<?php if ( $logo_img ) : ?>
								<img src="<?php echo esc_url( $logo_img ); ?>" alt="<?php esc_attr_e( 'Logo', 'nalli-megamenu' ); ?>" />
							<?php else : ?>
								<span style="font-size:1.3rem;font-weight:700;letter-spacing:.06em;">NALLI</span>
							<?php endif; ?>
						</a>
					</div>

					<!-- Desktop Navigation -->
					<nav class="nmm-nav" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'nalli-megamenu' ); ?>">
						<ul class="nmm-nav-list">
							<?php foreach ( $nav_items as $nav ) :
								$has_mega   = ( ! empty( $nav['has_megamenu'] ) && 'yes' === $nav['has_megamenu'] );
								$highlight  = ( ! empty( $nav['nav_highlight'] ) && 'yes' === $nav['nav_highlight'] );
								$hl_color   = ! empty( $nav['nav_highlight_color'] ) ? $nav['nav_highlight_color'] : '';
								$nav_label  = ! empty( $nav['nav_label'] ) ? $nav['nav_label'] : '';
								$mega_key   = sanitize_title( $nav_label );
								$item_tabs  = $tabs_for_nav( $nav_label );
								$item_cards = $items_for_nav( $nav_label );
								$first_tab  = reset( $item_tabs );
								?>
								<li class="nmm-nav-item<?php echo $has_mega ? ' nmm-has-mega' : ''; ?>"
								    <?php if ( $has_mega ) echo 'data-mega-key="' . esc_attr( $mega_key ) . '"'; ?>
								    <?php if ( $has_mega ) echo 'aria-haspopup="true"'; ?>>

									<a class="nmm-nav-link<?php echo $highlight ? ' nmm-highlight' : ''; ?>"
									   <?php if ( ! empty( $nav['nav_url']['url'] ) ) echo 'href="' . esc_url( $nav['nav_url']['url'] ) . '"'; else echo 'href="#" role="button"'; ?>
									   <?php if ( $highlight && $hl_color ) echo 'style="color:' . esc_attr( $hl_color ) . '"'; ?>
									   <?php if ( $has_mega ) echo 'aria-expanded="false"'; ?>>
										<?php echo esc_html( $nav_label ); ?>
									</a>

									<?php if ( $has_mega ) : ?>
									<!-- MegaMenu Panel: <?php echo esc_html( $nav_label ); ?> -->
									<div class="nmm-mega-panel"
									     role="region"
									     aria-label="<?php echo esc_attr( sprintf( __( 'Mega menu for %s', 'nalli-megamenu' ), $nav_label ) ); ?>">
										<div class="nmm-mega-inner">

											<!-- Left: Tab sidebar -->
											<?php if ( ! empty( $item_tabs ) ) : ?>
											<div class="nmm-mega-tabs">
												<ul class="nmm-tab-list" role="tablist" aria-label="<?php esc_attr_e( 'Browse by occasion', 'nalli-megamenu' ); ?>">
													<?php $tab_idx = 0; foreach ( $item_tabs as $tab ) :
														$is_first = ( 0 === $tab_idx );
														?>
														<li class="nmm-tab-item<?php echo $is_first ? ' nmm-tab-active' : ''; ?>"
														    data-filter="<?php echo esc_attr( $tab['tab_filter_key'] ); ?>"
														    role="tab"
														    aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
														    tabindex="<?php echo $is_first ? '0' : '-1'; ?>">
															<?php echo esc_html( $tab['tab_label'] ); ?>
														</li>
													<?php $tab_idx++; endforeach; ?>
												</ul>
											</div>
											<?php endif; ?>

											<!-- Right: Category image grid -->
											<div class="nmm-mega-grid" role="tabpanel">
												<?php if ( ! empty( $item_cards ) ) : ?>
												<?php foreach ( $item_cards as $card ) :
													$card_tab   = ! empty( $card['mm_tab_key'] ) ? $card['mm_tab_key'] : '';
													$card_url   = ! empty( $card['mm_url']['url'] ) ? $card['mm_url']['url'] : '#';
													$card_img   = ! empty( $card['mm_image']['url'] ) ? $card['mm_image']['url'] : '';
													$card_label = ! empty( $card['mm_label'] ) ? $card['mm_label'] : '';
													$badge_text = ! empty( $card['mm_badge'] ) ? $card['mm_badge'] : '';
													$badge_clr  = ! empty( $card['mm_badge_color'] ) ? $card['mm_badge_color'] : '#8B1C2C';
													// Cards with tab_key 'all' are always visible.
													$hidden = ( $first_tab && $card_tab !== 'all' && $card_tab !== $first_tab['tab_filter_key'] );
													?>
													<a class="nmm-cat-card"
													   href="<?php echo esc_url( $card_url ); ?>"
													   data-tab="<?php echo esc_attr( $card_tab ); ?>"
													   <?php if ( ! empty( $card['mm_url']['is_external'] ) ) echo 'target="_blank" rel="noopener noreferrer"'; ?>
													   <?php if ( $hidden ) echo 'style="display:none;"'; ?>>
														<div class="nmm-cat-img-wrap">
															<?php if ( $card_img ) : ?>
																<img src="<?php echo esc_url( $card_img ); ?>"
																     alt="<?php echo esc_attr( $card_label ); ?>"
																     loading="lazy" />
															<?php else : ?>
																<div style="height:var(--nmm-mm-image-height,160px);background:#f0ebe5;display:flex;align-items:center;justify-content:center;">
																	<span style="font-size:.7rem;color:#999;"><?php echo esc_html( $card_label ); ?></span>
																</div>
															<?php endif; ?>
															<?php if ( $badge_text ) : ?>
																<span class="nmm-badge"
																      style="background:<?php echo esc_attr( $badge_clr ); ?>;">
																	<?php echo esc_html( $badge_text ); ?>
																</span>
															<?php endif; ?>
														</div>
														<span class="nmm-cat-label"><?php echo esc_html( $card_label ); ?></span>
													</a>
												<?php endforeach; ?>
												<?php else : ?>
													<div style="padding:24px 16px;color:#999;font-size:13px;text-align:center;grid-column:1/-1;">
														<?php printf(
															/* translators: %s: nav label */
															esc_html__( 'No categories for "%s". Add items in MegaMenu Items section.', 'nalli-megamenu' ),
															esc_html( $nav_label )
														); ?>
													</div>
												<?php endif; ?>
											</div>

										</div>
									</div>
									<?php endif; ?>

								</li>
							<?php endforeach; ?>
						</ul>
					</nav>

					<!-- Utility Icons -->
					<div class="nmm-utils">

						<?php if ( $show_curr ) :
							$flag_src = ! empty( $s['currency_flag']['url'] ) ? $s['currency_flag']['url'] : '';
							$curr_lbl = ! empty( $s['currency_label'] ) ? $s['currency_label'] : 'INR';
							?>
							<div class="nmm-currency" role="button" aria-label="<?php esc_attr_e( 'Select currency', 'nalli-megamenu' ); ?>" tabindex="0">
								<?php if ( $flag_src ) : ?>
									<img class="nmm-flag" src="<?php echo esc_url( $flag_src ); ?>" alt="<?php echo esc_attr( $curr_lbl ); ?> flag" />
								<?php endif; ?>
								<span class="nmm-currency-label"><?php echo esc_html( $curr_lbl ); ?></span>
								<svg class="nmm-chevron" viewBox="0 0 10 6" aria-hidden="true">
									<polyline points="1,1 5,5 9,1" />
								</svg>
							</div>
						<?php endif; ?>

						<?php if ( $show_search ) : 
							$search_type = ! empty( $s['search_type'] ) ? $s['search_type'] : 'product';
							$placeholder = esc_attr__( 'Search for products...', 'nalli-megamenu' );
							if ( $search_type === 'category' ) {
								$placeholder = esc_attr__( 'Search for categories...', 'nalli-megamenu' );
							} elseif ( $search_type === 'both' ) {
								$placeholder = esc_attr__( 'Search products and categories...', 'nalli-megamenu' );
							}
						?>
							<div class="nmm-search-wrapper" data-search-type="<?php echo esc_attr( $search_type ); ?>">
								<a class="nmm-icon-btn nmm-search" href="#" aria-label="<?php esc_attr_e( 'Search', 'nalli-megamenu' ); ?>" role="button" aria-expanded="false" aria-haspopup="true">
									<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/></svg>
								</a>
								<div class="nmm-search-dropdown">
									<?php 
										if ( ! empty( $s['search_shortcode'] ) ) {
											echo do_shortcode( $s['search_shortcode'] );
										} else {
											// Default WP Search Form
											?>
											<form role="search" method="get" class="nmm-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
												<input type="hidden" name="post_type" value="<?php echo esc_attr( $search_type === 'category' ? 'product_cat' : 'product' ); ?>" />
												<div class="nmm-search-input-wrap">
													<svg class="nmm-search-icon-inside" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/></svg>
													<input type="search" class="nmm-search-field" placeholder="<?php echo $placeholder; ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" required />
													<button type="button" class="nmm-search-close-btn" aria-label="Close search">
														<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
													</button>
												</div>
												<button type="submit" class="nmm-search-submit" style="display: none;">
													<?php esc_html_e( 'Search', 'nalli-megamenu' ); ?>
												</button>
											</form>
											<div class="nmm-ajax-search-results"></div>
											<?php
										}
									?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $show_account ) :
							$acc_url = ! empty( $s['account_url']['url'] ) ? $s['account_url']['url'] : '#';
							?>
							<a class="nmm-icon-btn nmm-account"<?php echo $link_attr( $s['account_url'] ); // phpcs:ignore ?> aria-label="<?php esc_attr_e( 'My Account', 'nalli-megamenu' ); ?>">
								<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
							</a>
						<?php endif; ?>

						<?php if ( $show_wishlist ) : ?>
							<a class="nmm-icon-btn nmm-wishlist"<?php echo $link_attr( $s['wishlist_url'] ); // phpcs:ignore ?> aria-label="<?php esc_attr_e( 'Wishlist', 'nalli-megamenu' ); ?>">
								<?php
								if ( ! empty( $s['custom_wishlist_icon']['value'] ) ) {
									\Elementor\Icons_Manager::render_icon( $s['custom_wishlist_icon'], [ 'aria-hidden' => 'true' ] );
								} else {
									?>
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21C12 21 3 13.5 3 8a5 5 0 019-3 5 5 0 019 3c0 5.5-9 13-9 13z"/></svg>
									<?php
								}
								?>
								<span class="nmm-wishlist-count" aria-label="<?php esc_attr_e( 'Wishlist items', 'nalli-megamenu' ); ?>">0</span>
							</a>
						<?php endif; ?>

						<?php if ( $show_cart ) : 
							$cart_url_value = ! empty( $s['cart_url']['url'] ) && $s['cart_url']['url'] !== '#' ? $s['cart_url']['url'] : ( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' );
							$cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
						?>
							<a class="nmm-icon-btn nmm-cart" href="<?php echo esc_url( $cart_url_value ); ?>" aria-label="<?php esc_attr_e( 'Shopping Cart', 'nalli-megamenu' ); ?>">
								<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
								<span class="nmm-cart-count" aria-label="<?php esc_attr_e( 'Cart items', 'nalli-megamenu' ); ?>"><?php echo esc_html( $cart_count ); ?></span>
							</a>
						<?php endif; ?>

					</div><!-- .nmm-utils -->

					<!-- Mobile Hamburger -->
					<button class="nmm-hamburger"
					        aria-label="<?php esc_attr_e( 'Open navigation menu', 'nalli-megamenu' ); ?>"
					        aria-expanded="false"
					        aria-controls="nmm-drawer">
						<span></span><span></span><span></span>
					</button>

				</div><!-- .nmm-header-inner -->
			</header>

			<!-- Mobile Drawer -->
			<div class="nmm-drawer" id="nmm-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile navigation', 'nalli-megamenu' ); ?>">
				<div class="nmm-drawer-overlay" aria-hidden="true"></div>
				<div class="nmm-drawer-panel">

					<button class="nmm-drawer-close" aria-label="<?php esc_attr_e( 'Close navigation menu', 'nalli-megamenu' ); ?>">&times;</button>

					<?php if ( $logo_img ) : ?>
					<a class="nmm-drawer-logo" href="<?php echo esc_url( $logo_url_raw ); ?>">
						<img src="<?php echo esc_url( $logo_img ); ?>" alt="<?php esc_attr_e( 'Logo', 'nalli-megamenu' ); ?>" />
					</a>
					<?php endif; ?>

					<nav aria-label="<?php esc_attr_e( 'Mobile navigation', 'nalli-megamenu' ); ?>">
						<ul class="nmm-drawer-nav">
							<?php foreach ( $nav_items as $nav ) :
								$has_mega   = ( ! empty( $nav['has_megamenu'] ) && 'yes' === $nav['has_megamenu'] );
								$nav_label  = ! empty( $nav['nav_label'] ) ? $nav['nav_label'] : '';
								$item_cards = $items_for_nav( $nav_label );
								?>
								<li>
									<?php if ( $has_mega ) : ?>
										<button class="nmm-drawer-acc-trigger"
										        aria-expanded="false">
											<?php echo esc_html( $nav_label ); ?>
											<span class="nmm-acc-icon" aria-hidden="true">+</span>
										</button>
										<div class="nmm-drawer-acc-body">
											<!-- Shop By Category second-level collapsible -->
											<div class="nmm-drawer-sub-row">
												<button class="nmm-drawer-sub-trigger nmm-open"
												        aria-expanded="true">
													<?php esc_html_e( 'Shop By Category', 'nalli-megamenu' ); ?>
													<span class="nmm-acc-icon" aria-hidden="true">&mdash;</span>
												</button>
												<div class="nmm-drawer-sub-body nmm-open">
													<?php foreach ( $item_cards as $card ) :
														$card_url   = ! empty( $card['mm_url']['url'] ) ? $card['mm_url']['url'] : '#';
														$card_img   = ! empty( $card['mm_image']['url'] ) ? $card['mm_image']['url'] : '';
														$card_label = ! empty( $card['mm_label'] ) ? $card['mm_label'] : '';
														?>
														<a class="nmm-drawer-cat-card" href="<?php echo esc_url( $card_url ); ?>">
															<?php if ( $card_img ) : ?>
																<img class="nmm-drawer-cat-img"
																     src="<?php echo esc_url( $card_img ); ?>"
																     alt="<?php echo esc_attr( $card_label ); ?>"
																     loading="lazy" />
															<?php else : ?>
																<div class="nmm-drawer-cat-img nmm-drawer-cat-img--placeholder">
																	<span><?php echo esc_html( $card_label ); ?></span>
																</div>
															<?php endif; ?>
															<span class="nmm-drawer-cat-label"><?php echo esc_html( $card_label ); ?></span>
														</a>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									<?php else : ?>
										<a class="nmm-drawer-plain-link" href="<?php echo ! empty( $nav['nav_url']['url'] ) ? esc_url( $nav['nav_url']['url'] ) : '#'; ?>">
											<?php echo esc_html( $nav_label ); ?>
										</a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>

				</div><!-- .nmm-drawer-panel -->
			</div><!-- .nmm-drawer -->

		</div><!-- .nmm-wrapper -->
		<?php
	}

	/* ===================================================================
	 * content_template() — Backbone.js live preview
	 * =================================================================== */

	protected function content_template() {
		?>
		<#
		var sticky = settings.header_sticky === 'yes' ? '1' : '0';
		var shadow = settings.header_shadow === 'yes' ? '1' : '0';
		var speed  = settings.ann_speed && settings.ann_speed.size ? settings.ann_speed.size : 4000;
		var logoUrl = settings.logo_url && settings.logo_url.url ? settings.logo_url.url : '/';
		var logoImg = settings.logo_image && settings.logo_image.url ? settings.logo_image.url : '';
		var bpMap   = { tablet: 1024, mobile: 768 };
		var bpKey   = settings.nmm_mobile_breakpoint || 'tablet';
		var bp      = bpMap[ bpKey ] || 1024;
		#>
		<div class="nmm-wrapper" data-sticky="{{ sticky }}" data-shadow="{{ shadow }}" data-breakpoint="{{ bp }}" style="--nmm-ann-speed: {{ speed }}ms;">

			<# if ( settings.ann_enable === 'yes' && settings.ann_items.length ) { #>
			<div class="nmm-ann-bar">
				<div class="nmm-ann-ticker">
					<div class="nmm-ann-track">
						<# _.each( settings.ann_items, function( item, i ) { #>
							<span class="nmm-ann-item">
								<# if ( item.ann_link && item.ann_link.url ) { #>
									<a href="{{ item.ann_link.url }}">{{{ item.ann_text }}}</a>
								<# } else { #>
									{{{ item.ann_text }}}
								<# } #>
							</span>
							<# if ( i < settings.ann_items.length - 1 ) { #>
								<span class="nmm-ann-sep" aria-hidden="true">✦</span>
							<# } #>
						<# }); #>
					</div>
				</div>
			</div>
			<# } #>

			<header class="nmm-header">
				<div class="nmm-header-inner">

					<div class="nmm-logo">
						<a href="{{ logoUrl }}">
							<# if ( logoImg ) { #>
								<img src="{{ logoImg }}" alt="Logo" />
							<# } else { #>
								<span style="font-size:1.3rem;font-weight:700;letter-spacing:.06em;">NALLI</span>
							<# } #>
						</a>
					</div>

					<nav class="nmm-nav" role="navigation">
						<ul class="nmm-nav-list">
							<# if ( settings.nav_items ) {
								_.each( settings.nav_items, function( nav ) {
									var hasMega   = nav.has_megamenu === 'yes';
									var highlight = nav.nav_highlight === 'yes';
									var hlStyle   = ( highlight && nav.nav_highlight_color ) ? 'style="color:' + nav.nav_highlight_color + '"' : '';
							#>
							<li class="nmm-nav-item<# if(hasMega){#> nmm-has-mega<#}#>">
								<a class="nmm-nav-link<# if(highlight){#> nmm-highlight<#}#>"
								   href="{{ nav.nav_url && nav.nav_url.url ? nav.nav_url.url : '#' }}"
								   {{{ hlStyle }}}>
									{{{ nav.nav_label }}}
								</a>
							</li>
							<# }); } #>
						</ul>
					</nav>

					<div class="nmm-utils">
						<# if ( settings.currency_select_enable === 'yes' ) { #>
						<div class="nmm-currency">
							<# if ( settings.currency_flag && settings.currency_flag.url ) { #>
								<img class="nmm-flag" src="{{ settings.currency_flag.url }}" alt="flag" />
							<# } #>
							<span class="nmm-currency-label">{{ settings.currency_label || 'INR' }}</span>
							<svg class="nmm-chevron" viewBox="0 0 10 6"><polyline points="1,1 5,5 9,1"/></svg>
						</div>
						<# } #>

						<# if ( settings.show_search === 'yes' ) { #>
						<a class="nmm-icon-btn nmm-search" href="#" aria-label="Search">
							<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/></svg>
						</a>
						<# } #>

						<# if ( settings.show_account === 'yes' ) { #>
						<a class="nmm-icon-btn nmm-account" href="{{ settings.account_url && settings.account_url.url ? settings.account_url.url : '#' }}" aria-label="Account">
							<svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
						</a>
						<# } #>

						<# if ( settings.show_wishlist === 'yes' ) { #>
						<a class="nmm-icon-btn nmm-wishlist" href="{{ settings.wishlist_url && settings.wishlist_url.url ? settings.wishlist_url.url : '#' }}" aria-label="Wishlist">
							<svg viewBox="0 0 24 24"><path d="M12 21C12 21 3 13.5 3 8a5 5 0 019-3 5 5 0 019 3c0 5.5-9 13-9 13z"/></svg>
							<span class="nmm-wishlist-count">0</span>
						</a>
						<# } #>

						<# if ( settings.show_cart === 'yes' ) { #>
						<a class="nmm-icon-btn nmm-cart" href="{{ settings.cart_url && settings.cart_url.url ? settings.cart_url.url : '#' }}" aria-label="Cart">
							<svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
							<span class="nmm-cart-count">0</span>
						</a>
						<# } #>
					</div>

					<button class="nmm-hamburger" aria-label="Open menu" aria-expanded="false">
						<span></span><span></span><span></span>
					</button>

				</div>
			</header>

		</div>
		<?php
	}

} // end class Header_MegaMenu
