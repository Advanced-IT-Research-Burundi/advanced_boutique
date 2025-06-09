<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VehiculeController
 */
final class VehiculeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $vehicules = Vehicule::factory()->count(3)->create();

        $response = $this->get(route('vehicules.index'));

        $response->assertOk();
        $response->assertViewIs('vehicule.index');
        $response->assertViewHas('vehicules');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('vehicules.create'));

        $response->assertOk();
        $response->assertViewIs('vehicule.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VehiculeController::class,
            'store',
            \App\Http\Requests\VehiculeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $status = fake()->word();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('vehicules.store'), [
            'name' => $name,
            'status' => $status,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
        ]);

        $vehicules = Vehicule::query()
            ->where('name', $name)
            ->where('status', $status)
            ->where('created_by', $created_by->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $vehicules);
        $vehicule = $vehicules->first();

        $response->assertRedirect(route('vehicules.index'));
        $response->assertSessionHas('vehicule.id', $vehicule->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $vehicule = Vehicule::factory()->create();

        $response = $this->get(route('vehicules.show', $vehicule));

        $response->assertOk();
        $response->assertViewIs('vehicule.show');
        $response->assertViewHas('vehicule');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $vehicule = Vehicule::factory()->create();

        $response = $this->get(route('vehicules.edit', $vehicule));

        $response->assertOk();
        $response->assertViewIs('vehicule.edit');
        $response->assertViewHas('vehicule');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VehiculeController::class,
            'update',
            \App\Http\Requests\VehiculeUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $vehicule = Vehicule::factory()->create();
        $name = fake()->name();
        $status = fake()->word();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('vehicules.update', $vehicule), [
            'name' => $name,
            'status' => $status,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
        ]);

        $vehicule->refresh();

        $response->assertRedirect(route('vehicules.index'));
        $response->assertSessionHas('vehicule.id', $vehicule->id);

        $this->assertEquals($name, $vehicule->name);
        $this->assertEquals($status, $vehicule->status);
        $this->assertEquals($created_by->id, $vehicule->created_by);
        $this->assertEquals($user->id, $vehicule->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $vehicule = Vehicule::factory()->create();

        $response = $this->delete(route('vehicules.destroy', $vehicule));

        $response->assertRedirect(route('vehicules.index'));

        $this->assertSoftDeleted($vehicule);
    }
}
