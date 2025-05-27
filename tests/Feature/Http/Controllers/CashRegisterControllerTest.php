<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CreatedBy;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CashRegisterController
 */
final class CashRegisterControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $cashRegisters = CashRegister::factory()->count(3)->create();

        $response = $this->get(route('cash-registers.index'));

        $response->assertOk();
        $response->assertViewIs('cashRegister.index');
        $response->assertViewHas('cashRegisters');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('cash-registers.create'));

        $response->assertOk();
        $response->assertViewIs('cashRegister.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CashRegisterController::class,
            'store',
            \App\Http\Requests\CashRegisterStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $opening_balance = fake()->randomFloat(/** decimal_attributes **/);
        $closing_balance = fake()->randomFloat(/** decimal_attributes **/);
        $status = fake()->word();
        $opened_at = Carbon::parse(fake()->dateTime());
        $closed_at = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('cash-registers.store'), [
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'opening_balance' => $opening_balance,
            'closing_balance' => $closing_balance,
            'status' => $status,
            'opened_at' => $opened_at->toDateTimeString(),
            'closed_at' => $closed_at->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $cashRegisters = CashRegister::query()
            ->where('user_id', $user->id)
            ->where('stock_id', $stock->id)
            ->where('opening_balance', $opening_balance)
            ->where('closing_balance', $closing_balance)
            ->where('status', $status)
            ->where('opened_at', $opened_at)
            ->where('closed_at', $closed_at)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $cashRegisters);
        $cashRegister = $cashRegisters->first();

        $response->assertRedirect(route('cashRegisters.index'));
        $response->assertSessionHas('cashRegister.id', $cashRegister->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $cashRegister = CashRegister::factory()->create();

        $response = $this->get(route('cash-registers.show', $cashRegister));

        $response->assertOk();
        $response->assertViewIs('cashRegister.show');
        $response->assertViewHas('cashRegister');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $cashRegister = CashRegister::factory()->create();

        $response = $this->get(route('cash-registers.edit', $cashRegister));

        $response->assertOk();
        $response->assertViewIs('cashRegister.edit');
        $response->assertViewHas('cashRegister');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CashRegisterController::class,
            'update',
            \App\Http\Requests\CashRegisterUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $cashRegister = CashRegister::factory()->create();
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $opening_balance = fake()->randomFloat(/** decimal_attributes **/);
        $closing_balance = fake()->randomFloat(/** decimal_attributes **/);
        $status = fake()->word();
        $opened_at = Carbon::parse(fake()->dateTime());
        $closed_at = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('cash-registers.update', $cashRegister), [
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'opening_balance' => $opening_balance,
            'closing_balance' => $closing_balance,
            'status' => $status,
            'opened_at' => $opened_at->toDateTimeString(),
            'closed_at' => $closed_at->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $cashRegister->refresh();

        $response->assertRedirect(route('cashRegisters.index'));
        $response->assertSessionHas('cashRegister.id', $cashRegister->id);

        $this->assertEquals($user->id, $cashRegister->user_id);
        $this->assertEquals($stock->id, $cashRegister->stock_id);
        $this->assertEquals($opening_balance, $cashRegister->opening_balance);
        $this->assertEquals($closing_balance, $cashRegister->closing_balance);
        $this->assertEquals($status, $cashRegister->status);
        $this->assertEquals($opened_at, $cashRegister->opened_at);
        $this->assertEquals($closed_at, $cashRegister->closed_at);
        $this->assertEquals($created_by->id, $cashRegister->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $cashRegister = CashRegister::factory()->create();

        $response = $this->delete(route('cash-registers.destroy', $cashRegister));

        $response->assertRedirect(route('cashRegisters.index'));

        $this->assertSoftDeleted($cashRegister);
    }
}
