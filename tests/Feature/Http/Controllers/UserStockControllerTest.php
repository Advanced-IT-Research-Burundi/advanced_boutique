<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\Stock;
use App\Models\User;
use App\Models\UserStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserStockController
 */
final class UserStockControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $userStocks = UserStock::factory()->count(3)->create();

        $response = $this->get(route('user-stocks.index'));

        $response->assertOk();
        $response->assertViewIs('userStock.index');
        $response->assertViewHas('userStocks');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('user-stocks.create'));

        $response->assertOk();
        $response->assertViewIs('userStock.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserStockController::class,
            'store',
            \App\Http\Requests\UserStockStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('user-stocks.store'), [
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'created_by' => $created_by->id,
        ]);

        $userStocks = UserStock::query()
            ->where('user_id', $user->id)
            ->where('stock_id', $stock->id)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $userStocks);
        $userStock = $userStocks->first();

        $response->assertRedirect(route('userStocks.index'));
        $response->assertSessionHas('userStock.id', $userStock->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $userStock = UserStock::factory()->create();

        $response = $this->get(route('user-stocks.show', $userStock));

        $response->assertOk();
        $response->assertViewIs('userStock.show');
        $response->assertViewHas('userStock');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $userStock = UserStock::factory()->create();

        $response = $this->get(route('user-stocks.edit', $userStock));

        $response->assertOk();
        $response->assertViewIs('userStock.edit');
        $response->assertViewHas('userStock');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserStockController::class,
            'update',
            \App\Http\Requests\UserStockUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $userStock = UserStock::factory()->create();
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('user-stocks.update', $userStock), [
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'created_by' => $created_by->id,
        ]);

        $userStock->refresh();

        $response->assertRedirect(route('userStocks.index'));
        $response->assertSessionHas('userStock.id', $userStock->id);

        $this->assertEquals($user->id, $userStock->user_id);
        $this->assertEquals($stock->id, $userStock->stock_id);
        $this->assertEquals($created_by->id, $userStock->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $userStock = UserStock::factory()->create();

        $response = $this->delete(route('user-stocks.destroy', $userStock));

        $response->assertRedirect(route('userStocks.index'));

        $this->assertSoftDeleted($userStock);
    }
}
