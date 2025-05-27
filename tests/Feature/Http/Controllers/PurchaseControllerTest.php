<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PurchaseController
 */
final class PurchaseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $purchases = Purchase::factory()->count(3)->create();

        $response = $this->get(route('purchases.index'));

        $response->assertOk();
        $response->assertViewIs('purchase.index');
        $response->assertViewHas('purchases');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('purchases.create'));

        $response->assertOk();
        $response->assertViewIs('purchase.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseController::class,
            'store',
            \App\Http\Requests\PurchaseStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $supplier = Supplier::factory()->create();
        $stock = Stock::factory()->create();
        $total_amount = fake()->randomFloat(/** decimal_attributes **/);
        $paid_amount = fake()->randomFloat(/** decimal_attributes **/);
        $due_amount = fake()->randomFloat(/** decimal_attributes **/);
        $purchase_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'stock_id' => $stock->id,
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'purchase_date' => $purchase_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $purchases = Purchase::query()
            ->where('supplier_id', $supplier->id)
            ->where('stock_id', $stock->id)
            ->where('total_amount', $total_amount)
            ->where('paid_amount', $paid_amount)
            ->where('due_amount', $due_amount)
            ->where('purchase_date', $purchase_date)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $purchases);
        $purchase = $purchases->first();

        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('purchase.id', $purchase->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get(route('purchases.show', $purchase));

        $response->assertOk();
        $response->assertViewIs('purchase.show');
        $response->assertViewHas('purchase');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get(route('purchases.edit', $purchase));

        $response->assertOk();
        $response->assertViewIs('purchase.edit');
        $response->assertViewHas('purchase');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PurchaseController::class,
            'update',
            \App\Http\Requests\PurchaseUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $purchase = Purchase::factory()->create();
        $supplier = Supplier::factory()->create();
        $stock = Stock::factory()->create();
        $total_amount = fake()->randomFloat(/** decimal_attributes **/);
        $paid_amount = fake()->randomFloat(/** decimal_attributes **/);
        $due_amount = fake()->randomFloat(/** decimal_attributes **/);
        $purchase_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('purchases.update', $purchase), [
            'supplier_id' => $supplier->id,
            'stock_id' => $stock->id,
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'purchase_date' => $purchase_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $purchase->refresh();

        $response->assertRedirect(route('purchases.index'));
        $response->assertSessionHas('purchase.id', $purchase->id);

        $this->assertEquals($supplier->id, $purchase->supplier_id);
        $this->assertEquals($stock->id, $purchase->stock_id);
        $this->assertEquals($total_amount, $purchase->total_amount);
        $this->assertEquals($paid_amount, $purchase->paid_amount);
        $this->assertEquals($due_amount, $purchase->due_amount);
        $this->assertEquals($purchase_date, $purchase->purchase_date);
        $this->assertEquals($created_by->id, $purchase->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $purchase = Purchase::factory()->create();

        $response = $this->delete(route('purchases.destroy', $purchase));

        $response->assertRedirect(route('purchases.index'));

        $this->assertSoftDeleted($purchase);
    }
}
