<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->index();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->index();
            $table->string('name')->index();
            $table->string('url');
            $table->string('admin_url')->nullable();
            $table->string('type')->index();
            $table->string('status')->index();
            $table->date('published_on')->nullable();
            $table->date('maintenance_started_on')->nullable();
            $table->date('maintenance_ended_on')->nullable();
            $table->foreignId('director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('engineer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('github_url')->nullable();
            $table->text('notes')->nullable();
            $table->date('last_maintained_on')->nullable()->index();
            $table->date('next_check_on')->nullable()->index();
            $table->timestamp('last_checked_at')->nullable()->index();
            $table->text('auto_status_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_view_site')->default(true);
            $table->boolean('can_view_server')->default(false);
            $table->boolean('can_view_credentials')->default(false);
            $table->boolean('can_edit_credentials')->default(false);
            $table->boolean('can_view_maintenance')->default(true);
            $table->boolean('can_add_maintenance_log')->default(true);
            $table->timestamps();
            $table->unique(['site_id', 'user_id']);
        });

        Schema::create('server_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('plan')->nullable();
            $table->string('control_panel_url')->nullable();
            $table->string('ftp_host')->nullable();
            $table->string('ssh_host')->nullable();
            $table->string('php_version')->nullable();
            $table->string('database_name')->nullable();
            $table->boolean('ssl_enabled')->default(false);
            $table->date('ssl_expires_on')->nullable()->index();
            $table->string('domain_registrar')->nullable();
            $table->date('domain_expires_on')->nullable()->index();
            $table->string('dns_provider')->nullable();
            $table->string('cdn')->nullable();
            $table->string('waf')->nullable();
            $table->boolean('basic_auth_enabled')->default(false);
            $table->boolean('backup_enabled')->default(false);
            $table->string('backup_frequency')->nullable();
            $table->json('security_headers')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wordpress_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('wordpress_version')->nullable();
            $table->string('php_version')->nullable();
            $table->string('theme_name')->nullable();
            $table->string('theme_version')->nullable();
            $table->boolean('uses_child_theme')->default(false);
            $table->boolean('core_update_available')->default(false);
            $table->unsignedInteger('plugin_update_count')->default(0);
            $table->string('connection_status')->default('unknown')->index();
            $table->string('security_status')->default('unknown')->index();
            $table->text('status_message')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wordpress_plugins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wordpress_information_id')->constrained('wordpress_information')->cascadeOnDelete();
            $table->string('name');
            $table->string('current_version')->nullable();
            $table->string('latest_version')->nullable();
            $table->string('update_status')->default('unknown')->index();
            $table->date('last_checked_on')->nullable();
            $table->timestamps();
        });

        Schema::create('site_technologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('current_version')->nullable();
            $table->string('cdn_url')->nullable();
            $table->date('last_checked_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('category')->index();
            $table->string('label');
            $table->string('status')->index();
            $table->date('checked_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('worked_on')->index();
            $table->string('category')->index();
            $table->text('description');
            $table->unsignedInteger('minutes_spent')->nullable();
            $table->string('version_before')->nullable();
            $table->string('version_after')->nullable();
            $table->string('repository_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('service_name');
            $table->string('login_url')->nullable();
            $table->text('login_id')->nullable();
            $table->text('password')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_at')->index();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->string('result')->index();
            $table->json('security_headers')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('severity')->index();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('status')->default('open')->index();
            $table->timestamp('detected_at')->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('monitoring_logs');
        Schema::dropIfExists('credentials');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('maintenance_items');
        Schema::dropIfExists('site_technologies');
        Schema::dropIfExists('wordpress_plugins');
        Schema::dropIfExists('wordpress_information');
        Schema::dropIfExists('server_information');
        Schema::dropIfExists('site_users');
        Schema::dropIfExists('sites');
        Schema::dropIfExists('users');
    }
};
