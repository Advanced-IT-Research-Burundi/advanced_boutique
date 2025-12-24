<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InvoincePointer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InvoincePointerController
 */
final class InvoincePointerControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $invoincePointers = InvoincePointer::factory()->count(3)->create();

        $response = $this->get(route('invoince-pointers.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InvoincePointerController::class,
            'store',
            \App\Http\Requests\InvoincePointerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $response = $this->post(route('invoince-pointers.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(invoincePointers, [ /* ... */ ]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $invoincePointer = InvoincePointer::factory()->create();

        $response = $this->get(route('invoince-pointers.show', $invoincePointer));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\InvoincePointerController::class,
            'update',
            \App\Http\Requests\InvoincePointerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $invoincePointer = InvoincePointer::factory()->create();

        $response = $this->put(route('invoince-pointers.update', $invoincePointer));

        $invoincePointer->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $invoincePointer = InvoincePointer::factory()->create();

        $response = $this->delete(route('invoince-pointers.destroy', $invoincePointer));

        $response->assertNoContent();

        $this->assertModelMissing($invoincePointer);
    }
}
