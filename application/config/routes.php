<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|   $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|       my-controller/my-method -> my_controller/my_method
*/

$route['default_controller']   = 'clients';
$route['404_override']         = '';
$route['translate_uri_dashes'] = false;

// restaurant api
$route['api/registration'] = 'api/Registration/onboarding';
$route['api/login'] = 'api/MarieLogin/login';
$route['api/email_validation'] = 'api/Registration/email_validation';
$route['api/verification_email'] = 'api/Dashboard/email_verification';
$route['api/verify'] = 'api/Dashboard/email_verify';
$route['api/forgotPassword'] = 'api/MarieLogin/forgot_password';
$route['api/passwordCreation'] = 'api/MarieLogin/password_creation';
$route['api/findings'] = 'api/Dashboard/findings';
$route['api/common'] = 'api/Registration/commonForAll';

$route['api/init'] = 'api/Sales/init';
$route['api/saleschannel'] = 'api/Sales/sales_channel';
$route['api/salesmenu'] = 'api/Sales/sales_menu';
$route['api/salesmenu_insert'] = 'api/Sales/sales_menu_insert';
$route['api/salesmenu_upload'] = 'api/Sales/sales_menu_upload';
$route['api/salesmenu_edit_delete'] = 'api/Sales/sales_menu_edit_delete';
$route['api/salesmenu_row'] = 'api/Sales/sales_menu_row';
$route['api/sales_month'] = 'api/Sales/get_sales_month';
$route['api/request'] = 'api/Sales/request';
$route['api/deleteMultiple'] = 'api/Sales/delete_multiple';

# Ingredients
$route['api/ingredients'] = 'api/Ingredients/ingredients';
$route['api/ingredientsList'] = 'api/Ingredients/ingredients_list';
$route['api/storeIngredients'] = 'api/Ingredients/storeIngredients';
$route['api/createIngredient'] = 'api/Ingredients/addIngredients';
$route['api/editIngredient'] = 'api/Ingredients/editIngredients';
$route['api/stocks/selectingStock'] = 'api/Ingredients/selectingStock';
$route['api/stocks/create'] = 'api/Ingredients/createStock';
$route['api/stocks/edit'] = 'api/Ingredients/editStock';
$route['api/stocks/delete'] = 'api/Ingredients/deleteStock';
$route['api/stocks/list'] = 'api/Ingredients/stockLists';
$route['api/stocks/download'] = 'api/Ingredients/download';
$route['api/stocks/downloadApp'] = 'api/Ingredients/downloadMobile';
$route['api/stocks/dragDrop'] = 'api/Ingredients/dragDrop';
$route['api/selectingIngredients'] = 'api/Ingredients/selectingIngredients';
// $route['api/checks'] = 'api/Ingredients/check';

$route['api/storeSetup'] = 'api/Labour/store_setup';
$route['api/labour_activity'] = 'api/Labour/labour_activity';
$route['api/insertLabour'] = 'api/Labour/save_labour';
$route['api/fetchLabour'] = 'api/Labour/get_labour';
$route['api/labourList'] = 'api/Labour/labour_list';
$route['api/fetchTraceable'] = 'api/Labour/fetch_traceable';
$route['api/fetchSetup'] = 'api/Labour/fetch_setup';
$route['api/deleteLabour'] = 'api/Labour/delete_labour';
$route['api/editLabour'] = 'api/Labour/edit_labour';
// $route['api/insertDays'] = 'api/Labour/store_working_days';
// $route['api/fetchDays'] = 'api/Labour/fetch_working_days';
$route['api/overtimeSave'] = 'api/Labour/overtime';
$route['api/leaveSave'] = 'api/Labour/leave';
$route['api/attendance'] = 'api/Labour/attendance';
$route['api/homeScreen'] = 'api/Labour/homeScreen';
$route['api/restDays'] = 'api/Labour/restDays';
$route['api/check'] = 'api/Labour/leave_check';
$route['api/traceable'] = 'api/Labour/traceable';

$route['api/overheads/init'] = 'api/Overheads/initialization';
$route['api/overheads/store'] = 'api/Overheads/storeUserData';
$route['api/overheads/fetch'] = 'api/Overheads/fetchUserData';

$route['api/costing/init'] = 'api/costing/initialization';
$route['api/costing/overheads'] = 'api/costing/overheads';
$route['api/costing/indirectLabour'] = 'api/costing/indirect_labour';
$route['api/costing/directLabour'] = 'api/costing/direct_labour';
$route['api/costing/sides'] = 'api/costing/sides';
$route['api/costing/fetchSide'] = 'api/costing/fetch_sides';
$route['api/costing/saveCosting'] = 'api/costing/save_costing';
$route['api/costing/fetchCosting'] = 'api/costing/fetch_costing';
$route['api/costing/deleteCosting'] = 'api/costing/delete_costing';
$route['api/costing/performance'] = 'api/costing/performance';
$route['api/costing/homeScreen'] = 'api/costing/homeScreen';
$route['api/costing/copy'] = 'api/costing/copy_costing';
$route['api/costing/sidesDelete'] = 'api/costing/sides_delete';

$route['api/processBuilder/init'] = 'api/ProcessBuilder/initialization';
$route['api/processBuilder/save'] = 'api/ProcessBuilder/saving';
$route['api/processBuilder/fetch'] = 'api/ProcessBuilder/fetch';
$route['api/processBuilder/change'] = 'api/ProcessBuilder/change';

/**
 * $route['googleMap'] = 'api/Google_controller/googlemap_function';
 * https://google.com/api/googleMap/
 * Dashboard clean route
 */
$route['admin'] = 'admin/restaurant';

/**
 * Misc controller routes
 */
$route['admin/access_denied'] = 'admin/misc/access_denied';
$route['admin/not_found']     = 'admin/misc/not_found';

/**
 * Staff Routes
 */
$route['admin/profile']           = 'admin/staff/profile';
$route['admin/profile/(:num)']    = 'admin/staff/profile/$1';
$route['admin/tasks/view/(:any)'] = 'admin/tasks/index/$1';

/**
 * Items search rewrite
 */
$route['admin/items/search'] = 'admin/invoice_items/search';

/**
 * In case if client access directly to url without the arguments redirect to clients url
 */
$route['/'] = 'clients';

/**
 * @deprecated
 */
$route['viewinvoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['invoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @deprecated
 */
$route['viewestimate/(:num)/(:any)'] = 'estimate/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['estimate/(:num)/(:any)'] = 'estimate/index/$1/$2';
$route['subscription/(:any)']    = 'subscription/index/$1';

/**
 * @deprecated
 */
$route['viewproposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['proposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['contract/(:num)/(:any)'] = 'contract/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['knowledge-base']                 = 'knowledge_base/index';
$route['knowledge-base/search']          = 'knowledge_base/search';
$route['knowledge-base/article']         = 'knowledge_base/index';
$route['knowledge-base/article/(:any)']  = 'knowledge_base/article/$1';
$route['knowledge-base/category']        = 'knowledge_base/index';
$route['knowledge-base/category/(:any)'] = 'knowledge_base/category/$1';

/**
 * @deprecated 2.2.0
 */
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'add_kb_answer') === false) {
    $route['knowledge-base/(:any)']         = 'knowledge_base/article/$1';
    $route['knowledge_base/(:any)']         = 'knowledge_base/article/$1';
    $route['clients/knowledge_base/(:any)'] = 'knowledge_base/article/$1';
    $route['clients/knowledge-base/(:any)'] = 'knowledge_base/article/$1';
}

/**
 * @deprecated 2.2.0
 * Fallback for auth clients area, changed in version 2.2.0
 */
$route['clients/reset_password']  = 'authentication/reset_password';
$route['clients/forgot_password'] = 'authentication/forgot_password';
$route['clients/logout']          = 'authentication/logout';
$route['clients/register']        = 'authentication/register';
$route['clients/login']           = 'authentication/login';

// Aliases for short routes
$route['reset_password']  = 'authentication/reset_password';
$route['forgot_password'] = 'authentication/forgot_password';
$route['login']           = 'authentication/login';
$route['logout']          = 'authentication/logout';
$route['register']        = 'authentication/register';

/**
 * Terms and conditions and Privacy Policy routes
 */
$route['terms-and-conditions'] = 'terms_and_conditions';
$route['privacy-policy']       = 'privacy_policy';

/**
 * @since 2.3.0
 * Routes for admin/modules URL because Modules.php class is used in application/third_party/MX
 */
$route['admin/modules']               = 'admin/mods';
$route['admin/modules/(:any)']        = 'admin/mods/$1';
$route['admin/modules/(:any)/(:any)'] = 'admin/mods/$1/$2';

// Public single ticket route
$route['forms/tickets/(:any)'] = 'forms/public_ticket/$1';

/**
 * @since  2.3.0
 * Route for clients set password URL, because it's using the same controller for staff to
 * If user addded block /admin by .htaccess this won't work, so we need to rewrite the URL
 * In future if there is implementation for clients set password, this route should be removed
 */
$route['authentication/set_password/(:num)/(:num)/(:any)'] = 'admin/authentication/set_password/$1/$2/$3';

// For backward compatilibilty
$route['survey/(:num)/(:any)'] = 'surveys/participate/index/$1/$2';

if (file_exists(APPPATH . 'config/my_routes.php')) {
    include_once(APPPATH . 'config/my_routes.php');
}
