# PyroCMS Store Module

PyroStore is a e-commerce module for PyroCMS.It was developed keeping in mind good practice, usability, and performence. Works fine either on main domain, or on subfolder. Good integration with core modules allows you to adjust views to your own needs.

## PyroStore features ##

Among all features, the core idea was performance and simplicity. It **does not** use Streams (which generates a lot of MySQL queries) and most of all it uses pyrocache for product and categories listings. Therefore unnecessary queries are not requested multiple times. Additionally PyroStore supports:

* Customer Area (Address Book, Wishlist, Orders)
* Product attributes and options
* SEO and sitemap support
* Discount Coupons
* Product attributes and dropdown options
* Secure SSL Checkout and payment proccess
* Special sales, reduces, and featured products
* Unlimited Flat Rate Shipping Methods
* Simple Widget-like Payment Gateways development
* Widgets, Plugins for easier theme customization
* Email Notifications
* PHP 5.3 ready development

Above all that, PyroStore cares about average users, designer and frontend developers:

* No need to adjust PyroCMS routes to make it work,
* no need to override module CSS styles
* No dependency except of course PyroCMS itself.

## PyroStore Extensions ##

Under link below, there will be available free extensions, gateways and snippets for PyroStore, you can use in your development process. For now there is example gateway and htaccess snippet for SSL available.

https://github.com/sein69/pyrostore-extensions/downloads