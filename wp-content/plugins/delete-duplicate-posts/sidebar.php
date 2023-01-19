<?php

// this is an include only WP file
if ( !defined( 'ABSPATH' ) ) {
    die;
}
?>

<div id="sidebar-container" class="ddp_content_cell">
	<?php 
global  $ddp_fs ;

if ( !$ddp_fs->is_registered() && !$ddp_fs->is_pending_activation() ) {
    ?>
		<div class="sidebarrow optin">
			<h3><span class="dashicons dashicons-warning"></span>
				<?php 
    esc_html_e( 'Help us improve!', 'delete-duplicate-posts' );
    ?></h3>
			<p>
				<?php 
    esc_html_e( 'Opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'delete-duplicate-posts' );
    ?>
			</p>
			<a href="javascript:;" class="button button-secondary" onclick="cp_ddp_freemius_opt_in(this)" data-opt="yes"><?php 
    esc_html_e( 'Click here to opt in.', 'delete-duplicate-posts' );
    ?></a>
			<div id="cp-ddp-opt-spin" class="spinner"></div><input type="hidden" id="cp-ddp-freemius-opt-nonce" value="<?php 
    echo  esc_attr( wp_create_nonce( 'cp-ddp-freemius-opt' ) ) ;
    ?>" />
		</div>
	<?php 
}

$ddp_deleted_duplicates = get_option( 'ddp_deleted_duplicates' );

if ( $ddp_deleted_duplicates ) {
    ?>
		<div class="sidebarrow">
			<h3>
				<?php 
    printf(
        /* translators: %s: Number of deleted posts */
        esc_html__( '%s duplicates deleted!', 'delete-duplicate-posts' ),
        esc_html( number_format_i18n( $ddp_deleted_duplicates ) )
    );
    ?>
			</h3>
		</div>
	<?php 
}

?>

	<div class="sidebarrow">
		<p class="warning">
			<?php 
esc_html_e( 'We recommend you always make a backup before running this tool.', 'delete-duplicate-posts' );
?>
		</p>
	</div>

	<?php 
$display_promotion = true;

if ( $display_promotion ) {
    ?>
		<hr>

		<div class="sidebarrow ddppro">
			<h3><span class="dashicons dashicons-star-filled"></span> DDP Pro <span class="dashicons dashicons-star-filled"></span></h3>
			<ul class="linklist">
				<li><strong>301 Redirects</strong> - Automatically redirect deleted duplicates.</li>
				<li><strong>New compare method</strong> - Compare by meta tag <span>Use with WooCommerce (compare SKU) or other plugins</span></li>
				<li><strong>Choose post status</strong> - Look for duplicates in scheduled posts, drafts or any other available post status on your site.</li>
				<li><strong>Premium Support</strong> - Get help from the developers behind the plugin.</li>
				<?php 
    /*
    <li>301 Redirect deleted posts <span>Make sure traffic is directed to the right place</span></li>
    */
    ?>
				<li><strong>No ads</strong> - Support the developer ;-)</li>
			</ul>

			<a href="<?php 
    echo  esc_url( $ddp_fs->pricing_url() ) ;
    ?>" class="button button-primary" id="ddpprobutton">$14.99 <del>$19.99</del> /year</br><?php 
    _e( 'Click here', 'delete-duplicate-posts' );
    ?></a>
			<p>
				<center><em>Or get <a href="https://checkout.freemius.com/mode/dialog/plugin/925/plan/9473/licenses/1/?billing_cycle=lifetime" target="_blank">a lifetime license for only $29.99</a></em></center>
			</p>
			<div class="moneybackguarantee">
				<p><strong>Money Back Guarantee!</strong></p>
				<p>You are fully protected by our 100% Money Back Guarantee. If during the next 30 days you experience an issue that makes the plugin unusable and we are unable to resolve it, we'll happily consider offering a full refund of your money.</p>
			</div>
		</div><!-- .sidebarrow -->
		<hr>

	<?php 
}

?>

	<div class="sidebarrow">
		<h3><?php 
esc_html_e( 'Our other plugins', 'delete-duplicate-posts' );
?></h3>
		<a href="https://wpsecurityninja.com" target="_blank" style="float: right;" rel="noopener"><img src="<?php 
echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/security-ninja-logo.png' ) ;
?>" alt="Visit wpsecurityninja.com" class="logo"></a>

		<strong>WordPress Security made easy</strong>
		<p>Complete WordPress site protection with firewall, malware scanner, scheduled scans, security tests and much more - all you need to keep your website secure. Free trial.</p>

		<p><a href="https://wpsecurityninja.com/" target="_blank" rel="noopener" class="button button-primary">Visit wpsecurityninja.com</a></p>
		<br />
		<a href="https://cleverplugins.com" target="_blank" style="float: right;" rel="noopener"><img src="<?php 
echo  esc_url( plugin_dir_url( __FILE__ ) . 'images/seoboosterlogo.png' ) ;
?>" alt="Visit cleverplugins.com" class="logo"></a>
		<p>SEO Booster is a powerful tool for anyone serious about SEO. <a href="https://wordpress.org/plugins/seo-booster/" target="_blank" rel="noopener">wordpress.org/plugins/seo-booster/</a><br />
		<p><a href="https://cleverplugins.com/" target="_blank" rel="noopener" class="button button-primary">Visit cleverplugins.com</a></p>

	</div><!-- .sidebarrow -->
	<div class="sidebarrow">
		<h3>Need help?</h3>
		<p>Email support only for pro customers.</p>
		<p>Free users: <a href="https://wordpress.org/support/plugin/delete-duplicate-posts/" target="_blank" rel="noopener"><?php 
esc_html_e( 'Support Forum on WordPress.org', 'delete-duplicate-posts' );
?></a></p>
		<form method="post" id="ddp_reactivate">
			<?php 
wp_nonce_field( 'ddp_reactivate_nonce' );
?>
			<input class="button button-secondary button-small" type="submit" name="ddp_reactivate" value="<?php 
esc_html_e( 'Recreate Databases', 'delete-duplicate-posts' );
?>" />
		</form>
	</div><!-- .sidebarrow -->
</div>