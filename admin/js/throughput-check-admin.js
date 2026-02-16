/**
 * Admin JavaScript
 *
 * @package Throughput_Check
 */
(function ($) {
	'use strict';

	$(function () {
		$(document).on('click', '.tc-copy', function () {
			const sel = $(this).data('copy');
			const el = document.querySelector(sel);
			if (!el) return;
			const txt = el.textContent || '';
			navigator.clipboard?.writeText(txt).then(() => {
				$(this).text('Copied');
				setTimeout(() => $(this).text('Copy'), 900);
			});
		});

		const $btn = $('#tc-run');
		const $status = $('#tc-status');
		const $out = $('#tc-results');

		if (!$btn.length) return;

		function runStage(stage) {
			return $.post(throughput_check_admin.ajax_url, {
				action: 'throughput_check_run_test',
				nonce: throughput_check_admin.run_nonce,
				stage: stage
			});
		}

		function esc(s) {
			return String(s).replace(/[&<>"']/g, m => ({
				'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
			}[m]));
		}

		$btn.on('click', async function (e) {
			e.preventDefault();

			$btn.prop('disabled', true);
			$out.html('<p style="padding-left:10px">Running mini-scaling test (5 → 15 → 30)...</p>');
			$status.text('Starting...');

			const stages = ['small', 'medium', 'large'];
			const rows = [];

			try {
				for (let i = 0; i < stages.length; i++) {
					const stage = stages[i];
					$status.html(`<span class="tc-pill">${esc('Running ' + stage + '…')}</span>`);

					const res = await runStage(stage);
					if (!res || !res.success) throw new Error('Bad response');

					rows.push(res.data);
				}

				const okRows = rows.filter(r => Number(r.errors) === 0);
				const best = okRows.length ? okRows[okRows.length - 1] : rows[rows.length - 1];

				const grade = gradeFrom(best.estimated_rps, best.errors);
				const gradeLabel = gradeLabelFrom(grade);
				const bestStage = best.stage;

				const tableRows = rows.map(r => `
				<tr class="${r.stage === bestStage ? 'tc-best-row' : ''}">
					<td><span class="tc-pill tc-pill--stage">${esc(r.stage)}</span></td>
					<td><b>${esc(r.concurrency)}</b></td>
					<td>${esc(r.client_avg_ms)} ms</td>
					<td>${esc(r.batch_sec)} s</td>
					<td><b>${esc(r.estimated_rps)}</b> req/s</td>
					<td>${esc(r.server_p50_ms)} ms</td>
					<td>${esc(r.server_p95_ms)} ms</td>
					<td>${esc(r.errors)}</td>
					<td><span class="tc-pill tc-pill--${esc(String(r.stability).toLowerCase())}">${esc(r.stability)}</span></td>
				</tr>
				`).join('');

				$out.html(`
				<div class="tc-kpis">
					<div class="tc-kpi">
					<div class="tc-kpi__label">Best throughput</div>
					<div class="tc-kpi__value">${esc(best.estimated_rps)} <span class="tc-kpi__unit">req/s</span></div>
					<div class="tc-kpi__sub">at concurrency ${esc(best.concurrency)} (${esc(best.stage)})</div>
					</div>

					<div class="tc-kpi">
					<div class="tc-kpi__label">Performance grade</div>
					<div class="tc-kpi__value">
						<span class="tc-grade tc-grade--${esc(grade)}">${esc(grade)}</span>
						<span class="tc-kpi__sub">${esc(gradeLabel)}</span>
					</div>
					<div class="tc-kpi__sub tc-muted">
						A: ≥ 120 · B: ≥ 80 · C: ≥ 50 · D: &lt; 50 (or errors)
					</div>
					</div>

					<div class="tc-kpi">
					<div class="tc-kpi__label">Avg (client)</div>
					<div class="tc-kpi__value">${esc(best.client_avg_ms || best.avg_ms || '')} <span class="tc-kpi__unit">ms</span></div>
					<div class="tc-kpi__sub">batch ${esc(best.batch_sec)} s</div>
					</div>

					<div class="tc-kpi">
					<div class="tc-kpi__label">Server p95</div>
					<div class="tc-kpi__value">${esc(best.server_p95_ms)} <span class="tc-kpi__unit">ms</span></div>
					<div class="tc-kpi__sub">p50 ${esc(best.server_p50_ms)} ms · samples ${esc(best.server_samples || '')}</div>
					</div>
				</div>

				<div class="tc-section">
					<h3 class="tc-h3">Mini-scaling</h3>
					<table class="widefat striped tc-table">
					<thead>
						<tr>
						<th>Stage</th>
						<th>Conc</th>
						<th>Avg (client)</th>
						<th>Batch</th>
						<th>Throughput</th>
						<th>p50</th>
						<th>p95</th>
						<th>Err</th>
						<th>Stability</th>
						</tr>
					</thead>
					<tbody>${tableRows}</tbody>
					</table>
				</div>

				<div class="tc-urlbox">
					<div class="tc-urlbox__label">URL used</div>
					<div class="tc-urlbox__row">
					<code class="tc-code" id="tc-url">${esc(best.url_used)}</code>
					<button type="button" class="button tc-copy" data-copy="#tc-url">Copy</button>
					</div>
				</div>
				`);


			} catch (err) {
				$out.html('<p>Request failed.</p>');
			} finally {
				$btn.prop('disabled', false);
				$status.text('');
			}
		});
	});

	function gradeFrom(rps, errors) {
		rps = Number(rps) || 0;
		errors = Number(errors) || 0;

		if (errors > 0) return 'D';
		if (rps >= 120) return 'A';
		if (rps >= 80) return 'B';
		if (rps >= 50) return 'C';
		return 'D';
	}

	function gradeLabelFrom(grade) {
		switch (grade) {
			case 'A': return 'Excellent headroom';
			case 'B': return 'Solid for most sites';
			case 'C': return 'Watch traffic spikes';
			default: return 'High risk under load';
		}
	}

})(jQuery);
