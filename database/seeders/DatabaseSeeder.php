<?php

namespace Database\Seeders;

use App\Enums\SiteStatus;
use App\Enums\SiteType;
use App\Enums\UserRole;
use App\Models\Credential;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        $staff = User::create([
            'name' => '社内スタッフ',
            'email' => 'staff@example.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Staff,
        ]);

        $externalA = User::create([
            'name' => '外部エンジニアA',
            'email' => 'external-a@example.test',
            'password' => Hash::make('password'),
            'role' => UserRole::ExternalEngineer,
        ]);

        $externalB = User::create([
            'name' => '外部エンジニアB',
            'email' => 'external-b@example.test',
            'password' => Hash::make('password'),
            'role' => UserRole::ExternalEngineer,
        ]);

        $wpSite = Site::create([
            'company_name' => 'サンプル株式会社',
            'name' => 'コーポレートサイト',
            'url' => 'https://corp.example.test/',
            'admin_url' => 'https://corp.example.test/wp-admin/',
            'type' => SiteType::Wordpress,
            'status' => SiteStatus::UpdatesAvailable,
            'engineer_id' => $externalA->id,
            'last_maintained_on' => '2026-09-01',
            'next_check_on' => '2026-09-15',
            'created_by' => $admin->id,
        ]);

        $wpInfo = $wpSite->wordpressInformation()->create([
            'wordpress_version' => '6.6.2',
            'php_version' => '8.2',
            'theme_name' => 'Sample Theme',
            'theme_version' => '1.4.0',
            'uses_child_theme' => true,
            'core_update_available' => true,
        ]);
        $wpInfo->plugins()->create([
            'name' => 'Contact Form Sample',
            'current_version' => '5.9.0',
            'latest_version' => '6.0.0',
            'update_status' => 'update_available',
            'last_checked_on' => '2026-09-01',
        ]);

        $staticSite = Site::create([
            'company_name' => '架空商事',
            'name' => 'サービスLP',
            'url' => 'https://lp.example.test/',
            'type' => SiteType::LandingPage,
            'status' => SiteStatus::Normal,
            'engineer_id' => $externalB->id,
            'last_maintained_on' => '2026-08-28',
            'created_by' => $admin->id,
        ]);

        $phpSite = Site::create([
            'company_name' => 'テスト制作所',
            'name' => 'PHP会員サイト',
            'url' => 'https://member.example.test/',
            'type' => SiteType::Php,
            'status' => SiteStatus::NeedsCheck,
            'last_maintained_on' => '2026-08-20',
            'created_by' => $admin->id,
        ]);

        foreach ([$wpSite, $staticSite, $phpSite] as $site) {
            $site->serverInformation()->create([
                'provider' => 'Example Hosting',
                'plan' => 'Business',
                'php_version' => $site->type === SiteType::Php ? '8.3' : '8.2',
                'ssl_enabled' => true,
                'ssl_expires_on' => '2026-12-31',
                'domain_registrar' => 'Example Registrar',
                'domain_expires_on' => '2027-03-31',
                'backup_enabled' => true,
                'backup_frequency' => 'daily',
            ]);

            $site->maintenanceLogs()->create([
                'user_id' => $staff->id,
                'worked_on' => $site->last_maintained_on,
                'category' => '定期保守',
                'description' => '架空データによる定期保守記録です。',
                'minutes_spent' => 20,
            ]);

            Credential::create([
                'site_id' => $site->id,
                'type' => 'wordpress',
                'service_name' => 'ダミー管理画面',
                'login_url' => $site->admin_url,
                'login_id' => 'dummy-user',
                'password' => 'dummy-password-do-not-use',
                'notes' => 'Seeder用の架空認証情報です。',
                'created_by' => $admin->id,
            ]);
        }

        $wpSite->users()->attach($staff->id, ['can_view_site' => true, 'can_view_server' => true, 'can_view_maintenance' => true, 'can_add_maintenance_log' => true]);
        $wpSite->users()->attach($externalA->id, ['can_view_site' => true, 'can_view_server' => true, 'can_view_maintenance' => true, 'can_add_maintenance_log' => true]);
        $staticSite->users()->attach($externalB->id, ['can_view_site' => true, 'can_view_server' => false, 'can_view_maintenance' => true, 'can_add_maintenance_log' => true]);
    }
}
