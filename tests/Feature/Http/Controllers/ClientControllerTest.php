<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\CreatedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ClientController
 */
final class ClientControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $clients = Client::factory()->count(3)->create();

        $response = $this->get(route('clients.index'));

        $response->assertOk();
        $response->assertViewIs('client.index');
        $response->assertViewHas('clients');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('clients.create'));

        $response->assertOk();
        $response->assertViewIs('client.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ClientController::class,
            'store',
            \App\Http\Requests\ClientStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $balance = fake()->randomFloat(/** decimal_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('clients.store'), [
            'name' => $name,
            'balance' => $balance,
            'created_by' => $created_by->id,
        ]);

        $clients = Client::query()
            ->where('name', $name)
            ->where('balance', $balance)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $clients);
        $client = $clients->first();

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('client.id', $client->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $client = Client::factory()->create();

        $response = $this->get(route('clients.show', $client));

        $response->assertOk();
        $response->assertViewIs('client.show');
        $response->assertViewHas('client');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $client = Client::factory()->create();

        $response = $this->get(route('clients.edit', $client));

        $response->assertOk();
        $response->assertViewIs('client.edit');
        $response->assertViewHas('client');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ClientController::class,
            'update',
            \App\Http\Requests\ClientUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $client = Client::factory()->create();
        $name = fake()->name();
        $balance = fake()->randomFloat(/** decimal_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('clients.update', $client), [
            'name' => $name,
            'balance' => $balance,
            'created_by' => $created_by->id,
        ]);

        $client->refresh();

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('client.id', $client->id);

        $this->assertEquals($name, $client->name);
        $this->assertEquals($balance, $client->balance);
        $this->assertEquals($created_by->id, $client->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $client = Client::factory()->create();

        $response = $this->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));

        $this->assertSoftDeleted($client);
    }
}
