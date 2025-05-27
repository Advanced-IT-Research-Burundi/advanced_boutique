<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\CreatedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CashTransactionController
 */
final class CashTransactionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $cashTransactions = CashTransaction::factory()->count(3)->create();

        $response = $this->get(route('cash-transactions.index'));

        $response->assertOk();
        $response->assertViewIs('cashTransaction.index');
        $response->assertViewHas('cashTransactions');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('cash-transactions.create'));

        $response->assertOk();
        $response->assertViewIs('cashTransaction.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CashTransactionController::class,
            'store',
            \App\Http\Requests\CashTransactionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $cash_register = CashRegister::factory()->create();
        $type = fake()->word();
        $reference_id = fake()->numberBetween(-10000, 10000);
        $amount = fake()->randomFloat(/** decimal_attributes **/);
        $description = fake()->text();
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('cash-transactions.store'), [
            'cash_register_id' => $cash_register->id,
            'type' => $type,
            'reference_id' => $reference_id,
            'amount' => $amount,
            'description' => $description,
            'created_by' => $created_by->id,
        ]);

        $cashTransactions = CashTransaction::query()
            ->where('cash_register_id', $cash_register->id)
            ->where('type', $type)
            ->where('reference_id', $reference_id)
            ->where('amount', $amount)
            ->where('description', $description)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $cashTransactions);
        $cashTransaction = $cashTransactions->first();

        $response->assertRedirect(route('cashTransactions.index'));
        $response->assertSessionHas('cashTransaction.id', $cashTransaction->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $cashTransaction = CashTransaction::factory()->create();

        $response = $this->get(route('cash-transactions.show', $cashTransaction));

        $response->assertOk();
        $response->assertViewIs('cashTransaction.show');
        $response->assertViewHas('cashTransaction');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $cashTransaction = CashTransaction::factory()->create();

        $response = $this->get(route('cash-transactions.edit', $cashTransaction));

        $response->assertOk();
        $response->assertViewIs('cashTransaction.edit');
        $response->assertViewHas('cashTransaction');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CashTransactionController::class,
            'update',
            \App\Http\Requests\CashTransactionUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $cashTransaction = CashTransaction::factory()->create();
        $cash_register = CashRegister::factory()->create();
        $type = fake()->word();
        $reference_id = fake()->numberBetween(-10000, 10000);
        $amount = fake()->randomFloat(/** decimal_attributes **/);
        $description = fake()->text();
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('cash-transactions.update', $cashTransaction), [
            'cash_register_id' => $cash_register->id,
            'type' => $type,
            'reference_id' => $reference_id,
            'amount' => $amount,
            'description' => $description,
            'created_by' => $created_by->id,
        ]);

        $cashTransaction->refresh();

        $response->assertRedirect(route('cashTransactions.index'));
        $response->assertSessionHas('cashTransaction.id', $cashTransaction->id);

        $this->assertEquals($cash_register->id, $cashTransaction->cash_register_id);
        $this->assertEquals($type, $cashTransaction->type);
        $this->assertEquals($reference_id, $cashTransaction->reference_id);
        $this->assertEquals($amount, $cashTransaction->amount);
        $this->assertEquals($description, $cashTransaction->description);
        $this->assertEquals($created_by->id, $cashTransaction->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $cashTransaction = CashTransaction::factory()->create();

        $response = $this->delete(route('cash-transactions.destroy', $cashTransaction));

        $response->assertRedirect(route('cashTransactions.index'));

        $this->assertSoftDeleted($cashTransaction);
    }
}
