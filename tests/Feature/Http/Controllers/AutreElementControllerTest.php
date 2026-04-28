<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AutreElement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AutreElementController
 */
final class AutreElementControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $autreElements = AutreElement::factory()->count(3)->create();

        $response = $this->get(route('autre-elements.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AutreElementController::class,
            'store',
            \App\Http\Requests\AutreElementStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $date = Carbon::parse(fake()->date());
        $libelle = fake()->word();
        $quantite = fake()->randomFloat(/** decimal_attributes **/);
        $valeur = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->word();
        $type_element = fake()->randomElement(/** enum_attributes **/);

        $response = $this->post(route('autre-elements.store'), [
            'date' => $date->toDateString(),
            'libelle' => $libelle,
            'quantite' => $quantite,
            'valeur' => $valeur,
            'devise' => $devise,
            'type_element' => $type_element,
        ]);

        $autreElements = AutreElement::query()
            ->where('date', $date)
            ->where('libelle', $libelle)
            ->where('quantite', $quantite)
            ->where('valeur', $valeur)
            ->where('devise', $devise)
            ->where('type_element', $type_element)
            ->get();
        $this->assertCount(1, $autreElements);
        $autreElement = $autreElements->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $autreElement = AutreElement::factory()->create();

        $response = $this->get(route('autre-elements.show', $autreElement));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AutreElementController::class,
            'update',
            \App\Http\Requests\AutreElementUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $autreElement = AutreElement::factory()->create();
        $date = Carbon::parse(fake()->date());
        $libelle = fake()->word();
        $quantite = fake()->randomFloat(/** decimal_attributes **/);
        $valeur = fake()->randomFloat(/** decimal_attributes **/);
        $devise = fake()->word();
        $type_element = fake()->randomElement(/** enum_attributes **/);

        $response = $this->put(route('autre-elements.update', $autreElement), [
            'date' => $date->toDateString(),
            'libelle' => $libelle,
            'quantite' => $quantite,
            'valeur' => $valeur,
            'devise' => $devise,
            'type_element' => $type_element,
        ]);

        $autreElement->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($date, $autreElement->date);
        $this->assertEquals($libelle, $autreElement->libelle);
        $this->assertEquals($quantite, $autreElement->quantite);
        $this->assertEquals($valeur, $autreElement->valeur);
        $this->assertEquals($devise, $autreElement->devise);
        $this->assertEquals($type_element, $autreElement->type_element);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $autreElement = AutreElement::factory()->create();

        $response = $this->delete(route('autre-elements.destroy', $autreElement));

        $response->assertNoContent();

        $this->assertModelMissing($autreElement);
    }
}
