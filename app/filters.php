<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('users/login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


/*
|--------------------------------------------------------------------------
| Role Permissions
|--------------------------------------------------------------------------
|
| Access filters based on roles.
|
*/

// Check for role on all admin routes
Entrust::routeNeedsRole( 'admin*', array('Admin'), Redirect::to('/admin') );

// Check for permissions on admin actions

Entrust::routeNeedsPermission( 'admin/users*', 'manage_users', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'admin/roles*', 'manage_roles', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/appconfig*', 'manage_app_config', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/customers*', 'manage_customers', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/giftcards*', 'manage_gift_cards', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/items*', 'manage_items', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/items_kits*', 'manage_items_kits', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/receivings*', 'manage_receivings', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/reports*', 'manage_reports', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/sales*', 'manage_sales', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/suppliers*', 'manage_suppliers', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/payments*', 'manage_payments', Redirect::to('/noaccess') );
Entrust::routeNeedsPermission( 'pos/store*', 'manage_store', Redirect::to('/noaccess') );