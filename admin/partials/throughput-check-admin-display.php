<?php
/**
 * Admin View: Throughput Check Main Display
 *
 * @package Throughput_Check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! isset( $snapshot ) || ! is_array( $snapshot ) ) {
	$snapshot = array();
}
?>

<div class="wrap tc-wrap">
	<div class="tc-hero">
	<div class="tc-hero__left">
		<h1 class="tc-title"><?php echo esc_html__( 'Throughput Check', 'throughput-check' ); ?></h1>
		<p class="tc-sub">
		<?php echo esc_html__( 'Internal PHP-level throughput simulation (requests to your own site).', 'throughput-check' ); ?>
		</p>
		<p class="tc-note">
		<?php echo esc_html__( 'Results can differ from real-world traffic (CDN, caching layers, external latency).', 'throughput-check' ); ?>
		</p>
	</div>

	<div class="tc-hero__right">
		<button id="tc-run" class="button button-primary tc-btn">
		<?php echo esc_html__( 'Run Test', 'throughput-check' ); ?>
		</button>
		<span id="tc-status" class="tc-status" aria-live="polite"></span>

		<div class="tc-hint">
		<?php echo esc_html__( 'Mini-scaling runs automatically at 5 → 15 → 30 concurrency.', 'throughput-check' ); ?>
		</div>
	</div>
	</div>

	<div class="tc-card">
	<div class="tc-card__head">
		<h2><?php echo esc_html__( 'Environment snapshot', 'throughput-check' ); ?></h2>
	</div>
	<div class="tc-card__body">
		<table class="widefat striped tc-table--env">
		<tbody>
		<?php foreach ( $snapshot as $label => $value ) : ?>
			<tr>
			<th><?php echo esc_html( $label ); ?></th>
			<td><?php echo esc_html( $value ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	</div>
	</div>

	<div id="tc-results" class="tc-card tc-results">
	<div class="tc-card__head">
		<h2><?php echo esc_html__( 'Results', 'throughput-check' ); ?></h2>
		<div class="tc-card__meta"><?php echo esc_html__( 'Run the test to see KPIs and scaling table.', 'throughput-check' ); ?></div>
	</div>
	<div class="tc-card__body">
		<p style="margin:0;"><?php echo esc_html__( 'Results will appear here.', 'throughput-check' ); ?></p>
	</div>
	</div>
</div>
