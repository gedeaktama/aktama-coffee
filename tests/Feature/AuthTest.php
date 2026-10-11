<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@kopi.com',
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser@kopi.com',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@kopi.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'email' => 'testuser@kopi.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@kopi.com',
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('secret123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@kopi.com',
            'password' => 'secret123',
        ]);

        $this->assertGuest();
    }

    public function test_public_registration_strictly_assigns_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Retailer',
            'email' => 'budi@testretail.com',
            'phone' => '081299998888',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@testretail.com',
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
