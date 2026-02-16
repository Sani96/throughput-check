=== Throughput Check ===
Contributors: sani060913
Tags: wordpress performance, throughput test, stress test, load simulation, rest api benchmark
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Run a lightweight internal throughput simulation against your WordPress site and estimate requests per second with automatic mini-scaling.

== Description ==

**Throughput Check** is a lightweight WordPress performance testing plugin that estimates how many internal REST requests per second your site can handle.

It performs an internal PHP-level throughput simulation by sending concurrent REST API requests to your own WordPress installation.

No external services.  
No third-party tracking.  
No remote stress tools.

Everything runs inside your WordPress environment.

Perfect for:

- Developers testing server performance
- Agencies validating hosting environments
- Site owners checking capacity before traffic spikes
- Plugin developers comparing environments
- Anyone curious about internal PHP throughput

---

= Why Throughput Check? =

Most load testing tools:

- Require external services
- Add artificial network latency
- Require complicated configuration
- Are overkill for small sites

Throughput Check focuses on:

- Internal WordPress performance
- REST API execution speed
- PHP execution capacity
- Simple concurrency scaling
- Clear, readable performance metrics

It gives you a quick internal performance snapshot in seconds.

---

= How It Works =

The plugin:

1. Calls an internal REST endpoint.
2. Executes a small WordPress workload (get_option + WP_Query + cache touch).
3. Runs automatic mini-scaling at:
   - 5 concurrent requests
   - 15 concurrent requests
   - 30 concurrent requests
4. Calculates:
   - Average client response time
   - Batch duration
   - Estimated requests per second
   - Server p50 and p95 response time
   - Stability (errors detected or not)
5. Assigns a performance grade (A–D).

All tests are performed via internal REST requests to your own site.

No real external traffic is generated.

---

= Features =

- Internal REST-based throughput simulation
- Automatic mini-scaling (5 → 15 → 30 concurrency)
- Estimated requests per second (req/s)
- Client-side average response time
- Batch execution time
- Server p50 / p95 timing
- Error detection
- Performance grade (A, B, C, D)
- Environment snapshot (PHP, MySQL, memory, active plugins, etc.)
- Clean admin interface
- Copy-to-clipboard URL utility
- Lightweight codebase

---

= What This Is Not =

This plugin is NOT:

- A real-world external load test
- A CDN benchmark
- A browser performance tool
- A front-end speed test

It measures internal PHP + WordPress processing capacity.

Results may differ from real-world traffic affected by CDN, caching layers, or external network latency.

---

= Performance Grade System =

Grades are calculated based on estimated requests per second:

- A: ≥ 120 req/s
- B: ≥ 80 req/s
- C: ≥ 50 req/s
- D: < 50 req/s or errors detected

This provides a simple interpretation of internal throughput capacity.

---

= Admin Interface =

Access via:

**Tools → Throughput Check**

The admin page displays:

- Environment snapshot
- Mini-scaling test runner
- Performance KPIs
- Detailed stage table
- REST endpoint used

Everything runs with a single click.

---

= Technical Notes =

The test uses an internal REST endpoint:

/wp-json/throughput-check/v1/profile?mode=real

In "real" mode the endpoint performs:

- Multiple get_option() calls
- A small WP_Query (5 published posts)
- Cache set/get operation

This simulates a lightweight but realistic WordPress workload.

---

= Who Is It For? =

- WordPress developers
- Hosting testers
- Performance-focused agencies
- Plugin authors
- Technical site owners

---

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/throughput-check`
2. Activate the plugin through the WordPress admin
3. Go to **Tools → Throughput Check**
4. Click "Run Test"

---

== Frequently Asked Questions ==

= Does this generate real external traffic? =

No. All requests are internal REST calls to your own WordPress installation.

= Does this simulate real-world traffic? =

No. It simulates internal PHP-level throughput only.

External latency, CDN, and network conditions are not included.

= Is this safe to run on production? =

Yes. The mini-scaling is lightweight and limited to 5, 15, and 30 concurrent internal requests.

= Does it collect or transmit any data externally? =

No. The plugin does not send any data to external servers.

= Does it require a license key? =

The core functionality works without a license key.

---

== Screenshots ==

1. Main admin dashboard
2. Environment snapshot
3. Mini-scaling results table
4. Performance KPI summary with grade

---

== Changelog ==

= 1.0.0 =
* Initial release
* REST throughput simulation
* Mini-scaling (5 → 15 → 30)
* Performance grading system
* Environment snapshot
* Admin UI

---

== Upgrade Notice ==

= 1.0.0 =
Initial stable release of Throughput Check.
