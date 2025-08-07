<?php
/**
 * Handles plugin activation and predefined templates
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SONotes_Activation class handles plugin activation and predefined templates
 */
class SONotes_Activation {
	/**
	 * Create predefined templates on plugin activation
	 */
	public static function create_predefined_templates() {
		// Check if templates already exist
		$existing = get_posts(
			array(
				'post_type'      => 'sonotes_template',
				'posts_per_page' => 1,
				'post_status'    => 'any',
			)
		);

		if ( ! empty( $existing ) ) {
			return; // Templates already exist
		}

		// Define predefined templates
		$predefined = array(
			array(
				'post_title'   => 'Order Shipped',
				'post_content' => 'Great news! Your order has been shipped and is on its way to you. You will receive tracking details via email shortly.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Payment Received',
				'post_content' => 'Payment confirmed and received. Order is now being processed and will be shipped soon.',
				'meta'         => array( '_sonotes_type' => 'private' ),
			),
			array(
				'post_title'   => 'Order Delayed',
				'post_content' => 'We apologize for the delay with your order. We are working to resolve this and will update you as soon as possible.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Awaiting Stock',
				'post_content' => 'Order is waiting for stock replenishment. Customer has been notified of expected delay.',
				'meta'         => array( '_sonotes_type' => 'private' ),
			),
			array(
				'post_title'   => 'Customer Contacted',
				'post_content' => 'Customer contacted regarding order details. Waiting for customer response.',
				'meta'         => array( '_sonotes_type' => 'private' ),
			),
			array(
				'post_title'   => 'Refund Processed',
				'post_content' => 'Your refund has been processed and should appear in your account within 3-5 business days.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Order Completed',
				'post_content' => 'Your order has been completed successfully. Thank you for shopping with us!',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Order Cancelled',
				'post_content' => 'Your order has been cancelled as per your request. If you have any questions, please contact support.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Order Updated',
				'post_content' => 'Your order details have been updated. Please check your email for the latest information.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
			array(
				'post_title'   => 'Order Note Added',
				'post_content' => 'A new note has been added to your order. Please review the details in your account.',
				'meta'         => array( '_sonotes_type' => 'private' ),
			),
			array(
				'post_title'   => 'Order Issue Reported',
				'post_content' => 'An issue has been reported with your order. Our support team is looking into it and will contact you shortly.',
				'meta'         => array( '_sonotes_type' => 'private' ),
			),
			array(
				'post_title'   => 'Order Feedback Requested',
				'post_content' => 'We would love your feedback on your recent order. Please take a moment to share your thoughts with us.',
				'meta'         => array( '_sonotes_type' => 'customer' ),
			),
		);

		// Create each predefined template
		foreach ( $predefined as $template ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'sonotes_template',
					'post_status'  => 'publish',
					'post_title'   => $template['post_title'],
					'post_content' => $template['post_content'],
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Add meta data
				foreach ( $template['meta'] as $meta_key => $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}
	}
}
