<?php

	$user = new User;
    $user->username = 'admin';
    $user->email = 'admin@site.dev';
    $user->password = 'admin';
    $user->password_confirmation = 'admin';
    $user->confirmation_code = md5(uniqid(mt_rand(), true));

    if(! $user->save()) {
    	Log::info('Unable to create user '.$user->username, (array)$user->errors());
    	echo 'Unable to create user '.$user->username;
    	echo "<pre>";
    	print_r((array)$user->errors());
    	echo "</pre>";

    	$user = User::where('username','=','admin')->first();

    	if(Auth::user()->hasRole('Admin')){
            echo "ES ADMIN!!!";
        }else{
            

            $manageUsers = Permission::where('name','=','manage_users')->first();

            $admin = new Role;
            $admin->name = 'Admin';
            $admin->save();

            $user = User::where('username','=','admin')->first();

            /* role attach alias */
            $user->attachRole( $admin ); // Parameter can be an Role object, array or id.

            $admin->perms()->sync(array($manageUsers->id));
        }

        if(isset($user))
    	{
    		echo "El usuario ".$user->username." tiene los siguiente:<br />";
    		echo "Rol: ".$user->hasRole("Admin")."<br />";
			echo "Permiso: ".$user->can("manage_users")."<br />";
    	}

    } else {
    	$admin = new Role;
		$admin->name = 'Admin';
		$admin->save();

		$user = User::where('username','=','admin')->first();

		/* role attach alias */
		$user->attachRole( $admin ); // Parameter can be an Role object, array or id.

		$manageUsers = new Permission;
		$manageUsers->name = 'manage_users';
		$manageUsers->display_name = 'Manage Users';
		$manageUsers->save();

        $manageRoles = new Permission;
        $manageRoles->name = 'manage_roles';
        $manageRoles->display_name = 'Manage Roles';
        $manageRoles->save();

        $admin->perms()->sync(array($manageUsers->id));
		$admin->perms()->sync(array($manageRoles->id));

		echo "Rol: ".$user->hasRole("Admin")."<br />";
        echo "Permiso: ".$user->can("manage_users")."<br />";
		echo "Permiso: ".$user->can("manage_roles")."<br />";

    	Log::info('Created user "'.$user->username.'" <'.$user->email.'>');
    }