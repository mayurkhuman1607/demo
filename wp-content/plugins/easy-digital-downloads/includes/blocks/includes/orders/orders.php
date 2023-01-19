<?php
/**
 * Orders blocks.
 *
 * @package   edd-blocks
 * @copyright 2022 Easy Digital Downloads
 * @license   GPL2+
 * @since 2.0
 */

namespace EDD\Blocks\Orders;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use EDD\Blocks\Functions as Helpers;

require_once EDD_BLOCKS_DIR . 'includes/orders/functions.php';

add_action( 'init', __NAMESPACE__ . '\register' );
/**
 * Registers all of the EDD core blocks.
 *
 * @since 2.0
 * @return void
 */
function register() {
	$blocks = array(
		'order-history' => array(
			'render_callback' => __NAMESPACE__ . '\orders',
		),
		'confirmation'  => array(
			'render_callback' => __NAMESPACE__ . '\confirmation',
		),
		'receipt'       => array(
			'render_callback' => __NAMESPACE__ . '\receipt',
		),
	);

	foreach ( $blocks as $block => $args ) {
		register_block_type( EDD_BLOCKS_DIR . 'build/' . $block, $args );
	}
}

/**
 * Renders the order history block.
 *
 * @since 2.0
 * @param array  $block_attributes The block attributes.
 * @return string
 */
function orders( $block_attributes = array() ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	if ( edd_user_pending_verification() ) {
		ob_start();
		if ( ! empty( $_GET['edd-verify-request'] ) ) :
			?>
			<p class="edd-account-pending edd_success">
				<?php esc_html_e( 'An email with an activation link has been sent.', 'easy-digital-downloads' ); ?>
			</p>
		<?php endif; ?>
		<p class="edd-account-pending">
			<?php
			printf(
				wp_kses_post(
					/* translators: 1. Opening anchor tag. 2. Closing anchor tag. */
					__( 'Your account is pending verification. Please click the link in your email to activate your account. No email? %1$sSend a new activation code.%2$s', 'easy-digital-downloads' )
				),
				'<a href="' . esc_url( edd_get_user_verification_request_url() ) . '">',
				'</a>'
			);
			?>
		</p>
		<?php

		return ob_get_clean();
	}

	$block_attributes = wp_parse_args(
		$block_attributes,
		array(
			'number'  => 20,
			'columns' => 2,
		)
	);

	$number = (int) $block_attributes['number'];
	$args   = Functions\get_order_history_args( $block_attributes );

	// Set up classes.
	$classes = array(
		'wp-block-edd-orders',
		'edd-blocks__orders',
	);
	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
		<?php
		$classes = Helpers\get_block_classes( $block_attributes, array( 'edd-blocks__orders-grid' ) );
		$orders  = edd_get_orders( $args );
		include EDD_BLOCKS_DIR . 'views/orders/orders.php';

		unset( $args['number'], $args['offset'] );
		$count = edd_count_orders( $args );
		include EDD_BLOCKS_DIR . 'views/orders/pagination.php';
		?>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Renders the order confirmation block.
 *
 * @since 2.0
 * @return string
 */
function confirmation( $block_attributes = array() ) {
	$session = Functions\get_purchase_session();
	if ( empty( $session['purchase_key'] ) ) {
		if ( Helpers\is_block_editor() ) {
			return '<p class="edd-alert edd-alert-info">' . esc_html(  __( 'To view a sample confirmation screen, you need to have at least one order in your store.', 'easy-digital-downloads' ) ) . '</p>';
		}

		return '<p class="edd-alert edd-alert-error">' . esc_html( __( 'Your purchase session could not be retrieved.', 'easy-digital-downloads' ) ) . '</p>';
	}

	global $edd_receipt_args;

	$edd_receipt_args = wp_parse_args(
		$block_attributes,
		array(
			'payment_key'    => false,
			'payment_method' => true,
		)
	);

	// Set up classes.
	$classes = array(
		'wp-block-edd-confirmation',
		'edd-blocks__confirmation',
	);
	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
		<?php
		$order                  = edd_get_order_by( 'payment_key', $session['purchase_key'] );
		$edd_receipt_args['id'] = $order->id;
		include EDD_BLOCKS_DIR . 'views/orders/receipt-items.php';
		include EDD_BLOCKS_DIR . 'views/orders/totals.php';
		?>
		<div class="edd-blocks__confirmation-details">
			<a href="<?php echo esc_url( edd_get_receipt_page_uri( $order->id ) ); ?>">
				<?php esc_html_e( 'View Details and Downloads', 'easy-digital-downloads' ); ?>
			</a>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Renders the full order receipt.
 *
 * @since 2.0
 * @param array $block_attributes
 * @return string
 */
function receipt( $block_attributes = array() ) {
	global $edd_receipt_args;

	$edd_receipt_args = wp_parse_args(
		$block_attributes,
		array(
			'error'          => __( 'Sorry, trouble retrieving order receipt.', 'easy-digital-downloads' ),
			'payment_key'    => false,
			'payment_method' => true,
		)
	);
	$payment_key      = Functions\get_payment_key();

	// No key found.
	if ( ! $payment_key ) {
		if ( Helpers\is_block_editor() ) {
			return '<p class="edd-alert edd-alert-info">' . esc_html( __( 'To view a sample receipt, you need to have at least one order in your store.', 'easy-digital-downloads' ) ) . '</p>';
		}

		return '<p class="edd-alert edd-alert-error">' . esc_html( $edd_receipt_args['error'] ) . '</p>';
	}

	ob_start();
	edd_print_errors();

	$order         = edd_get_order_by( 'payment_key', $payment_key );
	$user_can_view = edd_can_view_receipt( $payment_key );
	if ( ! $user_can_view ) {
		show_no_access_message( $order );

		return ob_get_clean();
	}

	$classes = array(
		'wp-block-edd-receipt',
		'edd-blocks__receipt',
	);
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
		<?php
		include EDD_BLOCKS_DIR . 'views/orders/totals.php';
		maybe_show_receipt( $order );
		?>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Shows the message if the current viewer doesn't have access to any order information.
 *
 * @since 2.0
 * @param EDD\Orders\Order $order
 * @return void
 */
function show_no_access_message( $order ) {
	// User is logged in, but does not have access.
	if ( is_user_logged_in() ) {
		printf(
			'<p class="edd-alert edd-alert-error">%s</p>',
			esc_html__( 'Sorry, you do not have permission to view this receipt.', 'easy-digital-downloads' )
		);
		return;
	}

	// User is not logged in and can view a guest order.
	if ( empty( $order->user_id ) ) {
		printf(
			'<p>%s</p>',
			esc_html__( 'Please confirm your email address to access your downloads.', 'easy-digital-downloads' )
		);
		include EDD_BLOCKS_DIR . 'views/orders/guest.php';

		return;
	}

	// Otherwise, the order was made by a customer with a user account.
	printf(
		'<p>%s</p>',
		esc_html__( 'Please log in to view your order.', 'easy-digital-downloads' )
	);
	echo \EDD\Blocks\Forms\login( array( 'redirect' => edd_get_receipt_page_uri( $order->id ) ) );
}

/**
 * Shows the full receipt details if criteria are met; otherwise show a verification or login form.
 *
 * @since 2.0
 * @param EDD\Orders\Order $order
 * @return void
 */
function maybe_show_receipt( $order ) {
	$session = edd_get_purchase_session();
	if ( is_user_logged_in() || ( ! empty( $session['purchase_key'] ) && $session['purchase_key'] === $order->payment_key ) ) {
		global $edd_receipt_args;
		include EDD_BLOCKS_DIR . 'views/orders/receipt-items.php';

		/**
		 * Fires after the order receipt table.
		 *
		 * @since 3.0
		 * @param \EDD\Orders\Order $order          Current order.
		 * @param array             $edd_receipt_args [edd_receipt] shortcode arguments.
		 */
		do_action( 'edd_order_receipt_after_table', $order, $edd_receipt_args );
		return;
	}

	// The order belongs to a registered WordPress user.
	?>
	<p>
		<?php esc_html_e( 'Please log in to access your downloads.', 'easy-digital-downloads' ); ?>
	</p>
	<?php
	echo \EDD\Blocks\Forms\login( array( 'current' => true ) );
}

add_action( 'edd_view_receipt_guest', __NAMESPACE__ . '\verify_guest_email' );
/**
 * Verfies the email address to view the details for a guest order.
 *
 * @since 2.0
 * @param array $data
 * @return void
 */
function verify_guest_email( $data ) {
	if ( empty( $data['edd_guest_email'] ) || empty( $data['edd_guest_nonce'] ) || ! wp_verify_nonce( $data['edd_guest_nonce'], 'edd-guest-nonce' ) ) {
		edd_set_error( 'edd-guest-error', __( 'Your email address could not be verified.', 'easy-digital-downloads' ) );
		return;
	}
	$order = edd_get_order( $data['order_id'] );
	if ( $order instanceof \EDD\Orders\Order && $data['edd_guest_email'] === $order->email ) {
		edd_set_purchase_session(
			array(
				'purchase_key' => $order->payment_key,
			)
		);
		return;
	}
	edd_set_error( 'edd-guest-error', __( 'Your email address could not be verified.', 'easy-digital-downloads' ) );
}
