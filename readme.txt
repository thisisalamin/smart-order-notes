=== Smart Order Notes ===
Contributors: crafely
Tags: woocommerce, order notes, templates, admin, ecommerce
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
WC requires at least: 3.0
WC tested up to: 8.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Streamline WooCommerce order management with reusable note templates. Create, organize, and instantly insert predefined messages into order notes.

== Description ==

**Smart Order Notes** is a lightweight WooCommerce extension designed to dramatically speed up order management by providing reusable order note templates. Perfect for store administrators who frequently add similar notes to orders.

= ✨ Key Features =

* **Template Management** - Create, edit, and organize reusable order note templates
* **One-Click Insertion** - Insert templates instantly into WooCommerce order notes
* **Private & Customer Notes** - Choose whether notes are internal or sent to customers
* **Template Preview** - Preview template content before insertion
* **Smart Auto-Selection** - Automatically selects appropriate note type based on template settings
* **Clean Interface** - Minimal, intuitive UI that doesn't clutter your admin
* **HPOS Compatible** - Full support for WooCommerce High-Performance Order Storage
* **Role-Based Access** - Respects WooCommerce permissions and capabilities
* **Translation Ready** - Fully translatable with included POT file

= 🚀 Perfect For =

* **Customer Service Teams** - Standardize communication with consistent messaging
* **Store Managers** - Speed up order processing with quick note insertion
* **Multi-Admin Stores** - Ensure all team members use approved language
* **High-Volume Stores** - Reduce time spent typing repetitive notes

= 📋 Predefined Templates =

The plugin includes 12 professionally written templates to get you started:

* Order Shipped (Customer)
* Payment Received (Private)
* Order Delayed (Customer)
* Awaiting Stock (Private)
* Customer Contacted (Private)
* Refund Processed (Customer)
* Order Completed (Customer)
* Order Cancelled (Customer)
* Order Updated (Customer)
* Order Note Added (Private)
* Order Issue Reported (Private)
* Order Feedback Requested (Customer)

= 💡 How It Works =

1. **Create Templates** - Go to WooCommerce > Order Notes and create your reusable templates
2. **Choose Note Type** - Set each template as private (staff only) or customer (email notifications)
3. **Use on Orders** - Select templates from the sidebar when viewing any WooCommerce order
4. **Insert & Send** - One-click insertion with optional immediate sending to customers

= 🔧 Technical Features =

* **Zero Frontend Impact** - Admin-only plugin with no frontend queries
* **Minimal Resource Usage** - Less than 5KB of JavaScript and 3KB of CSS
* **Security First** - Nonce verification, capability checks, and sanitized inputs
* **Accessibility** - Full keyboard navigation and screen reader support
* **Responsive Design** - Works perfectly on all device sizes
* **Performance Optimized** - Efficient database queries and caching

= 🎯 Use Cases =

* **Shipping Notifications** - "Your order has been shipped and is on its way!"
* **Payment Confirmations** - "Payment received and order is being processed"
* **Delay Apologies** - "We apologize for the delay and are working to resolve this"
* **Support Responses** - "Our team has reviewed your request and will contact you shortly"
* **Status Updates** - "Your order status has been updated to processing"

= 🛠️ Developer Friendly =

* **Action Hooks** - `sonotes_template_inserted` when templates are used
* **Filter Hooks** - Modify template content, available templates, and note types
* **Clean Code** - Well-documented, follows WordPress coding standards
* **Extensible** - Easy to extend with custom functionality

== Installation ==

= Automatic Installation =

1. Login to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Smart Order Notes"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin zip file
2. Login to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded zip file and click "Install Now"
5. Activate the plugin

= Requirements =

* WordPress 5.0 or higher
* WooCommerce 3.0 or higher
* PHP 7.4 or higher
* User with `manage_woocommerce` capability

== Frequently Asked Questions ==

= Do I need WooCommerce for this plugin to work? =

Yes, Smart Order Notes is specifically designed for WooCommerce and requires it to be installed and activated.

= Can I create my own custom templates? =

Absolutely! You can create unlimited custom templates through WooCommerce > Order Notes in your admin dashboard.

= Will templates work with the new WooCommerce order screens? =

Yes, the plugin is fully compatible with both legacy WooCommerce order screens and the new High-Performance Order Storage (HPOS) system.

= Can I edit the predefined templates? =

Yes, all predefined templates can be edited, deleted, or customized to match your store's communication style.

= Do customer notes automatically send emails? =

When you select "Customer Note" and use "Insert & Send", the note will be added to the order and trigger WooCommerce's standard customer notification email.

= Is the plugin translation ready? =

Yes, the plugin is fully translatable. All strings are wrapped in translation functions and a POT file is included.

= Does this plugin slow down my site? =

No, Smart Order Notes is admin-only and has zero impact on your frontend performance. Assets are only loaded on relevant admin pages.

= Can I bulk apply templates to multiple orders? =

Currently, templates must be applied individually to orders. Bulk functionality may be added in future versions.

= What user roles can use this plugin? =

Any user with the `manage_woocommerce` capability can create and manage templates. Users with `edit_shop_orders` capability can use templates on order pages.

= Does this work with custom order statuses? =

Yes, the plugin works with any order status. Templates can be used regardless of the order's current status.

== Screenshots ==


1. **Template Management** - Clean interface for creating and managing note templates
2. **Order Integration** - Template selector appears in order sidebar for quick access

== Changelog ==

= 1.0.1 =
* Initial release
* Template management system with create, edit, delete functionality
* WooCommerce order page integration with sidebar metabox
* Support for both private and customer note types
* Template preview functionality
* Auto-selection of note types based on template defaults
* 12 professionally written predefined templates
* Full HPOS (High-Performance Order Storage) compatibility
* Responsive design for all device sizes
* Accessibility features with keyboard navigation
* Translation ready with complete internationalization
* Security features with nonce verification and capability checks
* Performance optimized with minimal resource usage

== Upgrade Notice ==

= 1.0.1 =
Initial release of Smart Order Notes. Install now to streamline your WooCommerce order management with reusable note templates.

== Additional Information ==

= Support =
For support, feature requests, and bug reports, please use the WordPress.org support forums or visit our website.

= Contributing =
Smart Order Notes is open source. Contribute on GitHub at https://github.com/thisisalamin/smart-order-notes

= Privacy =
This plugin does not collect, store, or transmit any user data. All functionality is local to your WordPress installation.

= Performance =
* Database queries: 0 on frontend, minimal on admin pages
* JavaScript: < 5KB (loaded only on relevant admin pages)
* CSS: < 3KB (loaded only when needed)
* No external API calls or dependencies
