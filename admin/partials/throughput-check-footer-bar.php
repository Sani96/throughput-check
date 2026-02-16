<?php
/**
 * Admin View: Throughput Check Footer Bar
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
	<div id="throughput-check-footer-bar" class="wpsani-footer-bar">
		<span>
			<?php esc_html_e( 'Discover more on', 'throughput-check' ); ?>
			<a href="<?php echo esc_url( 'https://wpsani.store/downloads/throughput-check?utm_source=plugin&utm_medium=footer&utm_campaign=throughput_check' ); ?>" target="_blank">
				wpsani.store
			</a>
		</span>
	</div>