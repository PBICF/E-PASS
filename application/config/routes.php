<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes with
| underscores in the controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

// AJAX / API
$route['api/employees/inquire']     = 'Ajax_Controller/inquire';
$route['api/employee/family']       = 'Ajax_Controller/get_family';
$route['api/family/update']         = 'Ajax_Controller/update_family';
$route['api/routes']                = 'Ajax_Controller/routes';
$route['api/pass/(:num)']           = 'Ajax_Controller/get_pass/$1';
$route['api/pass/details']          = 'Ajax_Controller/get_pass_details';

// Pass creation
$route['pass/create']               = 'Pass_Controller/create';
$route['pass/submit']               = 'Pass_Controller/submit';  

// Pass Print
$route['pass/reprint']              = 'Home_Controller/print';
$route['pass/cancel']               = 'Home_Controller/cancel';
$route['pass/cancel/submit']        = 'Home_Controller/cancel_pass';
$route['print/pass']                = 'Home_Controller/print_pass';
$route['print/pass/(:num)']         = 'Home_Controller/print_pass/$1';
$route['pass/(:num)/pdf']           = 'Home_Controller/render_pass/$1';

$route['pass/account/update']       = 'Update_Controller/pass_account';
$route['pass/employee/update']      = 'Update_Controller/employee_update';
$route['pass/family/update']        = 'Update_Controller/family_update';

$route['default_controller']        = 'Pass_Controller';
$route['404_override']              = 'errors/page_missing';
$route['translate_uri_dashes']      = FALSE;
