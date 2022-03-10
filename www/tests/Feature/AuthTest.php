<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    const API_URL             = 'http://nginx_api';
    const API_OAUTH_TOKEN_URL = self::API_URL . '/oauth/token';

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
        $response = Http::post(self::API_OAUTH_TOKEN_URL, [
            'client_secret' => config('app.client_secret'),
            'client_id'     => config('app.client_id'),
            'grant_type'    => config('app.grant_type'),
            'username'      => config('app.username'),
            'password'      => config('app.password'),
        ]);

        $this->assertTrue($response->status() === 200);
        $this->assertIsArray($response->json());
        $this->assertTrue(isset($response->json()['access_token']));
    }
}
