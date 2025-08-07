<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\DepenseImportationType;
use App\Models\DepensesImportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DepensesImportationController
 */
final class DepensesImportationControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $depensesImportations = DepensesImportation::factory()->count(3)->create();

        $response = $this->get(route('depenses-importations.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DepensesImportationController::class,
            'store',
            \App\Http\Requests\DepensesImportationStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $depense_importation_type = DepenseImportationType::factory()->create();
        $currency = fake()->word();
        $exchange_rate = fake()->randomFloat(/** double_attributes **/);
        $amount = fake()->randomFloat(/** double_attributes **/);
        $amount_currency = fake()->randomFloat(/** double_attributes **/);
        $date = Carbon::parse(fake()->dateTime());
        $user = User::factory()->create();

        $response = $this->post(route('depenses-importations.store'), [
            'depense_importation_type_id' => $depense_importation_type->id,
            'currency' => $currency,
            'exchange_rate' => $exchange_rate,
            'amount' => $amount,
            'amount_currency' => $amount_currency,
            'date' => $date->toDateTimeString(),
            'user_id' => $user->id,
        ]);

        $depensesImportations = DepensesImportation::query()
            ->where('depense_importation_type_id', $depense_importation_type->id)
            ->where('currency', $currency)
            ->where('exchange_rate', $exchange_rate)
            ->where('amount', $amount)
            ->where('amount_currency', $amount_currency)
            ->where('date', $date)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $depensesImportations);
        $depensesImportation = $depensesImportations->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $depensesImportation = DepensesImportation::factory()->create();

        $response = $this->get(route('depenses-importations.show', $depensesImportation));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DepensesImportationController::class,
            'update',
            \App\Http\Requests\DepensesImportationUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $depensesImportation = DepensesImportation::factory()->create();
        $depense_importation_type = DepenseImportationType::factory()->create();
        $currency = fake()->word();
        $exchange_rate = fake()->randomFloat(/** double_attributes **/);
        $amount = fake()->randomFloat(/** double_attributes **/);
        $amount_currency = fake()->randomFloat(/** double_attributes **/);
        $date = Carbon::parse(fake()->dateTime());
        $user = User::factory()->create();

        $response = $this->put(route('depenses-importations.update', $depensesImportation), [
            'depense_importation_type_id' => $depense_importation_type->id,
            'currency' => $currency,
            'exchange_rate' => $exchange_rate,
            'amount' => $amount,
            'amount_currency' => $amount_currency,
            'date' => $date->toDateTimeString(),
            'user_id' => $user->id,
        ]);

        $depensesImportation->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($depense_importation_type->id, $depensesImportation->depense_importation_type_id);
        $this->assertEquals($currency, $depensesImportation->currency);
        $this->assertEquals($exchange_rate, $depensesImportation->exchange_rate);
        $this->assertEquals($amount, $depensesImportation->amount);
        $this->assertEquals($amount_currency, $depensesImportation->amount_currency);
        $this->assertEquals($date, $depensesImportation->date);
        $this->assertEquals($user->id, $depensesImportation->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $depensesImportation = DepensesImportation::factory()->create();

        $response = $this->delete(route('depenses-importations.destroy', $depensesImportation));

        $response->assertNoContent();

        $this->assertModelMissing($depensesImportation);
    }
}
