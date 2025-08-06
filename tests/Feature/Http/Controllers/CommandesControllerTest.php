<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Commande;
use App\Models\Commandes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CommandesController
 */
final class CommandesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $commandes = Commandes::factory()->count(3)->create();

        $response = $this->get(route('commandes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommandesController::class,
            'store',
            \App\Http\Requests\CommandesStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $vehicule_id = fake()->numberBetween(-10000, 10000);

        $response = $this->post(route('commandes.store'), [
            'vehicule_id' => $vehicule_id,
        ]);

        $commandes = Commande::query()
            ->where('vehicule_id', $vehicule_id)
            ->get();
        $this->assertCount(1, $commandes);
        $commande = $commandes->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $commande = Commandes::factory()->create();

        $response = $this->get(route('commandes.show', $commande));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommandesController::class,
            'update',
            \App\Http\Requests\CommandesUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $commande = Commandes::factory()->create();
        $vehicule_id = fake()->numberBetween(-10000, 10000);

        $response = $this->put(route('commandes.update', $commande), [
            'vehicule_id' => $vehicule_id,
        ]);

        $commande->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($vehicule_id, $commande->vehicule_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $commande = Commandes::factory()->create();
        $commande = Commande::factory()->create();

        $response = $this->delete(route('commandes.destroy', $commande));

        $response->assertNoContent();

        $this->assertModelMissing($commande);
    }
}
