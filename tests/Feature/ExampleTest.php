<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/menu-widget');

        $response->assertStatus(200);
    }

    public function test_root_shows_admin_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Menu Widget Login');
    }

    public function test_menu_widget_admin_requires_login(): void
    {
        $this->get('/menu-widget-admin')
            ->assertRedirect('/');
    }

    public function test_menu_widget_admin_login_allows_access(): void
    {
        $this->post('/menu-widget-admin/login', [
            'username' => config('toast_menu.admin.username'),
            'password' => config('toast_menu.admin.password'),
        ])->assertRedirect('/menu-widget-admin');

        $this->get('/menu-widget-admin')
            ->assertOk();
    }
}
