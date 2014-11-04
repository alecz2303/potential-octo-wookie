<?php

Route::get('/', function()
{
	return View::make('hello');
});
//

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
    //POS
    Route::controller('pos','PosController');
});
Route::controller('pos','PosController');
