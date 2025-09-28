<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ProduitsTmp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProduitsTmpController
 */
final class ProduitsTmpControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $produitsTmps = ProduitsTmp::factory()->count(3)->create();

        $response = $this->get(route('produits-tmps.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProduitsTmpController::class,
            'store',
            \App\Http\Requests\ProduitsTmpStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('produits-tmps.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(produitsTmps, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $produitsTmp = ProduitsTmp::factory()->create();

        $response = $this->get(route('produits-tmps.show', $produitsTmp));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProduitsTmpController::class,
            'update',
            \App\Http\Requests\ProduitsTmpUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $produitsTmp = ProduitsTmp::factory()->create();

        $response = $this->put(route('produits-tmps.update', $produitsTmp));

        $produitsTmp->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $produitsTmp = ProduitsTmp::factory()->create();

        $response = $this->delete(route('produits-tmps.destroy', $produitsTmp));

        $response->assertNoContent();

        $this->assertModelMissing($produitsTmp);
    }
}
