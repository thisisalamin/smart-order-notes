<?php
/**
 * Handles template type metabox and saving meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add metabox for template type selection
 */
function sonotes_add_template_type_metabox() {
	add_meta_box(
		'sonotes_template_type',
		__( 'Note Type', 'smart-order-notes' ),
		'sonotes_template_type_metabox_cb',
		'sonotes_template',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sonotes_add_template_type_metabox' );

/**
 * Callback for template type metabox
 */
function sonotes_template_type_metabox_cb( $post ) {
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
				<strong><?php _e( 'Default Note Type:', 'smart-order-notes' ); ?></strong>
			</label>
		</p>
		<p>
			<select name="sonotes_type" id="sonotes_type_select" style="width: 100%;">
				<option value="private" <?php selected( $type, 'private' ); ?>>
					<?php _e( 'Private Note (staff only)', 'smart-order-notes' ); ?>
				</option>
				<option value="customer" <?php selected( $type, 'customer' ); ?>>
					<?php _e( 'Customer Note (sent to customer)', 'smart-order-notes' ); ?>
				</option>
			</select>
		</p>
		<div class="sonotes-help-text">
			<p class="description">
				<?php _e( 'Choose the default note type for this template. Users can still change this when inserting the note.', 'smart-order-notes' ); ?>
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
function sonotes_save_template_type( $post_id ) {
	// Check if nonce is valid
	if ( ! isset( $_POST['sonotes_template_nonce'] ) ||
		! wp_verify_nonce( $_POST['sonotes_template_nonce'], 'sonotes_save_template_meta' ) ) {
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
		$type = sanitize_text_field( $_POST['sonotes_type'] );
		if ( in_array( $type, array( 'private', 'customer' ) ) ) {
			update_post_meta( $post_id, '_sonotes_type', $type );
		}
	}
}
add_action( 'save_post_sonotes_template', 'sonotes_save_template_type' );

/**
 * Add help text to template edit screen
 */
function sonotes_template_help_text() {
	$screen = get_current_screen();
	if ( $screen && $screen->post_type === 'sonotes_template' ) {
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php _e( 'Creating Order Note Templates', 'smart-order-notes' ); ?></strong>
			</p>
			<ul style="margin-left: 20px;">
				<li><?php _e( 'Use the title field for a descriptive template name', 'smart-order-notes' ); ?></li>
				<li><?php _e( 'Write your note content in the editor below', 'smart-order-notes' ); ?></li>
				<li><?php _e( 'Choose whether this should be a private note (staff only) or customer note (sent to customer)', 'smart-order-notes' ); ?></li>
				<li><?php _e( 'Templates will appear in a dropdown on WooCommerce order pages', 'smart-order-notes' ); ?></li>
			</ul>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'sonotes_template_help_text' );
