<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreditTvaDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditTvaDetailController
 */
final class CreditTvaDetailControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $creditTvaDetails = CreditTvaDetail::factory()->count(3)->create();

        $response = $this->get(route('credit-tva-details.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTvaDetailController::class,
            'store',
            \App\Http\Requests\CreditTvaDetailStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('credit-tva-details.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(creditTvaDetails, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $creditTvaDetail = CreditTvaDetail::factory()->create();

        $response = $this->get(route('credit-tva-details.show', $creditTvaDetail));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTvaDetailController::class,
            'update',
            \App\Http\Requests\CreditTvaDetailUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $creditTvaDetail = CreditTvaDetail::factory()->create();

        $response = $this->put(route('credit-tva-details.update', $creditTvaDetail));

        $creditTvaDetail->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $creditTvaDetail = CreditTvaDetail::factory()->create();

        $response = $this->delete(route('credit-tva-details.destroy', $creditTvaDetail));

        $response->assertNoContent();

        $this->assertModelMissing($creditTvaDetail);
    }
}
