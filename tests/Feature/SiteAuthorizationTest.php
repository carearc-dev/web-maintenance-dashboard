<?php

namespace Tests\Feature;

use App\Enums\SiteStatus;
use App\Enums\SiteType;
use App\Enums\UserRole;
use App\Models\Credential;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_engineer_cannot_view_another_engineers_site_by_direct_url(): void
    {
        $engineerA = User::factory()->create(['role' => UserRole::ExternalEngineer]);
        $engineerB = User::factory()->create(['role' => UserRole::ExternalEngineer]);
        $siteA = Site::factory()->create(['type' => SiteType::Wordpress, 'status' => SiteStatus::Normal]);
        $siteB = Site::factory()->create(['type' => SiteType::Wordpress, 'status' => SiteStatus::Normal]);

        $siteA->users()->attach($engineerA->id, ['can_view_site' => true]);
        $siteB->users()->attach($engineerB->id, ['can_view_site' => true]);

        $this->actingAs($engineerA)->get(route('sites.show', $siteB))->assertForbidden();
    }

    public function test_credential_password_is_not_stored_as_plain_text(): void
    {
        $site = Site::factory()->create(['type' => SiteType::Wordpress, 'status' => SiteStatus::Normal]);
        $credential = Credential::create([
            'site_id' => $site->id,
            'type' => 'wordpress',
            'service_name' => 'Test',
            'password' => 'secret-value',
        ]);

        $this->assertNotSame('secret-value', $credential->getRawOriginal('password'));
        $this->assertSame('secret-value', $credential->password);
    }
}
