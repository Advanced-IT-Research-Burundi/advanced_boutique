<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CommandeDetail;
use App\Models\CommandeDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CommandeDetailsController
 */
final class CommandeDetailsControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $commandeDetails = CommandeDetails::factory()->count(3)->create();

        $response = $this->get(route('commande-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommandeDetailsController::class,
            'store',
            \App\Http\Requests\CommandeDetailsStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $commande_id = fake()->numberBetween(-10000, 10000);
        $company_code = fake()->numberBetween(-10000, 10000);
        $prix_unitaire = fake()->randomFloat(/** double_attributes **/);

        $response = $this->post(route('commande-details.store'), [
            'commande_id' => $commande_id,
            'company_code' => $company_code,
            'prix_unitaire' => $prix_unitaire,
        ]);

        $commandeDetails = CommandeDetail::query()
            ->where('commande_id', $commande_id)
            ->where('company_code', $company_code)
            ->where('prix_unitaire', $prix_unitaire)
            ->get();
        $this->assertCount(1, $commandeDetails);
        $commandeDetail = $commandeDetails->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $commandeDetail = CommandeDetails::factory()->create();

        $response = $this->get(route('commande-details.show', $commandeDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CommandeDetailsController::class,
            'update',
            \App\Http\Requests\CommandeDetailsUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $commandeDetail = CommandeDetails::factory()->create();
        $commande_id = fake()->numberBetween(-10000, 10000);
        $company_code = fake()->numberBetween(-10000, 10000);
        $prix_unitaire = fake()->randomFloat(/** double_attributes **/);

        $response = $this->put(route('commande-details.update', $commandeDetail), [
            'commande_id' => $commande_id,
            'company_code' => $company_code,
            'prix_unitaire' => $prix_unitaire,
        ]);

        $commandeDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($commande_id, $commandeDetail->commande_id);
        $this->assertEquals($company_code, $commandeDetail->company_code);
        $this->assertEquals($prix_unitaire, $commandeDetail->prix_unitaire);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $commandeDetail = CommandeDetails::factory()->create();
        $commandeDetail = CommandeDetail::factory()->create();

        $response = $this->delete(route('commande-details.destroy', $commandeDetail));

        $response->assertNoContent();

        $this->assertModelMissing($commandeDetail);
    }
}
