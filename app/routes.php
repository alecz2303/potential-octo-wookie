<?php
Route::get('/', function()
{
	return View::make('hello');
});
//

Route::get('/noaccess', function()
{
	return View::make('noaccess');
});

/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 */
Route::model('app_config', 'AppConfig');
Route::model('user', 'User');
Route::model('role', 'Role');
Route::model('people', 'Peoples');
Route::model('customers', 'Customers');
Route::model('suppliers', 'Suppliers');
Route::model('inventories', 'Inventories');
Route::model('items', 'Items');
Route::model('items_kits', 'ItemsKits');
Route::model('giftcards', 'Giftcards');
Route::model('receivings', 'Receivings');
Route::model('sales', 'Sales');
Route::model('app_config', 'AppConfig');
Route::model('store_orders', 'StoreOrders');
Route::model('store_orders_items', 'StoreOrdersItems');
Route::model('store_orders_items_taxes', 'StoreOrdersItemsTaxes');

/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------
 */
Route::pattern('user', '[0-9]+');
Route::pattern('role', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');



//First Run route
Route::get('first_run',array('as'=>'inicio','uses'=>'HomeController@getFirstRun'));
Route::get('user',['as'=>'configuracion_usuario', 'uses'=>'HomeController@getConfiguracionUsuario']);

// Confide routes
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');

//Admin routes
Route::group(array('prefix'=>'admin'), function(){
	//users management
	Route::get('users/{user}/show', 'AdminUsersController@getShow');
    Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
    Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
    Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
    Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
	Route::controller('users', 'AdminUsersController');

	# User Role Management
    Route::get('roles/{role}/show', 'AdminRolesController@getShow');
    Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
    Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
    Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
    Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
    Route::get('roles/', 'AdminRolesController@getIndex');
    Route::controller('roles', 'AdminRolesController');

    # Admin Dashboard
    Route::controller('/', 'AdminDashboardController');
});

//POS Routes
Route::group(array('prefix'=>'pos'), function(){
    //Customers
    Route::get('customers/{people}/edit', 'CustomersController@getEdit');
    Route::post('customers/{people}/edit', 'CustomersController@postEdit');
    Route::get('customers/{people}/delete', 'CustomersController@getDelete');
    Route::post('customers/{people}/delete', 'CustomersController@postDelete');
    Route::controller('customers', 'CustomersController');
    //Suppliers
    Route::get('suppliers/{people}/edit', 'SuppliersController@getEdit');
    Route::post('suppliers/{people}/edit', 'SuppliersController@postEdit');
    Route::get('suppliers/{people}/delete', 'SuppliersController@getDelete');
    Route::post('suppliers/{people}/delete', 'SuppliersController@postDelete');
    Route::controller('suppliers', 'SuppliersController');
    //Items
    Route::get('items/{items}/edit', 'ItemsController@getEdit');
    Route::post('items/{items}/edit', 'ItemsController@postEdit');
    Route::get('items/{items}/detail', 'ItemsController@getDetail');
    Route::get('items/{items}/inventory', 'ItemsController@getInventory');
    Route::post('items/{items}/inventory', 'ItemsController@postInventory');
    Route::get('items/{items}/delete', 'ItemsController@getDelete');
    Route::post('items/{items}/delete', 'ItemsController@postDelete');
    Route::controller('items', 'ItemsController');
    //Items_Kits
    Route::get('items_kits/{items_kits}/edit', 'ItemsKitsController@getEdit');
    Route::post('items_kits/{items_kits}/edit', 'ItemsKitsController@postEdit');
    Route::get('items_kits/{items_kits}/delete', 'ItemsKitsController@getDelete');
    Route::post('items_kits/{items_kits}/delete', 'ItemsKitsController@postDelete');
    Route::controller('items_kits', 'ItemsKitsController');
    //Giftcards
	Route::get('giftcards/{giftcards}/edit','GiftcardsController@getEdit');
	Route::post('giftcards/{giftcards}/edit','GiftcardsController@postEdit');
	Route::get('giftcards/{giftcards}/delete','GiftcardsController@getDelete');
	Route::post('giftcards/{giftcards}/delete','GiftcardsController@postDelete');
    Route::controller('giftcards', 'GiftcardsController');
    //Receivings
	Route::get('receivings/{receivings}/receipt', 'ReceivingsController@getReceipt');
    Route::controller('receivings', 'ReceivingsController');
    //Sales
	Route::get('sales/{sales}/receipt', 'SalesController@getReceipt');
    Route::controller('sales', 'SalesController');
	//AppConfig
	Route::controller('appconfig', 'AppConfigController');
	//Reports
	Route::get('reports/low_inventory/low', 'ReportsController@getLow');
	Route::get('reports/inventory/inventory', 'ReportsController@getInventory');
	Route::get('reports/summary_sales', 'ReportsController@getSummarysales');
	Route::post('reports/summary_sales', 'ReportsController@postSummarysales');
	Route::get('reports/summary_categories', 'ReportsController@getSummarycategories');
	Route::post('reports/summary_categories', 'ReportsController@postSummarycategories');
	Route::get('reports/summary_customers', 'ReportsController@getSummarycustomers');
	Route::post('reports/summary_customers', 'ReportsController@postSummarycustomers');
	Route::get('reports/summary_suppliers', 'ReportsController@getSummarysuppliers');
	Route::post('reports/summary_suppliers', 'ReportsController@postSummarysuppliers');
	Route::get('reports/summary_items', 'ReportsController@getSummaryitems');
	Route::post('reports/summary_items', 'ReportsController@postSummaryitems');
	Route::get('reports/summary_users', 'ReportsController@getSummaryusers');
	Route::post('reports/summary_users', 'ReportsController@postSummaryusers');
	Route::get('reports/summary_taxes', 'ReportsController@getSummarytaxes');
	Route::post('reports/summary_taxes', 'ReportsController@postSummarytaxes');
	Route::get('reports/summary_discounts', 'ReportsController@getSummarydiscounts');
	Route::post('reports/summary_discounts', 'ReportsController@postSummarydiscounts');
	Route::get('reports/summary_payments', 'ReportsController@getSummarypayments');
	Route::post('reports/summary_payments', 'ReportsController@postSummarypayments');
	Route::get('reports/graphic/sales', 'ReportsController@getGraphicsales');
	Route::post('reports/graphic/sales', 'ReportsController@postGraphicsales');
	Route::get('reports/graphic/category', 'ReportsController@getGraphiccategory');
	Route::post('reports/graphic/category', 'ReportsController@postGraphiccategory');
	Route::get('reports/graphic/customer', 'ReportsController@getGraphiccustomer');
	Route::post('reports/graphic/customer', 'ReportsController@postGraphiccustomer');
	Route::get('reports/graphic/supplier', 'ReportsController@getGraphicsupplier');
	Route::post('reports/graphic/supplier', 'ReportsController@postGraphicsupplier');
	Route::get('reports/graphic/item', 'ReportsController@getGraphicitem');
	Route::post('reports/graphic/item', 'ReportsController@postGraphicitem');
	Route::get('reports/graphic/user', 'ReportsController@getGraphicuser');
	Route::post('reports/graphic/user', 'ReportsController@postGraphicuser');
	Route::get('reports/graphic/tax', 'ReportsController@getGraphictax');
	Route::post('reports/graphic/tax', 'ReportsController@postGraphictax');
	Route::get('reports/graphic/discount', 'ReportsController@getGraphicdiscount');
	Route::post('reports/graphic/discount', 'ReportsController@postGraphicdiscount');
	Route::get('reports/graphic/payment', 'ReportsController@getGraphicpayment');
	Route::post('reports/graphic/payment', 'ReportsController@postGraphicpayment');
	Route::get('reports/detail_sales', 'ReportsController@getDetailsales');
	Route::post('reports/detail_sales', 'ReportsController@postDetailsales');
	Route::get('reports/detail_sales/{sales}/edit', 'ReportsController@getEditsale');
	Route::post('reports/detail_sales/{sales}/edit', 'ReportsController@postEditsale');
	Route::get('reports/detail_receivings', 'ReportsController@getDetailreceivings');
	Route::post('reports/detail_receivings', 'ReportsController@postDetailreceivings');
	Route::get('reports/detail_customers', 'ReportsController@getDetailcustomers');
	Route::post('reports/detail_customers', 'ReportsController@postDetailcustomers');
	Route::get('reports/detail_customers/{sales}/edit', 'ReportsController@getEditcustomer');
	Route::post('reports/detail_customers/{sales}/edit', 'ReportsController@postEditcustomer');
	Route::get('reports/detail_users', 'ReportsController@getDetailusers');
	Route::post('reports/detail_users', 'ReportsController@postDetailusers');
	Route::get('reports/detail_users/{sales}/edit', 'ReportsController@getEdituser');
	Route::post('reports/detail_users/{sales}/edit', 'ReportsController@postEdituser');
	Route::controller('reports', 'ReportsController');
	//Payments
	Route::get('payments/{sales}/{dif}/{customer_id}/add', 'PaymentsController@getAdd_payment');
	Route::post('payments/{sales}/{dif}/{customer_id}/add', 'PaymentsController@postAdd_payment');
	Route::controller('payments', 'PaymentsController');
    //STORE
    Route::get('store', 'StoreController@getStore');
    //POS
    Route::controller('pos','PosController');
});
Route::controller('pos','PosController');

Route::group(array('prefix'=>'store'), function(){
	Route::get('/store', 'StoreController@getIndex');
	Route::controller('/', 'StoreController');
});