=== Red-Headed Lite — Simple Orders Export ===
Contributors: thelionfrog
Tags: woocommerce, export, csv, sftp, email
Requires at least: 6.2
Tested up to: 6.6
Requires PHP: 8.1
Stable tag: 1.4.40
License: GPL-2.0-or-later

Exports WooCommerce orders the simple way — manual or bulk to CSV, delivered by Email or SFTP, or downloaded straight to your computer. Part of Ultimate Woo Powertools (by The Lion Frog).

== Description ==

**Red-Headed Lite** is the free starting point of the Red-Headed family — a no-friction way to export WooCommerce orders to CSV. Run a one-shot export from the order screen, use the bulk action on the WC orders list, deliver the file by email, push it to your SFTP, or grab it as a direct download. One profile, one destination per profile, zero subscription.

= What you get (free) =
* Manual + bulk export of WooCommerce orders from the WC orders list
* CSV format only
* Email delivery (capped at 30 sends per 24h sliding window, anti-spam)
* SFTP delivery (encrypted password storage, AES-256-CBC)
* Direct download (browser stream)
* 1 export profile
* 1 destination per profile
* HPOS (Custom Order Tables) compatible
* GDPR-friendly defaults
* Lion Frog Hub Soft-Lock landing for upgrade visibility

= Pro features =
**Red-Headed Pro** unlocks everything the team needs to industrialize order exports:

* **5 extra formats:** JSON, XML, XLSX, NDJSON, TSV
* **Unlimited email delivery** (no 30/24h cap)
* **3 extra destinations:** Google Drive (OAuth), REST endpoint (Bearer / Basic / custom header), Local ZIP archive
* **Unlimited export profiles**
* **Multi-destinations per profile** (one export, simultaneous fan-out)
* **Cron scheduling:** hourly / daily / weekly (and custom intervals)
* **Auto-trigger on WC order status change** (with dedupe + min-total threshold)
* **Advanced filters:** date range, payment method, category, SKU, min/max amount
* **Visual field mapper:** drag-and-drop column picker with header rename + transforms
* **Computed columns:** formulas across order fields
* **Line-item export mode** (one row per product instead of one row per order)
* **Post-export status change** (auto-set "processing" → "completed" after a successful export)
* **Custom WC statuses** (export-aware non-native statuses, e.g. `wc-rh-exported`)
* **REST API** endpoints (`/pelican/v1/profiles`, `/jobs`)
* **HMAC-signed webhooks** (`export.generated`, `export.delivered`, `export.failed`, SHA-256, retry ×3 exponential)
* **PolyLang & WPML** compatible (translatable email subject + body)

Pro features are visible inside Lite but soft-locked — upgrade in one click from the Hub.

== Installation ==

1. Upload `red-headed-lite` to `/wp-content/plugins/`.
2. Activate it.
3. Go to **Froggy Hub → Red-Headed** to configure.

== Changelog ==

= 1.4.40 - 2026-05-17 =
* **Docs homogenization** — readme.txt rewritten to reflect actual Lite/Pro feature parity verified against the Soft_Lock matrix.

= 1.4.4 =
* **Fix (CRITICAL):** Removed obsolete legacy main file (`woo-order-lite.php`). Both `woo-order-lite.php` and `red-headed-lite.php` carried a `Plugin Name:` header → WordPress loaded them as TWO plugins → fatal "Cannot redeclare" on activation. Plugin was unactivatable since the rebrand.

= 1.1.0 — 2026-04-30 =
* Verbal rebrand: ships as **Red-Headed Lite — Simple Orders Export**.
* Slug renamed `pelican-lite` → `red-headed-lite` (final naming).
* Mascot: Red-Headed Poison Frog.
* Backward-compatible with v1.0.0 data — no migration required.

= 1.0.0 — 2026-04-30 =
* Initial release (as Pélican).
