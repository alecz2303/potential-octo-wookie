<?php

class UserTableSeeder extends Seeder {

  public function run()
  {
    DB::statement("SET FOREIGN_KEY_CHECKS=0");

    DB::table('permission_role')->delete();
    DB::table('assigned_roles')->delete();
    DB::table('permissions')->delete();
    DB::table('roles')->delete();
    DB::table('users')->delete();

    DB::table('permission_role')->truncate();
    DB::table('assigned_roles')->truncate();
    DB::table('permissions')->truncate();
    DB::table('roles')->truncate();
    DB::table('users')->truncate();

    $user = new User;
    $user->username = 'admin';
    $user->email = 'kerberos.it.s@gmail.com';
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

    ######################################################################################################

    $user1 = new User;
    $user1->username = 'ventas';
    $user1->email = 'kerberos.it.s@gmail.com';
    $user1->password = 'ventas';
    $user1->password_confirmation = 'ventas';
    $user1->confirmation_code = md5(uniqid(mt_rand(), true));
    $user1->confirmed = '1';
    $user1->save();

    $ventas = new Role;
    $ventas->name = 'Ventas';
    $ventas->save();

    $user1 = User::where('username','=','ventas')->first();

    /* role attach alias */
    $user1->attachRole( $ventas ); // Parameter can be an Role object, array or id.

#############################################################################################################

    $manageUsers = new Permission;
    $manageUsers->name = 'manage_users';
    $manageUsers->display_name = 'Manage Users';
    $manageUsers->save();

    $manageRoles = new Permission;
    $manageRoles->name = 'manage_roles';
    $manageRoles->display_name = 'Manage Roles';
    $manageRoles->save();

    $manageConfig = new Permission;
    $manageConfig->name = 'manage_app_config';
    $manageConfig->display_name = 'Manage Config';
    $manageConfig->save();

    $manageSales = new Permission;
    $manageSales->name = 'manage_sales';
    $manageSales->display_name = 'Manage Sales';
    $manageSales->save();

    $manageCustomers = new Permission;
    $manageCustomers->name = 'manage_customers';
    $manageCustomers->display_name = 'Manage Customers';
    $manageCustomers->save();

    $manageGiftCards = new Permission;
    $manageGiftCards->name = 'manage_gift_cards';
    $manageGiftCards->display_name = 'Manage Gift Cards';
    $manageGiftCards->save();

    $manageItems = new Permission;
    $manageItems->name = 'manage_items';
    $manageItems->display_name = 'Manage Items';
    $manageItems->save();

    $manageItemsKits = new Permission;
    $manageItemsKits->name = 'manage_items_kits';
    $manageItemsKits->display_name = 'Manage Items Kits';
    $manageItemsKits->save();

    $manageReceivings = new Permission;
    $manageReceivings->name = 'manage_receivings';
    $manageReceivings->display_name = 'Manage Receivings';
    $manageReceivings->save();

    $manageReports = new Permission;
    $manageReports->name = 'manage_reports';
    $manageReports->display_name = 'Manage Reports';
    $manageReports->save();

    $manageSuppliers = new Permission;
    $manageSuppliers->name = 'manage_suppliers';
    $manageSuppliers->display_name = 'Manage Suppliers';
    $manageSuppliers->save();

    $admin->perms()->sync(array(
                                $manageUsers->id,
                                $manageRoles->id,
                                $manageConfig->id,
                                $manageSales->id,
                                $manageCustomers->id,
                                $manageGiftCards->id,
                                $manageItems->id,
                                $manageItemsKits->id,
                                $manageReceivings->id,
                                $manageReports->id,
                                $manageSuppliers->id
                                ));

    $ventas->perms()->sync(array(
                                $manageSales->id,
                                $manageCustomers->id,
                                $manageGiftCards->id,
                                $manageItems->id,
                                $manageItemsKits->id
                                ));

      Log::info('Created user "'.$user->username.'" <'.$user->email.'>');
    }
  }
}
