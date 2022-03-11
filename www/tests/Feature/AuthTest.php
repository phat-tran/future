<?php

namespace Tests\Feature;

use App\Constants\Constant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Tests web authentication.
     *
     * @return void
     */
    public function testAuth()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response = $this->get('/register');
        $response->assertStatus(200);
        $user     = User::factory()->create();
        $response = $this->actingAs($user)
            ->get('/home');
        $response->assertStatus(200);
    }

    /**
     * Tests API Authentication.
     *
     * @return void
     */
    public function testApiAuth()
    {
        $response = Http::post(config('app.api_host') . Constant::API_OAUTH_TOKEN_URL, [
            'client_secret' => config('app.api_client_secret'),
            'client_id'     => config('app.api_client_id'),
            'grant_type'    => config('app.api_grant_type'),
            'username'      => config('app.api_username'),
            'password'      => config('app.api_password'),
        ]);

        $this->assertTrue($response->status() === 200);
        $this->assertIsArray($response->json());
        $this->assertTrue(isset($response->json()['access_token']));
    }
}
