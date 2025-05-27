<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\CreatedBy;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SaleController
 */
final class SaleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $sales = Sale::factory()->count(3)->create();

        $response = $this->get(route('sales.index'));

        $response->assertOk();
        $response->assertViewIs('sale.index');
        $response->assertViewHas('sales');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('sales.create'));

        $response->assertOk();
        $response->assertViewIs('sale.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SaleController::class,
            'store',
            \App\Http\Requests\SaleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $client = Client::factory()->create();
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $total_amount = fake()->randomFloat(/** decimal_attributes **/);
        $paid_amount = fake()->randomFloat(/** decimal_attributes **/);
        $due_amount = fake()->randomFloat(/** decimal_attributes **/);
        $sale_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('sales.store'), [
            'client_id' => $client->id,
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'sale_date' => $sale_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $sales = Sale::query()
            ->where('client_id', $client->id)
            ->where('stock_id', $stock->id)
            ->where('user_id', $user->id)
            ->where('total_amount', $total_amount)
            ->where('paid_amount', $paid_amount)
            ->where('due_amount', $due_amount)
            ->where('sale_date', $sale_date)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $sales);
        $sale = $sales->first();

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('sale.id', $sale->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->get(route('sales.show', $sale));

        $response->assertOk();
        $response->assertViewIs('sale.show');
        $response->assertViewHas('sale');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->get(route('sales.edit', $sale));

        $response->assertOk();
        $response->assertViewIs('sale.edit');
        $response->assertViewHas('sale');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SaleController::class,
            'update',
            \App\Http\Requests\SaleUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $sale = Sale::factory()->create();
        $client = Client::factory()->create();
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $total_amount = fake()->randomFloat(/** decimal_attributes **/);
        $paid_amount = fake()->randomFloat(/** decimal_attributes **/);
        $due_amount = fake()->randomFloat(/** decimal_attributes **/);
        $sale_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('sales.update', $sale), [
            'client_id' => $client->id,
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'sale_date' => $sale_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $sale->refresh();

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('sale.id', $sale->id);

        $this->assertEquals($client->id, $sale->client_id);
        $this->assertEquals($stock->id, $sale->stock_id);
        $this->assertEquals($user->id, $sale->user_id);
        $this->assertEquals($total_amount, $sale->total_amount);
        $this->assertEquals($paid_amount, $sale->paid_amount);
        $this->assertEquals($due_amount, $sale->due_amount);
        $this->assertEquals($sale_date, $sale->sale_date);
        $this->assertEquals($created_by->id, $sale->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->delete(route('sales.destroy', $sale));

        $response->assertRedirect(route('sales.index'));

        $this->assertSoftDeleted($sale);
    }
}
