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

$route['admin/login'] = "admin/login";
$route['admin/images'] = "image_admin";
$route['admin/image_upload'] = "image_admin/upload";
$route['admin/image/(:num)'] = "image_admin/edit/$1";
$route['admin/artists'] = "artist_admin";
$route['admin/artist/(:any)/images/(:any)'] = "artist_admin/images/$1/$2";
$route['admin/artist/(:any)/images'] = "artist_admin/images/$1";
$route['admin/artist/(:any)/delete'] = "artist_admin/delete/$1";
$route['admin/artist/(:any)'] = "artist_admin/edit/$1";
$route['admin/exhibitions'] = "exhibition_admin";
$route['admin/exhibitions/(\d{4})'] = "exhibition_admin/index/$1";
$route['admin/exhibition/create'] = "exhibition_admin/add";
$route['admin/exhibition/(:any)/delete'] = "exhibition_admin/delete/$1";
$route['admin/exhibition/(:any)/images'] = "exhibition_admin/images/$1";
$route['admin/exhibition/(:any)'] = "exhibition_admin/edit/$1";
$route['api/artists'] = "api/artists";
$route['api/artist/(:any)/images/(:any)'] = "api/artist_gallery_images/$1/$2";
$route['api/artist/(:any)/images'] = "api/artist_images/$1";
$route['api/exhibition/(:any)/images'] = "api/exhibition_images/$1";
$route['api/images'] = "api/images";
$route['api/galleries'] = "api/galleries";
$route['admin'] = "admin";
$route['(:any)/contact'] = "gallery/index/$1";
$route['(:any)/exhibitions'] = "exhibition/index/$1";
$route['(:any)/past_exhibitions'] = "exhibition/past/$1";
$route['(:any)/exhibition/(:any)'] = "exhibition/view/$1";
$route['(:any)/artists'] = "artist/index/$1";
$route['(:any)/artist/(:any)'] = "artist/view/$1";
$route['(:any)/news'] = "news/index/$1";
$route['(:any)/about'] = "about/index/$1";
$route['default_controller'] = "welcome";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */