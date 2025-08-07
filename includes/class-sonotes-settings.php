<?php
/**
 * Handles plugin settings and options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add settings page under WooCommerce menu
 */
function sonotes_add_settings_page() {
	if ( current_user_can( 'manage_woocommerce' ) ) {
		add_submenu_page(
			'woocommerce',
			__( 'Order Notes Settings', 'smart-order-notes' ),
			__( 'Note Settings', 'smart-order-notes' ),
			'manage_woocommerce',
			'sonotes-settings',
			'sonotes_settings_page_html'
		);
	}
}
add_action( 'admin_menu', 'sonotes_add_settings_page', 99 );

/**
 * Settings page HTML
 */
function sonotes_settings_page_html() {
	// Check user capabilities
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	// Handle form submission
	if ( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'sonotes_settings' );

		$auto_select_type = isset( $_POST['sonotes_auto_select_type'] ) ? 1 : 0;
		$show_preview     = isset( $_POST['sonotes_show_preview'] ) ? 1 : 0;
		$template_limit   = intval( $_POST['sonotes_template_limit'] );

		update_option( 'sonotes_auto_select_type', $auto_select_type );
		update_option( 'sonotes_show_preview', $show_preview );
		update_option( 'sonotes_template_limit', max( 5, min( 100, $template_limit ) ) );

		echo '<div class="notice notice-success"><p>' . __( 'Settings saved!', 'smart-order-notes' ) . '</p></div>';
	}

	// Get current settings
	$auto_select_type = get_option( 'sonotes_auto_select_type', 1 );
	$show_preview     = get_option( 'sonotes_show_preview', 1 );
	$template_limit   = get_option( 'sonotes_template_limit', 50 );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<form action="" method="post">
			<?php wp_nonce_field( 'sonotes_settings' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Auto-select Note Type', 'smart-order-notes' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="sonotes_auto_select_type" value="1" <?php checked( $auto_select_type ); ?>>
							<?php _e( 'Automatically select note type based on template default', 'smart-order-notes' ); ?>
						</label>
						<p class="description">
							<?php _e( 'When enabled, selecting a template will automatically choose the matching note type.', 'smart-order-notes' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Show Template Preview', 'smart-order-notes' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="sonotes_show_preview" value="1" <?php checked( $show_preview ); ?>>
							<?php _e( 'Show template content preview when selecting templates', 'smart-order-notes' ); ?>
						</label>
						<p class="description">
							<?php _e( 'Displays a preview of the template content before inserting.', 'smart-order-notes' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Template Limit', 'smart-order-notes' ); ?></th>
					<td>
						<input type="number" name="sonotes_template_limit" value="<?php echo esc_attr( $template_limit ); ?>" min="5" max="100">
						<p class="description">
							<?php _e( 'Maximum number of templates to display in dropdowns (5-100).', 'smart-order-notes' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>

		<hr>

		<h2><?php _e( 'Plugin Information', 'smart-order-notes' ); ?></h2>

		<?php
		// Get plugin stats
		$template_count = wp_count_posts( 'sonotes_template' );
		$private_count  = 0;
		$customer_count = 0;

		$templates = get_posts(
			array(
				'post_type'      => 'sonotes_template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		);

		foreach ( $templates as $template ) {
			$type = get_post_meta( $template->ID, '_sonotes_type', true );
			if ( $type === 'customer' ) {
				++$customer_count;
			} else {
				++$private_count;
			}
		}
		?>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Statistic', 'smart-order-notes' ); ?></th>
					<th><?php _e( 'Count', 'smart-order-notes' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php _e( 'Total Templates', 'smart-order-notes' ); ?></td>
					<td><?php echo esc_html( $template_count->publish ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Private Note Templates', 'smart-order-notes' ); ?></td>
					<td><?php echo esc_html( $private_count ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Customer Note Templates', 'smart-order-notes' ); ?></td>
					<td><?php echo esc_html( $customer_count ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Plugin Version', 'smart-order-notes' ); ?></td>
					<td><?php echo esc_html( SONOTES_VERSION ); ?></td>
				</tr>
			</tbody>
		</table>

		<br>

		<div class="card">
			<h3><?php _e( 'Quick Actions', 'smart-order-notes' ); ?></h3>
			<p>
				<a href="<?php echo admin_url( 'post-new.php?post_type=sonotes_template' ); ?>" class="button button-primary">
					<?php _e( 'Create New Template', 'smart-order-notes' ); ?>
				</a>
				<a href="<?php echo admin_url( 'edit.php?post_type=sonotes_template' ); ?>" class="button">
					<?php _e( 'Manage Templates', 'smart-order-notes' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php
}
