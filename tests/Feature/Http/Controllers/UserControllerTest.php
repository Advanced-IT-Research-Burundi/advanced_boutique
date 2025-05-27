<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserController
 */
final class UserControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $users = User::factory()->count(3)->create();

        $response = $this->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('user.index');
        $response->assertViewHas('users');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('users.create'));

        $response->assertOk();
        $response->assertViewIs('user.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserController::class,
            'store',
            \App\Http\Requests\UserStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $first_name = fake()->firstName();
        $last_name = fake()->lastName();
        $email = fake()->safeEmail();
        $password = fake()->password();
        $status = fake()->word();
        $role = fake()->word();
        $must_change_password = fake()->boolean();
        $two_factor_enabled = fake()->boolean();
        $user = User::factory()->create();

        $response = $this->post(route('users.store'), [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'status' => $status,
            'role' => $role,
            'must_change_password' => $must_change_password,
            'two_factor_enabled' => $two_factor_enabled,
            'user_id' => $user->id,
        ]);

        $users = User::query()
            ->where('first_name', $first_name)
            ->where('last_name', $last_name)
            ->where('email', $email)
            ->where('password', $password)
            ->where('status', $status)
            ->where('role', $role)
            ->where('must_change_password', $must_change_password)
            ->where('two_factor_enabled', $two_factor_enabled)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $users);
        $user = $users->first();

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('user.id', $user->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertViewIs('user.show');
        $response->assertViewHas('user');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.edit', $user));

        $response->assertOk();
        $response->assertViewIs('user.edit');
        $response->assertViewHas('user');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserController::class,
            'update',
            \App\Http\Requests\UserUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $user = User::factory()->create();
        $first_name = fake()->firstName();
        $last_name = fake()->lastName();
        $email = fake()->safeEmail();
        $password = fake()->password();
        $status = fake()->word();
        $role = fake()->word();
        $must_change_password = fake()->boolean();
        $two_factor_enabled = fake()->boolean();

        $response = $this->put(route('users.update', $user), [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'status' => $status,
            'role' => $role,
            'must_change_password' => $must_change_password,
            'two_factor_enabled' => $two_factor_enabled,
            'user_id' => $user->id,
        ]);

        $user->refresh();

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('user.id', $user->id);

        $this->assertEquals($first_name, $user->first_name);
        $this->assertEquals($last_name, $user->last_name);
        $this->assertEquals($email, $user->email);
        $this->assertEquals($password, $user->password);
        $this->assertEquals($status, $user->status);
        $this->assertEquals($role, $user->role);
        $this->assertEquals($must_change_password, $user->must_change_password);
        $this->assertEquals($two_factor_enabled, $user->two_factor_enabled);
        $this->assertEquals($user->id, $user->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));

        $this->assertSoftDeleted($user);
    }
}
