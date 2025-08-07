<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DepenseImportationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DepenseImportationTypeController
 */
final class DepenseImportationTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $depenseImportationTypes = DepenseImportationType::factory()->count(3)->create();

        $response = $this->get(route('depense-importation-types.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DepenseImportationTypeController::class,
            'store',
            \App\Http\Requests\DepenseImportationTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('depense-importation-types.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(depenseImportationTypes, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $depenseImportationType = DepenseImportationType::factory()->create();

        $response = $this->get(route('depense-importation-types.show', $depenseImportationType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DepenseImportationTypeController::class,
            'update',
            \App\Http\Requests\DepenseImportationTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $depenseImportationType = DepenseImportationType::factory()->create();

        $response = $this->put(route('depense-importation-types.update', $depenseImportationType));

        $depenseImportationType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $depenseImportationType = DepenseImportationType::factory()->create();

        $response = $this->delete(route('depense-importation-types.destroy', $depenseImportationType));

        $response->assertNoContent();

        $this->assertModelMissing($depenseImportationType);
    }
}
