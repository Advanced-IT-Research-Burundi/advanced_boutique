<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreditTva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CreditTvaController
 */
final class CreditTvaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $creditTvas = CreditTva::factory()->count(3)->create();

        $response = $this->get(route('credit-tvas.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTvaController::class,
            'store',
            \App\Http\Requests\CreditTvaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $is_actif = fake()->boolean();

        $response = $this->post(route('credit-tvas.store'), [
            'is_actif' => $is_actif,
        ]);

        $creditTvas = CreditTva::query()
            ->where('is_actif', $is_actif)
            ->get();
        $this->assertCount(1, $creditTvas);
        $creditTva = $creditTvas->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $creditTva = CreditTva::factory()->create();

        $response = $this->get(route('credit-tvas.show', $creditTva));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CreditTvaController::class,
            'update',
            \App\Http\Requests\CreditTvaUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $creditTva = CreditTva::factory()->create();
        $is_actif = fake()->boolean();

        $response = $this->put(route('credit-tvas.update', $creditTva), [
            'is_actif' => $is_actif,
        ]);

        $creditTva->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($is_actif, $creditTva->is_actif);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $creditTva = CreditTva::factory()->create();

        $response = $this->delete(route('credit-tvas.destroy', $creditTva));

        $response->assertNoContent();

        $this->assertModelMissing($creditTva);
    }
}
