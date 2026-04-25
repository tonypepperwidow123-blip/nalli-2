=== Nalli MegaMenu ===
Contributors: yourname
Tags: elementor, mega menu, header, navigation, woocommerce
Requires at least: 5.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
Requires Plugins: elementor
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced Elementor Header MegaMenu widget with scrolling announcement bar, sticky header, and image-based mega menu.

== Description ==

**Nalli MegaMenu** provides one powerful Elementor widget — **Header MegaMenu** — that brings luxury Indian fashion retail UI patterns to any WordPress site.

= Features =

* **Scrolling Announcement Bar** — infinite ticker with configurable speed; pauses on hover
* **Sticky Header** — optional fixed position with scroll-triggered drop shadow
* **Image-Based Mega Menu** — full-width dropdown panel with left tab sidebar + category image grid
* **Tab Filtering** — click sidebar tabs to filter category cards (Wedding, Festive, Events…)
* **Mobile Drawer** — slide-in accordion navigation with focus trap and proper ARIA
* **Currency Selector** — flag + label + chevron UI
* **Utility Icons** — search, account, wishlist, cart (with count badges) using thin-stroke SVGs
* **Fully Elementor-Controlled** — every color, size, typography, and layout option is a proper Elementor control
* **CSS Variable Architecture** — all style controls write to CSS custom properties via `selectors`
* **WCAG AA accessible** — aria roles, labels, keyboard navigation, focus management
* **No global enqueue** — assets only load when the widget is present on the page

= Design =

Refined luxury / editorial aesthetic inspired by Nalli Silks:
* Deep Crimson `#8B1C2C` accent
* Off-white `#FAF8F6` tab sidebar
* Charcoal `#333` nav text
* Smooth `opacity + translateY` mega menu reveal (0.22s)
* Image hover `scale(1.04)` (0.3s)

== Installation ==

1. Upload the `nalli-megamenu` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins > Installed Plugins**
3. Make sure **Elementor** is installed and activated
4. Edit any page with Elementor, search for **"Header MegaMenu"** widget (under the **Nalli MegaMenu** category), and drag it onto the canvas

== Frequently Asked Questions ==

= Do I need WooCommerce for the cart/wishlist icons? =

No. The icons are purely presentational links. The cart and wishlist counts show `0` by default. You can hook into WooCommerce or any wishlist plugin to update the count spans via JavaScript.

= How do I connect mega menu items to a nav item? =

In the **MegaMenu Items** repeater, set the **Parent Nav Label** field to exactly match the **Label** of a nav item that has **Has Mega Menu?** enabled (case-sensitive).

= How do I set up sidebar tabs? =

In **MegaMenu Sidebar Tabs**, add a tab entry and set:
* **Parent Nav Label** → matches the nav item label
* **Filter Key** → a slug (e.g. `wedding`)

Then on each category card in **MegaMenu Items**, set **Tab Key** to the matching slug.

= Is it responsive? =

Yes. On tablet (≤1024px) the desktop nav hides and the mobile drawer is shown. Columns in the mega grid are separately controllable per device.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
