<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Vehicule;
use App\Models\VehiculeDepense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VehiculeDepenseController
 */
final class VehiculeDepenseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $vehiculeDepenses = VehiculeDepense::factory()->count(3)->create();

        $response = $this->get(route('vehicule-depenses.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VehiculeDepenseController::class,
            'store',
            \App\Http\Requests\VehiculeDepenseStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $vehicule = Vehicule::factory()->create();
        $amount = fake()->randomFloat(/** double_attributes **/);
        $date = Carbon::parse(fake()->dateTime());
        $user = User::factory()->create();

        $response = $this->post(route('vehicule-depenses.store'), [
            'vehicule_id' => $vehicule->id,
            'amount' => $amount,
            'date' => $date->toDateTimeString(),
            'user_id' => $user->id,
        ]);

        $vehiculeDepenses = VehiculeDepense::query()
            ->where('vehicule_id', $vehicule->id)
            ->where('amount', $amount)
            ->where('date', $date)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $vehiculeDepenses);
        $vehiculeDepense = $vehiculeDepenses->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $vehiculeDepense = VehiculeDepense::factory()->create();

        $response = $this->get(route('vehicule-depenses.show', $vehiculeDepense));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\VehiculeDepenseController::class,
            'update',
            \App\Http\Requests\VehiculeDepenseUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $vehiculeDepense = VehiculeDepense::factory()->create();
        $vehicule = Vehicule::factory()->create();
        $amount = fake()->randomFloat(/** double_attributes **/);
        $date = Carbon::parse(fake()->dateTime());
        $user = User::factory()->create();

        $response = $this->put(route('vehicule-depenses.update', $vehiculeDepense), [
            'vehicule_id' => $vehicule->id,
            'amount' => $amount,
            'date' => $date->toDateTimeString(),
            'user_id' => $user->id,
        ]);

        $vehiculeDepense->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($vehicule->id, $vehiculeDepense->vehicule_id);
        $this->assertEquals($amount, $vehiculeDepense->amount);
        $this->assertEquals($date, $vehiculeDepense->date);
        $this->assertEquals($user->id, $vehiculeDepense->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $vehiculeDepense = VehiculeDepense::factory()->create();

        $response = $this->delete(route('vehicule-depenses.destroy', $vehiculeDepense));

        $response->assertNoContent();

        $this->assertModelMissing($vehiculeDepense);
    }
}
