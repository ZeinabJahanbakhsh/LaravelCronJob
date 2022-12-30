<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RouteUserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_home_screen_shows_welcome_and_status_ok()
    {
        $response = $this->get('/');
        $response->assertViewIs('welcome');
        $response->assertStatus(200);
    }


    public function test_register_user_with_static_data()
    {
        $this->withoutExceptionHandling();
        $this->artisan('migrate:refresh');
        //$this->refreshDatabase();

        //add lia user
        $user = $this->postJson('/api/register', [
            'name' => 'liateam',
            'email' => 'liateam@liateam.com',
            'password' => 'Lia@1234',
            'c_password' => 'Lia@1234',
        ])->assertCreated();

        $user->assertStatus(201);
        //$this->assertDatabaseHas('users', ['email' => $user->email]);

    }


    public function test_register_user_with_faker()
    {

        $user = User::factory()->make();

        $userData = $this->postJson('/api/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'c_password' => $user->password,
        ])->assertCreated();

        $userData->assertStatus(201);

    }


    public function test_successful_response_login()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('passport:install');
        $this->artisan('key:generate');

        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456789'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => '123456789'
        ];

        $response = $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
                'success',
            ]);
        //dd($response->original['data']['token']);

    }


    public function test_get_token_and_return_user_detail()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('passport:install');
        $this->artisan('key:generate');
        $this->withoutMiddleware();

        $user = User::factory()->create([
            'email'    => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456789'),
        ]);

        $payload = [
            'email'    => $user->email,
            'password' => '123456789'
        ];

        $response = $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
                'success',
            ]);

        $token = 'Bearer ' . $response->original['data']['token'];
        //$token = $user->generateToken();
        $headers = ['Authorization' => "Bearer $token"];

        $this->json('POST', 'api/user-detail', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    "id",
                    "name",
                    "email",
                    "email_verified_at",
                    "created_at",
                    "updated_at",
                ],
                'success',
            ]);

    }


    public function test_create_directory()
    {

        $this->artisan('migrate:refresh');
        $this->artisan('passport:install');
        $this->artisan('key:generate');
        $this->withoutMiddleware();

        $name = $this->faker->name;
        $this->json('GET', 'api/createDirectory/'.$name)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                  'new directory'
                ],
                'success',
            ]);

    }


    public function test_create_file()
    {

        $this->artisan('migrate:refresh');
        $this->artisan('passport:install');
        $this->artisan('key:generate');
        $this->withoutMiddleware();

        $name = $this->faker->name;
        $this->json('GET', 'api/createFile/'.$name)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'new file'
                ],
                'success',
            ]);

    }

    //
    public function test_successful_response_log_out()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('passport:install');
        $this->artisan('key:generate');
        $this->withoutMiddleware();

        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456789'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => '123456789'
        ];

        $response = $this->json('POST', 'api/login', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
                'success',
            ]);
        //dd($response->original['data']['token']);
        //$this->actingAs($user, 'api');
        $this->withHeaders([
            'Authorization' => 'Bearer '. $response->original['data']['token'],
        ])->json('POST', 'api/logout')
            ->assertStatus(200)
            ->assertJsonStructure([
            'success',
            'msg'
        ]);

    }

}
