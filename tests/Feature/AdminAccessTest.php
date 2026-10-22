<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_redirects_guests_to_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_is_public_but_does_not_offer_registration(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Sign in to admin')
            ->assertDontSee('Create account');

        $this->get('/register')->assertNotFound();
    }

    public function test_authenticated_non_admin_cannot_access_dashboard(): void
    {
        $user = User::query()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => 'NotUsedHere123!',
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_settings(): void
    {
        $admin = User::query()->create([
            'name' => 'Portfolio Admin',
            'email' => 'admin@example.com',
            'password' => 'A-secure-test-password',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Your workspace');

        $this->actingAs($admin)
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('Make it yours.')
            ->assertSee('Favicon image URL');
    }

    public function test_admin_can_sign_in_with_valid_credentials(): void
    {
        User::query()->create([
            'name' => 'Portfolio Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('A-secure-test-password'),
            'is_admin' => true,
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'A-secure-test-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_admin_settings_are_saved_to_sql_fallback_when_firestore_is_not_configured(): void
    {
        config(['services.firestore.project_id' => null]);
        config(['services.firestore.credentials_base64' => null]);

        $admin = User::query()->create([
            'name' => 'Portfolio Admin',
            'email' => 'admin@example.com',
            'password' => 'A-secure-test-password',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->put('/admin/settings', [
                'site_name' => 'My Bookkeeping Studio',
                'site_title' => 'My Bookkeeping Studio | Bookkeeping Support',
                'brand_name' => 'Bookkeeping Studio',
                'favicon_url' => '',
                'hero_image_url' => '',
                'hero_image_alt' => '',
                'eyebrow' => 'BOOKKEEPING · RECONCILIATION · REPORTING',
                'headline' => "Clear books.\nBetter decisions.",
                'description' => 'Reliable bookkeeping support for small businesses.',
                'about_headline' => 'Careful with your records.',
                'about_text' => 'An organized approach to financial records.',
                'contact_headline' => 'Let’s talk about your books.',
                'contact_text' => 'Share what kind of support you need.',
                'contact_email' => '',
                'availability' => 'Taking enquiries',
                'work_intro' => 'Illustrative bookkeeping workflows.',
                'footer_note' => 'Thoughtful bookkeeping support.',
                'meta_description' => 'Bookkeeping, reconciliation and reporting support.',
                'custom_colors' => [
                    'primary' => '#14283F',
                    'background' => '#F5F1E8',
                    'accent' => '#B99A61',
                    'surface' => '#FFFDF8',
                    'text' => '#192C41',
                    'muted' => '#68727B',
                ],
            ])
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHas('success');

        $encoded = DB::table('site_settings_fallback')->where('id', 1)->value('settings');
        $saved = json_decode((string) $encoded, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('My Bookkeeping Studio', $saved['site_name']);
        $this->assertSame('#14283F', $saved['custom_colors']['primary']);

        $this->get('/')
            ->assertOk()
            ->assertSee('My Bookkeeping Studio | Bookkeeping Support');
    }
    public function test_admin_can_upload_a_project_image(): void
    {
        config(['services.firestore.project_id' => null, 'services.firestore.credentials_base64' => null, 'app.url' => 'https://portfolio.test']);
        URL::forceRootUrl('https://portfolio.test');
        URL::forceScheme('https');
        Storage::fake('public');

        $admin = User::query()->create([
            'name' => 'Portfolio Admin',
            'email' => 'admin@example.com',
            'password' => 'A-secure-test-password',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/projects', [
                'title' => 'Sample project',
                'category' => 'Bookkeeping',
                'summary' => 'A sample work item.',
                'image_file' => UploadedFile::fake()->image('sample-project.jpg'),
            ])
            ->assertRedirect(route('admin.projects'))
            ->assertSessionHas('success');

        $encoded = DB::table('site_settings_fallback')->where('id', 1)->value('settings');
        $saved = json_decode((string) $encoded, true, 512, JSON_THROW_ON_ERROR);
        $imageUrl = $saved['projects'][0]['image_url'] ?? '';

        $this->assertStringStartsWith('https://portfolio.test/storage/projects/', $imageUrl);
        $this->assertNotEmpty(Storage::disk('public')->allFiles('projects'));
        $imagePath = parse_url($imageUrl, PHP_URL_PATH);
        $this->get($imagePath)->assertOk();
    }

    public function test_admin_can_be_provisioned_from_environment_values(): void
    {
        config([
            'admin.email' => 'env-admin@example.com',
            'admin.name' => 'Environment Administrator',
            'admin.password' => 'A-strong-test-password-123!',
        ]);

        $this->artisan('bookkeeper:make-admin')
            ->expectsOutput('Administrator created successfully.')
            ->assertExitCode(0);

        $user = User::query()->where('email', 'env-admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('A-strong-test-password-123!', $user->password));
    }

    public function test_admin_provisioning_uses_configured_password_without_extra_length_policy(): void
    {
        config([
            'admin.email' => 'configured-admin@example.com',
            'admin.name' => 'Environment Administrator',
            'admin.password' => 'TeaCup!42',
        ]);

        $this->artisan('bookkeeper:make-admin')
            ->expectsOutput('Administrator created successfully.')
            ->assertExitCode(0);

        $user = User::query()->where('email', 'configured-admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('TeaCup!42', $user->password));
    }

}
