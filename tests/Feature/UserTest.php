<?php

namespace Tests\Feature;

use App\Constants\Endpoint;
use App\Mail\OTPSent;
use App\Mail\VerificationLinkSent;
use App\Models\OtpAbility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $registerData;
    private $factoryUser;
    protected function setUp(): void
    {
        parent::setUp();
        $this->factoryUser = User::factory()->create();
        $this->registerData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
            'referrer_id' => $this->factoryUser->id
        ];
        $otpAbilities = ['*', 'verify_email', 'reset_password', 'change_password'];
        foreach ($otpAbilities as $otpAbility) {
            \App\Models\OtpAbility::factory()->create(['name' => $otpAbility]);
        }
    }

    public function test_register_a_user()
    {
        Mail::fake();
        $response = $this->postJson('api' . Endpoint::REGISTER, $this->registerData);
        $response->assertCreated();
        $response->assertJsonStructure(['user', 'token']);
        $this->assertEquals($response->json('user')['referrer_id'], $this->factoryUser->id);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('one_time_passwords', 1);
        Mail::assertQueued(VerificationLinkSent::class);
    }

    public function test_login()
    {
        $response = $this->postJson('api' . Endpoint::REGISTER, $this->registerData);
        $response->assertCreated();
        $response = $this->postJson('api' . Endpoint::LOGIN, [
            'email' => $this->registerData['email'],
            'password' => $this->registerData['password']
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_logout()
    {
        $response = $this->postJson('api' . Endpoint::REGISTER, $this->registerData);
        $response->assertCreated();
        $response = $this->postJson('api' . Endpoint::LOGIN, [
            'email' => $this->registerData['email'],
            'password' => $this->registerData['password']
        ]);
        $response = $this->postJson('api' . Endpoint::LOGOUT, [], ['Authorization' => 'Bearer ' . $response->json()['token']]);
        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_verify_email()
    {
        $response = $this->postJson('api' . Endpoint::REGISTER, $this->registerData);
        $user = User::find($response->json('user')['id']);
        $user->markEmailAsVerified();
        $this->assertInstanceOf(Carbon::class, $user->otp->used_at);
        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_request_otp()
    {
        Mail::fake();
        $response = $this->actingAs($this->factoryUser)->postJson('api' . Endpoint::REQUEST_OTP, ['name' => 'verify_email']);
        $response->assertOk();
        $this->assertDatabaseCount('one_time_passwords', 1);
        Mail::assertQueued(OTPSent::class);
    }

    public function test_verify_link()
    {
        $user = User::factory()->create();
        $password = $user->generateOTP(OtpAbility::getVerfiyEmailOtpAbility());
        $mailable = new VerificationLinkSent($password, $user->id);
        // echo $mailable->render();
        $mailable->assertSeeInHtml(env("APP_URL") . "/verify-email-link/" . $user->id);
        $mailable->assertSeeInHtml("expires=");
        $mailable->assertSeeInHtml("password=");
        $mailable->assertSeeInHtml("signature=");
    }
}
