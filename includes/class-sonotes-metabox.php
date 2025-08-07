<?php
/**
 * Handles template type metabox and saving meta
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SONotes_Metabox {
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_template_type_metabox' ) );
		add_action( 'save_post_sonotes_template', array( __CLASS__, 'save_template_type' ) );
	}

	/**
	 * Add metabox for template type selection
	 */
	public static function add_template_type_metabox() {
		add_meta_box(
			'sonotes_template_type',
			__( 'Note Type', 'smart-order-notes' ),
			array( __CLASS__, 'template_type_metabox_cb' ),
			'sonotes_template',
			'side',
			'high'
		);
	}

	/**
	 * Callback for template type metabox
	 */
	public static function template_type_metabox_cb( $post ) {
		// Add nonce field for security
		wp_nonce_field( 'sonotes_save_template_meta', 'sonotes_template_nonce' );

		$type = get_post_meta( $post->ID, '_sonotes_type', true );
		if ( empty( $type ) ) {
			$type = 'private'; // Default to private
		}
		?>
		<div class="sonotes-template-type-wrapper">
			<p>
				<label for="sonotes_type_select">
					<strong><?php esc_html_e( 'Default Note Type:', 'smart-order-notes' ); ?></strong>
				</label>
			</p>
			<p>
				<select name="sonotes_type" id="sonotes_type_select" style="width: 100%;">
					<option value="private" <?php selected( $type, 'private' ); ?>>
						<?php esc_html_e( 'Private Note (staff only)', 'smart-order-notes' ); ?>
					</option>
					<option value="customer" <?php selected( $type, 'customer' ); ?>>
						<?php esc_html_e( 'Customer Note (sent to customer)', 'smart-order-notes' ); ?>
					</option>
				</select>
			</p>
			<div class="sonotes-help-text">
				<p class="description">
					<?php esc_html_e( 'Choose the default note type for this template. Users can still change this when inserting the note.', 'smart-order-notes' ); ?>
				</p>
			</div>
		</div>
		<style>
		.sonotes-template-type-wrapper {
			padding: 5px 0;
		}
		.sonotes-help-text {
			margin-top: 10px;
			padding: 8px 10px;
			background: #f6f7f7;
			border-left: 4px solid #72777c;
		}
		.sonotes-help-text .description {
			margin: 0;
			font-style: italic;
			color: #666;
		}
		</style>
		<?php
	}

	/**
	 * Save template type meta data
	 */
	public static function save_template_type( $post_id ) {
		// Check if nonce is valid
		if ( ! isset( $_POST['sonotes_template_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sonotes_template_nonce'] ) ), 'sonotes_save_template_meta' ) ) {
			return;
		}

		// Check if user has permission to edit this post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Save the template type
		if ( isset( $_POST['sonotes_type'] ) ) {
			$type = sanitize_text_field( wp_unslash( $_POST['sonotes_type'] ) );
			if ( in_array( $type, array( 'private', 'customer' ), true ) ) {
				update_post_meta( $post_id, '_sonotes_type', $type );
			}
		}
	}
}

SONotes_Metabox::init();
