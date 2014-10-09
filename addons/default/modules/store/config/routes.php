<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	www.your-site.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://www.codeigniter.com/user_guide/general/routing.html
*/
// admin
$route['store/admin/ajax(:any)?']		= 'admin/ajax$1';
$route['store/admin/data(:any)?']		= 'admin/data$1';
$route['store/admin/shipments(:any)?']		= 'admin/shipments$1';
$route['store/admin/gateways(:any)?']		= 'admin/gateways$1';
$route['store/admin/coupons(:any)?']		= 'admin/coupons$1';
$route['store/admin/affiliate(:any)?']		= 'admin/affiliate$1';
$route['store/admin/sales(:any)?']		= 'admin/sales$1';
$route['store/admin/categories(:any)?']		= 'admin/categories$1';
$route['store/admin/products/fields(:any)?']	= 'admin/custom_fields$1';
$route['store/admin/products(:any)?']		= 'admin/products$1';
$route['store/admin/options(:any)?']		= 'admin/options$1';
$route['store/admin/stats(:any)?']              = 'admin/stats$1';
$route['store/admin(:any)?']                    = 'admin/store$1';
$route['store/uploader(/:any)?']		= 'admin/uploader$1';
// front-end
$route['store(/:num)?']                         = 'store/index$1';
//$route['store(:any)'] 		= 'store$1';