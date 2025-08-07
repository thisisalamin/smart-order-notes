<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SONotes_Order_UI {
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_order_metabox' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_order_scripts' ) );
		add_action( 'wp_ajax_sonotes_insert_and_send', array( __CLASS__, 'ajax_insert_and_send' ) );
		add_action( 'wp_ajax_sonotes_insert_template', array( __CLASS__, 'ajax_insert_template' ) );
		add_filter( 'post_row_actions', array( __CLASS__, 'order_row_actions' ), 10, 2 );
	}

	public static function add_order_metabox() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}
		$screens = array( 'shop_order', 'woocommerce_page_wc-orders' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'sonotes_order_templates',
				__( 'Order Note Templates', 'smart-order-notes' ),
				array( __CLASS__, 'render_order_metabox' ),
				$screen,
				'side',
				'core',
				array( '__back_compat_meta_box' => true )
			);
		}
	}

	public static function enqueue_order_scripts( $hook ) {
		global $post_type, $pagenow;
		if ( ( $post_type === 'shop_order' && $hook === 'post.php' ) ||
			( $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === 'wc-orders' && isset( $_GET['_sonotes_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_sonotes_nonce'] ) ), 'sonotes_nonce' ) ) ) {
			wp_enqueue_script( 'jquery' );
			wp_localize_script(
				'jquery',
				'sonotes_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'sonotes_nonce' ),
				)
			);
			wp_add_inline_style(
				'wp-admin',
				'.sonotes-template-selector { padding: 10px 0; }'
				. '.sonotes-field-group { margin-bottom: 10px; }'
				. '.sonotes-field-group label { display: block; margin-bottom: 3px; }'
				. '.sonotes-note-type-selector label { display: inline; font-weight: normal; margin-bottom: 0; }'
				. '.sonotes-preview-content { max-height: 80px; overflow-y: auto; }'
				. '.button-success { background-color: #46b450 !important; border-color: #46b450 !important; color: white !important; }'
				. '.sonotes-no-templates { text-align: center; padding: 20px 10px; color: #666; }'
			);
		}
	}

	public static function render_order_metabox( $post_or_order ) {
		// ...existing code from sonotes_render_order_metabox...
		// (Copy the full function body here, unchanged, as a static method)
		?>
		<?php
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
				<p><?php esc_html_e( 'No templates available.', 'smart-order-notes' ); ?></p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sonotes_template' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Create Templates', 'smart-order-notes' ); ?>
					</a>
				</p>
			</div>
			<?php
			return;
		}

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
				<strong><?php esc_html_e( 'Select Template:', 'smart-order-notes' ); ?></strong>
			</label>
				<select id="sonotes_template_select" style="width: 100%; margin-top: 5px;">
				<option value=""><?php esc_html_e( 'Choose a template...', 'smart-order-notes' ); ?></option>
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
				<strong><?php esc_html_e( 'Note Type:', 'smart-order-notes' ); ?></strong>
			</label>
				<div class="sonotes-note-type-selector" style="margin-top: 5px;">
					<label style="margin-right: 15px;">
					<input type="radio" name="sonotes_note_type" value="private" checked="checked">
					<?php esc_html_e( 'Private Note', 'smart-order-notes' ); ?>
					</label>
					<label>
					<input type="radio" name="sonotes_note_type" value="customer">
					<?php esc_html_e( 'Customer Note', 'smart-order-notes' ); ?>
					</label>
				</div>
			<p class="description" style="margin-top: 5px;">
				<?php esc_html_e( 'Customer notes will be sent via email to the customer.', 'smart-order-notes' ); ?>
			</p>
			</div>
		<div class="sonotes-field-group" style="margin-top: 15px; display: flex; gap: 8px;">
			<button type="button" class="button button-primary" id="sonotes_insert_btn" style="flex:1;">
				<?php esc_html_e( 'Insert', 'smart-order-notes' ); ?>
			</button>
			<button type="button" class="button button-secondary" id="sonotes_insert_send_btn" style="flex:1;">
				<?php esc_html_e( 'Insert & Send', 'smart-order-notes' ); ?>
			</button>
		</div>
			<div class="sonotes-preview" id="sonotes_preview" style="margin-top: 10px; display: none;">
			<label><strong><?php esc_html_e( 'Preview:', 'smart-order-notes' ); ?></strong></label>
				<div class="sonotes-preview-content" style="background: #f9f9f9; padding: 8px; border: 1px solid #ddd; margin-top: 5px; font-size: 12px; line-height: 1.4;"></div>
			</div>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// ...existing JS code from sonotes_render_order_metabox...
			// (Copy the full JS code here, unchanged)
			$('#sonotes_template_select').on('change', function() {
				var $selected = $(this).find('option:selected');
				var content = $selected.data('content');
				var type = $selected.data('type');
				if (content) {
					$('#sonotes_preview .sonotes-preview-content').text(content);
					$('#sonotes_preview').show();
					if (type) {
						$('input[name="sonotes_note_type"][value="' + type + '"]').prop('checked', true);
					}
				} else {
					$('#sonotes_preview').hide();
				}
			});
			$('#sonotes_insert_btn').on('click', function() {
				var content = $('#sonotes_template_select option:selected').data('content');
				var noteType = $('input[name="sonotes_note_type"]:checked').val();
				if (!content) {
					alert('<?php esc_js( __( 'Please select a template first.', 'smart-order-notes' ) ); ?>');
					return;
				}
				var $noteField = $('#add_order_note, textarea[name="order_note"], .wc-order-add-note textarea');
				if ($noteField.length) {
					$noteField.val(content).focus();
					var $noteTypeSelect = $('#order_note_type, select[name="order_note_type"]');
					if ($noteTypeSelect.length && noteType) {
						$noteTypeSelect.val(noteType === 'customer' ? 'customer' : '').trigger('change');
					}
					$(this).text('<?php esc_js( __( 'Inserted!', 'smart-order-notes' ) ); ?>').addClass('button-success');
					setTimeout(function() {
						$('#sonotes_insert_btn').text('<?php esc_js( __( 'Insert', 'smart-order-notes' ) ); ?>').removeClass('button-success');
					}, 2000);
				} else {
					alert('<?php esc_js( __( 'Could not find the order note field. Please add the note manually.', 'smart-order-notes' ) ); ?>');
				}
			});
			$('#sonotes_insert_send_btn').on('click', function() {
				var content = $('#sonotes_template_select option:selected').data('content');
				var noteType = $('input[name="sonotes_note_type"]:checked').val();
				var orderId = $("input#post_ID").val() || $("input[name='id']").val();
				if (!content || !orderId) {
					alert('<?php esc_js( __( 'Please select a template first.', 'smart-order-notes' ) ); ?>');
					return;
				}
				var $btn = $(this);
				$btn.prop('disabled', true).text('<?php esc_js( __( 'Sending...', 'smart-order-notes' ) ); ?>');
				$.post(sonotes_ajax.ajax_url, {
					action: 'sonotes_insert_and_send',
					nonce: sonotes_ajax.nonce,
					order_id: orderId,
					content: content,
					note_type: noteType
				}, function(response) {
					$btn.prop('disabled', false).text('<?php esc_js( __( 'Insert & Send', 'smart-order-notes' ) ); ?>');
					if (response && response.success) {
						location.reload();
					} else {
						alert(response && response.data ? response.data : 'Error adding note.');
					}
				});
			});
		});
		</script>
		<?php
	}

	public static function ajax_insert_and_send() {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sonotes_nonce' ) ) {
			wp_send_json_error( 'Security check failed.' );
		}
		$order_id  = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
		$content   = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		$note_type = isset( $_POST['note_type'] ) && $_POST['note_type'] === 'customer' ? 'customer' : 'private';
		if ( ! $order_id || ! $content ) {
			wp_send_json_error( 'Missing data.' );
		}
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( 'Order not found.' );
		}
		$is_customer_note = ( $note_type === 'customer' );
		$order->add_order_note( $content, $is_customer_note, true );
		do_action( 'sonotes_template_inserted', $order_id, $content, $note_type );
		wp_send_json_success();
	}

	public static function ajax_insert_template() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sonotes_nonce' ) ) {
			wp_die( 'Security check failed' );
		}
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

	public static function order_row_actions( $actions, $post ) {
		if ( $post->post_type === 'shop_order' && current_user_can( 'edit_shop_orders' ) ) {
			$templates_count = wp_count_posts( 'sonotes_template' )->publish;
			if ( $templates_count > 0 ) {
				$actions['sonotes_templates'] = sprintf(
					'<span style="color: #0073aa;">\ud83d\udcdd %d %s</span>',
					$templates_count,
					_n( 'template available', 'templates available', $templates_count, 'smart-order-notes' )
				);
			}
		}
		return $actions;
	}
}

SONotes_Order_UI::init();
