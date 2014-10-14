<?php

class UserTableSeeder extends Seeder {

  public function run()
  {
    $user = new User;
    $user->username = 'admin';
    $user->email = 'admin@site.dev';
    $user->password = 'admin';
    $user->password_confirmation = 'admin';
    $user->confirmation_code = md5(uniqid(mt_rand(), true));
    $user->confirmed = '1';

    if(! $user->save()) {
      Log::info('Unable to create user '.$user->username, (array)$user->errors());
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
      Log::info('Created user "'.$user->username.'" <'.$user->email.'>');
    }
  }
}