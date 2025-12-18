<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'user-list',
           'user-create',
           'user-edit',
           'user-delete',
           'school-list',
           'school-create',
           'school-edit',
           'school-delete',
           'student-list',
           'student-create',
           'student-edit',
           'student-delete',
           'staff-list',
           'staff-create',
           'staff-edit',
           'staff-delete',
           'template-list',
           'template-create',
           'template-edit',
           'template-delete',
           'settings-edit',
        ];
      
        foreach ($permissions as $permission) {
             Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Create Admin Role and Assign All Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
        
        // Assign admin role to existing admin users
        // Assuming your users table has a 'role' column from previous implementation
        $users = User::where('role', 'admin')->get();
        foreach($users as $user){
            $user->assignRole($adminRole);
        }
    }
}
