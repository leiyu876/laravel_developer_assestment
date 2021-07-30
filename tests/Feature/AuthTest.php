<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_can_register()
    {
        $response = $this->create()->assertStatus(201);

        $this->assertEquals($response->json()['message'], 'User successfully created.');

        $this->assertCount(1, User::all());
        
        $user = $this->validUser();

        $this->assertDatabaseHas('users', [
            'name' => $user['name'],
            'user_name' => $user['user_name'],
            'email' => $user['email'],
        ]);
    }

    public function test_confirm_6_digit_pin()
    {
        $this->create();

        $user = User::first();

        $this->assertNull($user->registered_at);

        $response = $this->postJson('/api/register/'.$user->id.'/confirm', [
                'pin' => $user->email_confirmation_pin,
            ])->assertStatus(200);

        $this->assertEquals($response->json()['message'], 'Account verified.');

        $user->refresh();
        
        $this->assertNotNull($user->registered_at);
    }

    public function test_confirm_pin_invalid()
    {
        $this->create();

        $user = User::first();

        $this->assertNull($user->registered_at);

        $response = $this->postJson('/api/register/'.$user->id.'/confirm', [
                'pin' => '1234567', // invalid pin
            ])->assertStatus(422);
        
        $this->assertEquals($response->json()['errors']['pin'][0], 'pin not valid.');

        $user->refresh();
        
        $this->assertNull($user->registered_at);
    }

    public function test_can_login_if_confirmed()
    {
        $this->create();

        $user = User::first();
        
        $this->postJson('/api/register/'.$user->id.'/confirm', [
            'pin' => $user->email_confirmation_pin,
        ]);

        $response = $this->postJson('/api/login', [
                'user_name' => $user->user_name,
                'password' => $this->validUser()['password'],
            ])->assertStatus(200);

        $this->assertEquals($response->json()['message'], 'Successfully Login.');
    }

    public function test_cant_login_if_not_confirmed()
    {
        $this->create();

        $user = User::first();

        $response = $this->postJson('/api/login', [
                'user_name' => $user->user_name,
                'password' => $this->validUser()['password'],
            ])->assertStatus(422);

        $this->assertEquals($response->json()['message'], 'Please confirm registration using 6 digit pin emailed to you.');
    }

    public function test_can_update_profile_if_login()
    {
        $this->create();

        $user = User::first();

        $this->postJson('/api/register/'.$user->id.'/confirm', [
            'pin' => $user->email_confirmation_pin,
        ]);
        
        $response = $this->postJson('/api/login', [
            'user_name' => $user->user_name,
            'password' => $this->validUser()['password'],
        ]);

        $response = $this->patchJson('/api/profile/'.$user->id.'?api_token='.$response->json()['token'], [
                'name' => 'My New Name',
            ])->assertStatus(200);

        $this->assertEquals($response->json()['message'], 'Profile Updated.');
        $this->assertEquals($user->refresh()->name, 'My New Name');
    }

    public function test_cant_update_profile_if_not_authenticated()
    {
        $this->create();

        $user = User::first();

        $response = $this->patchJson('/api/profile/'.$user->id.'?api_token=123214123', [
            'name' => 'My New Name',
        ])->assertStatus(401);

        $this->assertEquals($response->json()['message'], 'Unauthenticated.');
    }

    // Below are helper methods

    private function validUser()
    {
        return [
            'name' => 'Leo Nuneza',
            'user_name' => 'leiyu876',
            'email' => 'leiyu876@gmail.com',
            'password' => 'myvalidpassword',
            'password_confirmation' => 'myvalidpassword',
        ];
    }

    private function create($arr = [])
    {
        return $this->postJson('/api/register', array_merge($this->validUser(), $arr));
    }
}
