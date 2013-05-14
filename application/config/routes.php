<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['(:any)/contact'] = "gallery/index/$1";
$route['(:any)/exhibitions'] = "exhibition/index/$1";
$route['(:any)/past_exhibitions'] = "exhibition/past/$1";
$route['(:any)/exhibition/(:any)'] = "exhibition/view/$1";
$route['(:any)/artists'] = "artist/index/$1";
$route['(:any)/artist/(:any)'] = "artist/view/$1";
$route['(:any)/news'] = "news/index/$1";
$route['(:any)/about'] = "about/index/$1";
$route['admin/login'] = "admin/login";
$route['admin/images'] = "image_admin";
$route['admin/image_upload'] = "image_admin/upload";
$route['admin/image/(:num)'] = "image_admin/edit/$1";
$route['api/artists'] = "api/artists";
$route['api/galleries'] = "api/galleries";
$route['admin'] = "admin";
$route['default_controller'] = "welcome";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */