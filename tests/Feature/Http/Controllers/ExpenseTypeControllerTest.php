<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ExpenseTypeController
 */
final class ExpenseTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $expenseTypes = ExpenseType::factory()->count(3)->create();

        $response = $this->get(route('expense-types.index'));

        $response->assertOk();
        $response->assertViewIs('expenseType.index');
        $response->assertViewHas('expenseTypes');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('expense-types.create'));

        $response->assertOk();
        $response->assertViewIs('expenseType.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ExpenseTypeController::class,
            'store',
            \App\Http\Requests\ExpenseTypeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $description = fake()->text();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('expense-types.store'), [
            'name' => $name,
            'description' => $description,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
        ]);

        $expenseTypes = ExpenseType::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('created_by', $created_by->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $expenseTypes);
        $expenseType = $expenseTypes->first();

        $response->assertRedirect(route('expenseTypes.index'));
        $response->assertSessionHas('expenseType.id', $expenseType->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->get(route('expense-types.show', $expenseType));

        $response->assertOk();
        $response->assertViewIs('expenseType.show');
        $response->assertViewHas('expenseType');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->get(route('expense-types.edit', $expenseType));

        $response->assertOk();
        $response->assertViewIs('expenseType.edit');
        $response->assertViewHas('expenseType');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ExpenseTypeController::class,
            'update',
            \App\Http\Requests\ExpenseTypeUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $expenseType = ExpenseType::factory()->create();
        $name = fake()->name();
        $description = fake()->text();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('expense-types.update', $expenseType), [
            'name' => $name,
            'description' => $description,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
        ]);

        $expenseType->refresh();

        $response->assertRedirect(route('expenseTypes.index'));
        $response->assertSessionHas('expenseType.id', $expenseType->id);

        $this->assertEquals($name, $expenseType->name);
        $this->assertEquals($description, $expenseType->description);
        $this->assertEquals($created_by->id, $expenseType->created_by);
        $this->assertEquals($user->id, $expenseType->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $expenseType = ExpenseType::factory()->create();

        $response = $this->delete(route('expense-types.destroy', $expenseType));

        $response->assertRedirect(route('expenseTypes.index'));

        $this->assertSoftDeleted($expenseType);
    }
}
