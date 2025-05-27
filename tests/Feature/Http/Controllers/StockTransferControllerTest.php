<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\FromStock;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\ToStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StockTransferController
 */
final class StockTransferControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $stockTransfers = StockTransfer::factory()->count(3)->create();

        $response = $this->get(route('stock-transfers.index'));

        $response->assertOk();
        $response->assertViewIs('stockTransfer.index');
        $response->assertViewHas('stockTransfers');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('stock-transfers.create'));

        $response->assertOk();
        $response->assertViewIs('stockTransfer.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StockTransferController::class,
            'store',
            \App\Http\Requests\StockTransferStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $from_stock = FromStock::factory()->create();
        $to_stock = ToStock::factory()->create();
        $user = User::factory()->create();
        $transfer_date = Carbon::parse(fake()->dateTime());
        $note = fake()->text();
        $created_by = CreatedBy::factory()->create();
        $stock = Stock::factory()->create();

        $response = $this->post(route('stock-transfers.store'), [
            'from_stock_id' => $from_stock->id,
            'to_stock_id' => $to_stock->id,
            'user_id' => $user->id,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'note' => $note,
            'created_by' => $created_by->id,
            'stock_id' => $stock->id,
        ]);

        $stockTransfers = StockTransfer::query()
            ->where('from_stock_id', $from_stock->id)
            ->where('to_stock_id', $to_stock->id)
            ->where('user_id', $user->id)
            ->where('transfer_date', $transfer_date)
            ->where('note', $note)
            ->where('created_by', $created_by->id)
            ->where('stock_id', $stock->id)
            ->get();
        $this->assertCount(1, $stockTransfers);
        $stockTransfer = $stockTransfers->first();

        $response->assertRedirect(route('stockTransfers.index'));
        $response->assertSessionHas('stockTransfer.id', $stockTransfer->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $response = $this->get(route('stock-transfers.show', $stockTransfer));

        $response->assertOk();
        $response->assertViewIs('stockTransfer.show');
        $response->assertViewHas('stockTransfer');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $response = $this->get(route('stock-transfers.edit', $stockTransfer));

        $response->assertOk();
        $response->assertViewIs('stockTransfer.edit');
        $response->assertViewHas('stockTransfer');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StockTransferController::class,
            'update',
            \App\Http\Requests\StockTransferUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $stockTransfer = StockTransfer::factory()->create();
        $from_stock = FromStock::factory()->create();
        $to_stock = ToStock::factory()->create();
        $user = User::factory()->create();
        $transfer_date = Carbon::parse(fake()->dateTime());
        $note = fake()->text();
        $created_by = CreatedBy::factory()->create();
        $stock = Stock::factory()->create();

        $response = $this->put(route('stock-transfers.update', $stockTransfer), [
            'from_stock_id' => $from_stock->id,
            'to_stock_id' => $to_stock->id,
            'user_id' => $user->id,
            'transfer_date' => $transfer_date->toDateTimeString(),
            'note' => $note,
            'created_by' => $created_by->id,
            'stock_id' => $stock->id,
        ]);

        $stockTransfer->refresh();

        $response->assertRedirect(route('stockTransfers.index'));
        $response->assertSessionHas('stockTransfer.id', $stockTransfer->id);

        $this->assertEquals($from_stock->id, $stockTransfer->from_stock_id);
        $this->assertEquals($to_stock->id, $stockTransfer->to_stock_id);
        $this->assertEquals($user->id, $stockTransfer->user_id);
        $this->assertEquals($transfer_date, $stockTransfer->transfer_date);
        $this->assertEquals($note, $stockTransfer->note);
        $this->assertEquals($created_by->id, $stockTransfer->created_by);
        $this->assertEquals($stock->id, $stockTransfer->stock_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $stockTransfer = StockTransfer::factory()->create();

        $response = $this->delete(route('stock-transfers.destroy', $stockTransfer));

        $response->assertRedirect(route('stockTransfers.index'));

        $this->assertSoftDeleted($stockTransfer);
    }
}
