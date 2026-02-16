<?php
/**
 * Admin View: Throughput Check Footer Bar
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
	<div id="throughput-check-footer-bar">
		<span>
			<?php esc_html_e( 'Discover more on', 'throughput-check' ); ?>
			<a href="<?php echo esc_url( 'https://wpsani.store' ); ?>" target="_blank">
				wpsani.store
			</a>
		</span>
	</div>