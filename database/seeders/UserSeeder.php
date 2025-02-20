<?php

namespace Database\Seeders;

use App\Models\OrganizationSettings;
use App\Models\Permission;
use App\Models\Privilege;
use App\Models\UserOrganization;
use App\Models\UserPrivilege;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //superadmin
        $user1 = new User();
        $user1->name = 'Admin Admin';
        $user1->email = 'admin@chatiebase.com';
        $user1->password = Hash::make('password');
        $user1->is_admin = 1;
        $user1->status = 'active';
        $user1->save();

        $organizationSettings = new OrganizationSettings();
        $organizationSettings->name = "XZY";
        $organizationSettings->website = "https://zxy.com";
        $organizationSettings->timezone = "Europe/London";
        $organizationSettings->country = "United Kingdom";
        $organizationSettings->save();

        $userOrganization = new UserOrganization();
        $userOrganization->user_id = $user1->id;
        $userOrganization->organization_id = $organizationSettings->id;
        $userOrganization->save();

        $this->setDefaultpermission($userOrganization);

        $userPrivilege = new UserPrivilege();
        $userPrivilege->user_id = $user1->id;
        $userPrivilege->privilege_id = "1";
        $userPrivilege->save();

        unset($user);

        $user = new User();
        $user->name = 'Neon Emmanuel';
        $user->email = 'neon@gnail.com';
        $user->password = Hash::make('password');
        $user->was_invited = "1";
        $user->was_invited_by = $user1->id;
        $user->is_admin = 1;
        $user->status = 'active';
        $user->save();

        $userOrganization = new UserOrganization();
        $userOrganization->user_id = $user->id;
        $userOrganization->organization_id = $organizationSettings->id;
        $userOrganization->save();

        $userPrivilege = new UserPrivilege();
        $userPrivilege->user_id = $user->id;
        $userPrivilege->privilege_id = "1";
        $userPrivilege->save();

        unset($user);


        $user = new User();
        $user->name = 'Oziri Emeka Emmanuel';
        $user->email = 'oziriemeka@gmail.com';
        $user->password = Hash::make('123456');
        $user->is_admin = 1;
        $user->was_invited = "1";
        $user->was_invited_by = $user1->id;
        $user->status = 'active';
        $user->save();

        $userOrganization = new UserOrganization();
        $userOrganization->user_id = $user->id;
        $userOrganization->organization_id = $organizationSettings->id;
        $userOrganization->save();

        $userPrivilege = new UserPrivilege();
        $userPrivilege->user_id = $user->id;
        $userPrivilege->privilege_id = "2";
        $userPrivilege->save();

        unset($user);

        for($i = 0; $i < 15; $i++){
            $faker = Factory::create();
            $user = new User();
            $user->name = $faker->name();
            $user->email = $faker->email;
            $user->password = Hash::make('password');
            $user->was_invited = "1";
            $user->was_invited_by = $user1->id;
            $user->save();

            $userOrganization = new UserOrganization();
            $userOrganization->user_id = $user->id;
            $userOrganization->organization_id = $organizationSettings->id;
            $userOrganization->save();

            $userPrivilege = new UserPrivilege();
            $userPrivilege->user_id = $user->id;
            $userPrivilege->privilege_id = "2";
            $userPrivilege->save();
            unset($user);
        }
    }

    public function setDefaultpermission(UserOrganization $org)
    {
        $permissions = Permission::all()->pluck('id');
        $name = "Administrator";
        $privilege = new Privilege();
        $privilege->name = $name;
        $privilege->can_delete = 0;
        $privilege->organization_id = $org->id;
        $privilege->save();
        $privilege->permissions()->sync($permissions); //Assign all privilege to Administrator

        unset($permissions);
        unset($name);
        unset($privilege);

        $permissions = Permission::where("group", "chat")->get()->pluck('id');

        $name = "Customer care representative";
        $privilege = new Privilege();
        $privilege->name = $name;
        $privilege->can_delete = 0;
        $privilege->organization_id = $org->id;
        $privilege->save();
        $privilege->permissions()->sync($permissions); //Assign all privilege to Administrator
    }
}
