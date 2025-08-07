<?php
/**
 * Handles dropdown UI and template insertion on order page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add template selector metabox to WooCommerce orders
 */
function sonotes_add_order_metabox() {
	// Check if we have proper permissions
	if ( ! current_user_can( 'edit_shop_orders' ) ) {
		return;
	}

	// Add to both old and new WooCommerce order screens, with high priority
	$screens = array( 'shop_order', 'woocommerce_page_wc-orders' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'sonotes_order_templates',
			__( 'Order Note Templates', 'smart-order-notes' ),
			'sonotes_render_order_metabox',
			$screen,
			'side',
			'high' // This makes it appear before the default order notes
		);
	}
}
add_action( 'add_meta_boxes', 'sonotes_add_order_metabox', 0 );

/**
 * Render the order metabox content
 */
function sonotes_render_order_metabox( $post_or_order ) {
	// Get all published templates
	$templates = get_posts(
		array(
			'post_type'      => 'sonotes_template',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	if ( empty( $templates ) ) {
		?>
		<div class="sonotes-no-templates">
			<p><?php _e( 'No templates available.', 'smart-order-notes' ); ?></p>
			<p>
				<a href="<?php echo admin_url( 'edit.php?post_type=sonotes_template' ); ?>" class="button button-secondary">
					<?php _e( 'Create Templates', 'smart-order-notes' ); ?>
				</a>
			</p>
		</div>
		<?php
		return;
	}

	// Group templates by type for better UX
	$private_templates  = array();
	$customer_templates = array();

	foreach ( $templates as $template ) {
		$type = get_post_meta( $template->ID, '_sonotes_type', true );
		if ( $type === 'customer' ) {
			$customer_templates[] = $template;
		} else {
			$private_templates[] = $template;
		}
	}
	?>
	<div class="sonotes-template-selector">
		<div class="sonotes-field-group">
			<label for="sonotes_template_select">
				<strong><?php _e( 'Select Template:', 'smart-order-notes' ); ?></strong>
			</label>
			<select id="sonotes_template_select" style="width: 100%; margin-top: 5px;">
				<option value=""><?php _e( 'Choose a template...', 'smart-order-notes' ); ?></option>

				<?php if ( ! empty( $private_templates ) ) : ?>
					<optgroup label="<?php esc_attr_e( 'Private Notes (Staff Only)', 'smart-order-notes' ); ?>">
						<?php foreach ( $private_templates as $template ) : ?>
							<option value="<?php echo esc_attr( $template->ID ); ?>"
									data-content="<?php echo esc_attr( $template->post_content ); ?>"
									data-type="private">
								<?php echo esc_html( $template->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>

				<?php if ( ! empty( $customer_templates ) ) : ?>
					<optgroup label="<?php esc_attr_e( 'Customer Notes (Sent to Customer)', 'smart-order-notes' ); ?>">
						<?php foreach ( $customer_templates as $template ) : ?>
							<option value="<?php echo esc_attr( $template->ID ); ?>"
									data-content="<?php echo esc_attr( $template->post_content ); ?>"
									data-type="customer">
								<?php echo esc_html( $template->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>
			</select>
		</div>

		<div class="sonotes-field-group" style="margin-top: 10px;">
			<label>
				<strong><?php _e( 'Note Type:', 'smart-order-notes' ); ?></strong>
			</label>
			<div class="sonotes-note-type-selector" style="margin-top: 5px;">
				<label style="margin-right: 15px;">
					<input type="radio" name="sonotes_note_type" value="private" checked="checked">
					<?php _e( 'Private Note', 'smart-order-notes' ); ?>
				</label>
				<label>
					<input type="radio" name="sonotes_note_type" value="customer">
					<?php _e( 'Customer Note', 'smart-order-notes' ); ?>
				</label>
			</div>
			<p class="description" style="margin-top: 5px;">
				<?php _e( 'Customer notes will be sent via email to the customer.', 'smart-order-notes' ); ?>
			</p>
		</div>

		<div class="sonotes-field-group" style="margin-top: 15px;">
			<button type="button" class="button button-primary" id="sonotes_insert_btn" style="width: 100%;">
				<?php _e( 'Insert Template', 'smart-order-notes' ); ?>
			</button>
		</div>

		<div class="sonotes-preview" id="sonotes_preview" style="margin-top: 10px; display: none;">
			<label><strong><?php _e( 'Preview:', 'smart-order-notes' ); ?></strong></label>
			<div class="sonotes-preview-content" style="background: #f9f9f9; padding: 8px; border: 1px solid #ddd; margin-top: 5px; font-size: 12px; line-height: 1.4;"></div>
		</div>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Handle template selection
		$('#sonotes_template_select').on('change', function() {
			var $selected = $(this).find('option:selected');
			var content = $selected.data('content');
			var type = $selected.data('type');

			// Show preview
			if (content) {
				$('#sonotes_preview .sonotes-preview-content').text(content);
				$('#sonotes_preview').show();

				// Auto-select note type based on template
				if (type) {
					$('input[name="sonotes_note_type"][value="' + type + '"]').prop('checked', true);
				}
			} else {
				$('#sonotes_preview').hide();
			}
		});

		// Handle insert button click
		$('#sonotes_insert_btn').on('click', function() {
			var content = $('#sonotes_template_select option:selected').data('content');
			var noteType = $('input[name="sonotes_note_type"]:checked').val();

			if (!content) {
				alert('<?php _e( 'Please select a template first.', 'smart-order-notes' ); ?>');
				return;
			}

			// Try different selectors for the order note textarea (different WC versions)
			var $noteField = $('#add_order_note, textarea[name="order_note"], .wc-order-add-note textarea');

			if ($noteField.length) {
				$noteField.val(content).focus();

				// Set the note type if the dropdown exists (newer WC versions)
				var $noteTypeSelect = $('#order_note_type, select[name="order_note_type"]');
				if ($noteTypeSelect.length && noteType) {
					$noteTypeSelect.val(noteType === 'customer' ? 'customer' : '').trigger('change');
				}

				// Show success message
				$(this).text('<?php _e( 'Inserted!', 'smart-order-notes' ); ?>').addClass('button-success');
				setTimeout(function() {
					$('#sonotes_insert_btn').text('<?php _e( 'Insert Template', 'smart-order-notes' ); ?>').removeClass('button-success');
				}, 2000);
			} else {
				alert('<?php _e( 'Could not find the order note field. Please add the note manually.', 'smart-order-notes' ); ?>');
			}
		});
	});
	</script>

	<style>
	.sonotes-template-selector {
		padding: 10px 0;
	}
	.sonotes-field-group {
		margin-bottom: 10px;
	}
	.sonotes-field-group label {
		display: block;
		margin-bottom: 3px;
	}
	.sonotes-note-type-selector label {
		display: inline;
		font-weight: normal;
		margin-bottom: 0;
	}
	.sonotes-preview-content {
		max-height: 80px;
		overflow-y: auto;
	}
	.button-success {
		background-color: #46b450 !important;
		border-color: #46b450 !important;
		color: white !important;
	}
	.sonotes-no-templates {
		text-align: center;
		padding: 20px 10px;
		color: #666;
	}
	</style>
	<?php
}

/**
 * Add AJAX handler for inserting templates (alternative method for complex integrations)
 */
function sonotes_ajax_insert_template() {
	// Verify nonce
	$nonce = sanitize_text_field( $_REQUEST['nonce'] ?? '' );
	if ( ! wp_verify_nonce( $nonce, 'sonotes_nonce' ) ) {
		wp_die( 'Security check failed' );
	}

	// Check permissions
	if ( ! current_user_can( 'edit_shop_orders' ) ) {
		wp_die( 'Insufficient permissions' );
	}

	$template_id = intval( $_REQUEST['template_id'] ?? 0 );
	$template    = get_post( $template_id );

	if ( ! $template || $template->post_type !== 'sonotes_template' ) {
		wp_die( 'Invalid template' );
	}

	wp_send_json_success(
		array(
			'content' => $template->post_content,
			'type'    => get_post_meta( $template_id, '_sonotes_type', true ),
		)
	);
}
add_action( 'wp_ajax_sonotes_insert_template', 'sonotes_ajax_insert_template' );

/**
 * Add quick access link in order list actions
 */
function sonotes_order_row_actions( $actions, $post ) {
	if ( $post->post_type === 'shop_order' && current_user_can( 'edit_shop_orders' ) ) {
		$templates_count = wp_count_posts( 'sonotes_template' )->publish;
		if ( $templates_count > 0 ) {
			$actions['sonotes_templates'] = sprintf(
				'<span style="color: #0073aa;">📝 %d %s</span>',
				$templates_count,
				_n( 'template available', 'templates available', $templates_count, 'smart-order-notes' )
			);
		}
	}
	return $actions;
}
add_filter( 'post_row_actions', 'sonotes_order_row_actions', 10, 2 );
