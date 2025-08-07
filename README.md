# Smart Order Notes

A lightweight WooCommerce extension designed to simplify and speed up order management by letting store admins create reusable order note templates.

## Features


### ✨ Core Features
- **Create & Manage Templates**: Admin interface to add, edit, and delete note templates
- **Quick Insert on Orders**: Dropdown selector in WooCommerce order admin that inserts template text into order notes
- **Private & Customer Notes**: Choose whether the template applies as a private note (for staff) or customer note (sent to customer)
- **Clean UI**: Minimal, intuitive interface to avoid clutter
- **Role-based Access**: Only users with proper WooCommerce permissions can manage or use templates
- **Lightweight & Fast**: No heavy dependencies or performance impact

## 🛠️ Planned Features

- **Template Management Enhancements**
  - Allow users to create/edit/delete templates from the order page (modal or quick link)
  - Add categories/tags for templates for better organization
- **Template Variables**
  - Support dynamic variables in templates (e.g., {customer_name}, {order_total}) that auto-populate when inserted
- **Bulk Actions**
  - Allow bulk insertion of notes to multiple orders (from the orders list)
- **Logging & History**
  - Keep a log of which templates were used, by whom, and when, for audit purposes
- **Settings Page**
  - Add a settings page to configure plugin options (default note type, permissions, etc.)

### 🚀 Enhanced Features
- **Template Preview**: See template content before inserting
- **Smart Note Type Detection**: Automatically suggests note type based on template settings
- **Multiple WooCommerce Versions**: Compatible with both legacy and HPOS (High-Performance Order Storage)
- **Accessibility**: Full keyboard navigation and screen reader support
- **Responsive Design**: Works on all device sizes

## Installation

1. Upload the `smart-order-notes` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and activated
4. Navigate to WooCommerce > Order Notes to manage templates

## Usage

### Creating Templates

1. Go to **WooCommerce > Order Notes** in your admin dashboard
2. Click **Add New Template**
3. Enter a descriptive title for your template
4. Write the note content in the editor
5. Choose the default note type (Private or Customer)
6. Publish the template

### Using Templates on Orders

1. Open any WooCommerce order in the admin
2. Look for the **Order Note Templates** metabox in the sidebar
3. Select a template from the dropdown
4. Choose the note type (Private or Customer)
5. Click **Insert Template** to add it to the order notes

### Predefined Templates

The plugin includes these predefined templates to get you started:

- **Order Shipped** (Customer) - Notify customer their order has shipped
- **Payment Received** (Private) - Internal note about payment confirmation
- **Order Delayed** (Customer) - Apologize for delays and provide updates
- **Awaiting Stock** (Private) - Internal note about stock issues
- **Customer Contacted** (Private) - Note about customer communication
- **Refund Processed** (Customer) - Inform customer about refund status

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.4 or higher

## File Structure

```
smart-order-notes/
├── smart-order-notes.php          # Main plugin file
├── includes/
│   ├── class-sonotes-activation.php   # Activation & predefined templates
│   ├── class-sonotes-cpt.php         # Custom post type registration
│   ├── class-sonotes-metabox.php     # Template metabox functionality
│   └── class-sonotes-order-ui.php    # Order page integration
├── assets/
│   ├── css/
│   │   └── admin.css              # Admin styles
│   └── js/
│       └── admin.js               # Admin JavaScript
└── README.md                      # Documentation
```

## Hooks & Filters

### Actions
- `sonotes_template_inserted` - Fired when a template is inserted into an order note
- `sonotes_template_created` - Fired when a new template is created

### Filters
- `sonotes_template_content` - Filter template content before insertion
- `sonotes_available_templates` - Filter the list of available templates
- `sonotes_note_types` - Filter available note types

### Example Usage

```php
// Modify template content before insertion
add_filter( 'sonotes_template_content', function( $content, $template_id, $order_id ) {
    // Add order number to template
    $order = wc_get_order( $order_id );
    if ( $order ) {
        $content = str_replace( '{order_number}', $order->get_order_number(), $content );
    }
    return $content;
}, 10, 3 );
```

## Permissions

The plugin respects WooCommerce permissions:

- **manage_woocommerce** - Required to create, edit, and delete templates
- **edit_shop_orders** - Required to use templates on order pages

## Compatibility

- **WooCommerce**: 3.0+ (tested up to 8.5)
- **WordPress**: 5.0+ (tested up to 6.4)
- **PHP**: 7.4+ (tested up to 8.2)
- **HPOS**: Full compatibility with High-Performance Order Storage
- **Multisite**: Compatible
- **Translation Ready**: Fully translatable

## Performance

- **Zero Database Queries** on frontend (admin-only plugin)
- **Minimal JavaScript**: < 5KB of JS loaded only on relevant admin pages
- **Efficient CSS**: < 3KB of CSS loaded only when needed
- **No External Dependencies**: Uses only WordPress/WooCommerce core functions

## Security Features

- **Nonce Verification**: All AJAX requests are secured with nonces
- **Capability Checks**: All functions check user permissions
- **Input Sanitization**: All user inputs are properly sanitized
- **XSS Protection**: All outputs are properly escaped
- **SQL Injection Prevention**: Uses WordPress APIs exclusively

## Troubleshooting

### Templates not appearing in dropdown
- Check that templates are published (not draft)
- Verify you have `manage_woocommerce` capability
- Ensure WooCommerce is active

### Insert button not working
- Check browser console for JavaScript errors
- Verify WooCommerce order note fields are present
- Try refreshing the page

### Permission denied errors
- Ensure your user role has `manage_woocommerce` capability
- Check if you can access other WooCommerce admin pages

## Development

### Building from Source
```bash
# No build process required - plugin is ready to use
```

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Debugging
Enable WordPress debug mode to see additional logging:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

## License

GPL v2 or later - see LICENSE file for details.

## Support

For support, feature requests, and bug reports, please use the plugin's support forum or contact the developer.

## Changelog

### 1.0.0
- Initial release
- Core template management functionality
- WooCommerce order integration
- Responsive admin interface
- Full accessibility support
- HPOS compatibility

---

**Smart Order Notes** - Making WooCommerce order management faster and more efficient! 🚀
