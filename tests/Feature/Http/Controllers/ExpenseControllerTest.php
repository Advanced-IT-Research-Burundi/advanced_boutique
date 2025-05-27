<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ExpenseController
 */
final class ExpenseControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $expenses = Expense::factory()->count(3)->create();

        $response = $this->get(route('expenses.index'));

        $response->assertOk();
        $response->assertViewIs('expense.index');
        $response->assertViewHas('expenses');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('expenses.create'));

        $response->assertOk();
        $response->assertViewIs('expense.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ExpenseController::class,
            'store',
            \App\Http\Requests\ExpenseStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $expense_type = ExpenseType::factory()->create();
        $amount = fake()->randomFloat(/** decimal_attributes **/);
        $description = fake()->text();
        $expense_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('expenses.store'), [
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'expense_type_id' => $expense_type->id,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $expense_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $expenses = Expense::query()
            ->where('stock_id', $stock->id)
            ->where('user_id', $user->id)
            ->where('expense_type_id', $expense_type->id)
            ->where('amount', $amount)
            ->where('description', $description)
            ->where('expense_date', $expense_date)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $expenses);
        $expense = $expenses->first();

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('expense.id', $expense->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->get(route('expenses.show', $expense));

        $response->assertOk();
        $response->assertViewIs('expense.show');
        $response->assertViewHas('expense');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->get(route('expenses.edit', $expense));

        $response->assertOk();
        $response->assertViewIs('expense.edit');
        $response->assertViewHas('expense');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ExpenseController::class,
            'update',
            \App\Http\Requests\ExpenseUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $expense = Expense::factory()->create();
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $expense_type = ExpenseType::factory()->create();
        $amount = fake()->randomFloat(/** decimal_attributes **/);
        $description = fake()->text();
        $expense_date = Carbon::parse(fake()->dateTime());
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('expenses.update', $expense), [
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'expense_type_id' => $expense_type->id,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $expense_date->toDateTimeString(),
            'created_by' => $created_by->id,
        ]);

        $expense->refresh();

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('expense.id', $expense->id);

        $this->assertEquals($stock->id, $expense->stock_id);
        $this->assertEquals($user->id, $expense->user_id);
        $this->assertEquals($expense_type->id, $expense->expense_type_id);
        $this->assertEquals($amount, $expense->amount);
        $this->assertEquals($description, $expense->description);
        $this->assertEquals($expense_date, $expense->expense_date);
        $this->assertEquals($created_by->id, $expense->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->delete(route('expenses.destroy', $expense));

        $response->assertRedirect(route('expenses.index'));

        $this->assertSoftDeleted($expense);
    }
}
