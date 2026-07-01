<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * إنشاء مستخدم أدمن بالاسم والباسورد المحدد
     */
    public function run(): void
    {
        $adminRole = Role::findByName('admin', 'web');

        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'admin',
                'email'    => null,
                'password' => Hash::make('8472304'),
            ]
        );

        $admin->syncRoles([$adminRole]);

        $this->command->info('✓ تم إنشاء مستخدم الأدمن:');
        $this->command->table(
            ['الحقل', 'القيمة'],
            [
                ['اسم المستخدم', 'admin'],
                ['كلمة المرور',  '8472304'],
                ['الدور',        'admin'],
            ]
        );
    }
}
