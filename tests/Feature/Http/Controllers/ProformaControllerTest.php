<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Agency;
use App\Models\CreatedBy;
use App\Models\Proforma;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProformaController
 */
final class ProformaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $proformas = Proforma::factory()->count(3)->create();

        $response = $this->get(route('proformas.index'));

        $response->assertOk();
        $response->assertViewIs('proforma.index');
        $response->assertViewHas('proformas');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('proformas.create'));

        $response->assertOk();
        $response->assertViewIs('proforma.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProformaController::class,
            'store',
            \App\Http\Requests\ProformaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $total_amount = fake()->randomFloat(/** double_attributes **/);
        $due_amount = fake()->randomFloat(/** double_attributes **/);
        $sale_date = Carbon::parse(fake()->date());
        $agency = Agency::factory()->create();
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('proformas.store'), [
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'total_amount' => $total_amount,
            'due_amount' => $due_amount,
            'sale_date' => $sale_date->toDateString(),
            'agency_id' => $agency->id,
            'created_by' => $created_by->id,
        ]);

        $proformas = Proforma::query()
            ->where('stock_id', $stock->id)
            ->where('user_id', $user->id)
            ->where('total_amount', $total_amount)
            ->where('due_amount', $due_amount)
            ->where('sale_date', $sale_date)
            ->where('agency_id', $agency->id)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $proformas);
        $proforma = $proformas->first();

        $response->assertRedirect(route('proformas.index'));
        $response->assertSessionHas('proforma.id', $proforma->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $proforma = Proforma::factory()->create();

        $response = $this->get(route('proformas.show', $proforma));

        $response->assertOk();
        $response->assertViewIs('proforma.show');
        $response->assertViewHas('proforma');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $proforma = Proforma::factory()->create();

        $response = $this->get(route('proformas.edit', $proforma));

        $response->assertOk();
        $response->assertViewIs('proforma.edit');
        $response->assertViewHas('proforma');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProformaController::class,
            'update',
            \App\Http\Requests\ProformaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $proforma = Proforma::factory()->create();
        $stock = Stock::factory()->create();
        $user = User::factory()->create();
        $total_amount = fake()->randomFloat(/** double_attributes **/);
        $due_amount = fake()->randomFloat(/** double_attributes **/);
        $sale_date = Carbon::parse(fake()->date());
        $agency = Agency::factory()->create();
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('proformas.update', $proforma), [
            'stock_id' => $stock->id,
            'user_id' => $user->id,
            'total_amount' => $total_amount,
            'due_amount' => $due_amount,
            'sale_date' => $sale_date->toDateString(),
            'agency_id' => $agency->id,
            'created_by' => $created_by->id,
        ]);

        $proforma->refresh();

        $response->assertRedirect(route('proformas.index'));
        $response->assertSessionHas('proforma.id', $proforma->id);

        $this->assertEquals($stock->id, $proforma->stock_id);
        $this->assertEquals($user->id, $proforma->user_id);
        $this->assertEquals($total_amount, $proforma->total_amount);
        $this->assertEquals($due_amount, $proforma->due_amount);
        $this->assertEquals($sale_date, $proforma->sale_date);
        $this->assertEquals($agency->id, $proforma->agency_id);
        $this->assertEquals($created_by->id, $proforma->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $proforma = Proforma::factory()->create();

        $response = $this->delete(route('proformas.destroy', $proforma));

        $response->assertRedirect(route('proformas.index'));

        $this->assertSoftDeleted($proforma);
    }
}
