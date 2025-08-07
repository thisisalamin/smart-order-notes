<?php
/**
 * Handles custom post type registration and admin menu for Smart Order Notes
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SONotes_CPT {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_template_cpt' ), 0 );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_filter( 'manage_sonotes_template_posts_columns', array( __CLASS__, 'template_columns' ) );
		add_action( 'manage_sonotes_template_posts_custom_column', array( __CLASS__, 'template_column_content' ), 10, 2 );
		add_filter( 'manage_edit-sonotes_template_sortable_columns', array( __CLASS__, 'template_sortable_columns' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'template_orderby' ) );
	}

	/**
	 * Register custom post type for note templates
	 */
	public static function register_template_cpt() {
		$labels = array(
			'name'               => __( 'Order Note Templates', 'smart-order-notes' ),
			'singular_name'      => __( 'Order Note Template', 'smart-order-notes' ),
			'add_new'            => __( 'Add New Template', 'smart-order-notes' ),
			'add_new_item'       => __( 'Add New Note Template', 'smart-order-notes' ),
			'edit_item'          => __( 'Edit Note Template', 'smart-order-notes' ),
			'new_item'           => __( 'New Note Template', 'smart-order-notes' ),
			'view_item'          => __( 'View Note Template', 'smart-order-notes' ),
			'search_items'       => __( 'Search Note Templates', 'smart-order-notes' ),
			'not_found'          => __( 'No templates found', 'smart-order-notes' ),
			'not_found_in_trash' => __( 'No templates found in Trash', 'smart-order-notes' ),
			'all_items'          => __( 'All Note Templates', 'smart-order-notes' ),
			'menu_name'          => __( 'Order Notes', 'smart-order-notes' ),
		);

		$args = array(
			'labels'                           => $labels,
			'public'                           => false,
			'publicly_queryable'               => false,
			'show_ui'                          => true,
			'show_in_menu'                     => false,
			'query_var'                        => false,
			'rewrite'                          => false,
			'capability_type'                  => 'shop_order',
			'capabilities'                     => array(
				'create_posts'        => 'manage_woocommerce',
				'edit_posts'          => 'manage_woocommerce',
				'edit_others_posts'   => 'manage_woocommerce',
				'publish_posts'       => 'manage_woocommerce',
				'read_private_posts'  => 'manage_woocommerce',
				'delete_posts'        => 'manage_woocommerce',
				'delete_others_posts' => 'manage_woocommerce',
			),
			'map_meta_cap'                     => true,
			'has_archive'                      => false,
			'hierarchical'                     => false,
			'menu_position'                    => null,
			'supports'                         => array( 'title', 'editor' ),
			'show_in_rest'                     => false,
			// HPOS/Custom order table compatibility
			'woocommerce_is_custom_order_type' => false,
			'custom_order_tables'              => false,
			'woocommerce_order_data_store_cpt' => false,
		);

		register_post_type( 'sonotes_template', $args );
	}

	/**
	 * Add admin menu for templates under WooCommerce
	 */
	public static function admin_menu() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_submenu_page(
				'woocommerce',
				__( 'Order Note Templates', 'smart-order-notes' ),
				__( 'Order Notes', 'smart-order-notes' ),
				'manage_woocommerce',
				'edit.php?post_type=sonotes_template'
			);
		}
	}

	/**
	 * Customize post list columns for note templates
	 */
	public static function template_columns( $columns ) {
		$new_columns                    = array();
		$new_columns['cb']              = $columns['cb'];
		$new_columns['title']           = $columns['title'];
		$new_columns['sonotes_type']    = __( 'Note Type', 'smart-order-notes' );
		$new_columns['sonotes_content'] = __( 'Content Preview', 'smart-order-notes' );
		$new_columns['date']            = $columns['date'];

		return $new_columns;
	}

	/**
	 * Display custom column content
	 */
	public static function template_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'sonotes_type':
				$type       = get_post_meta( $post_id, '_sonotes_type', true );
				$type_label = $type === 'customer' ? __( 'Customer Note', 'smart-order-notes' ) : __( 'Private Note', 'smart-order-notes' );
				$type_color = $type === 'customer' ? '#46b450' : '#72777c';
				echo '<span style="color: ' . esc_attr( $type_color ) . '; font-weight: 500;">' . esc_html( $type_label ) . '</span>';
				break;

			case 'sonotes_content':
				$content = get_post_field( 'post_content', $post_id );
				echo '<div style="max-width: 300px;">' . esc_html( wp_trim_words( $content, 15, '...' ) ) . '</div>';
				break;
		}
	}

	/**
	 * Make custom columns sortable
	 */
	public static function template_sortable_columns( $columns ) {
		$columns['sonotes_type'] = 'sonotes_type';
		return $columns;
	}

	/**
	 * Handle sorting by custom columns
	 */
	public static function template_orderby( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->get( 'orderby' ) === 'sonotes_type' ) {
			$query->set( 'meta_key', '_sonotes_type' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}

SONotes_CPT::init();
