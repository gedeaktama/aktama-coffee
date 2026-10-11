<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_customer_role_is_forbidden_from_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertStatus(403);
    }

    public function test_staff_roles_can_access_dashboard(): void
    {
        $roles = [
            User::ROLE_OWNER,
            User::ROLE_ADMIN,
            User::ROLE_SALES,
            User::ROLE_WAREHOUSE,
            User::ROLE_FINANCE,
        ];

        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
            ]);

            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertSee(strtoupper($role));
        }
    }

    public function test_only_owner_can_access_owner_zone(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        // Admin cannot access owner zone
        $responseAdmin = $this->actingAs($admin)->get('/dashboard/owner-zone');
        $responseAdmin->assertStatus(403);

        // Owner can access owner zone
        $responseOwner = $this->actingAs($owner)->get('/dashboard/owner-zone');
        $responseOwner->assertStatus(200);
        $responseOwner->assertJson(['status' => 'success']);
    }

    public function test_only_finance_can_access_finance_zone(): void
    {
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $finance = User::factory()->create(['role' => User::ROLE_FINANCE]);

        // Sales cannot access finance zone
        $responseSales = $this->actingAs($sales)->get('/dashboard/finance-zone');
        $responseSales->assertStatus(403);

        // Finance can access finance zone
        $responseFinance = $this->actingAs($finance)->get('/dashboard/finance-zone');
        $responseFinance->assertStatus(200);
        $responseFinance->assertJson(['status' => 'success']);
    }
}
