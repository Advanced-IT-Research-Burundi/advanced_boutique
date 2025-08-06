<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ProductCompanyName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductCompanyNameController
 */
final class ProductCompanyNameControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $productCompanyNames = ProductCompanyName::factory()->count(3)->create();

        $response = $this->get(route('product-company-names.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductCompanyNameController::class,
            'store',
            \App\Http\Requests\ProductCompanyNameStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $product_code = fake()->word();
        $company_code = fake()->word();
        $item_name = fake()->word();
        $size = fake()->word();
        $packing_details = fake()->word();
        $mfg_location = fake()->word();
        $weight_kg = fake()->randomFloat(/** double_attributes **/);
        $order_qty = fake()->randomFloat(/** double_attributes **/);
        $total_weight = fake()->randomFloat(/** double_attributes **/);
        $pu = fake()->word();
        $total_weight_pu = fake()->randomFloat(/** float_attributes **/);

        $response = $this->post(route('product-company-names.store'), [
            'product_code' => $product_code,
            'company_code' => $company_code,
            'item_name' => $item_name,
            'size' => $size,
            'packing_details' => $packing_details,
            'mfg_location' => $mfg_location,
            'weight_kg' => $weight_kg,
            'order_qty' => $order_qty,
            'total_weight' => $total_weight,
            'pu' => $pu,
            'total_weight_pu' => $total_weight_pu,
        ]);

        $productCompanyNames = ProductCompanyName::query()
            ->where('product_code', $product_code)
            ->where('company_code', $company_code)
            ->where('item_name', $item_name)
            ->where('size', $size)
            ->where('packing_details', $packing_details)
            ->where('mfg_location', $mfg_location)
            ->where('weight_kg', $weight_kg)
            ->where('order_qty', $order_qty)
            ->where('total_weight', $total_weight)
            ->where('pu', $pu)
            ->where('total_weight_pu', $total_weight_pu)
            ->get();
        $this->assertCount(1, $productCompanyNames);
        $productCompanyName = $productCompanyNames->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $productCompanyName = ProductCompanyName::factory()->create();

        $response = $this->get(route('product-company-names.show', $productCompanyName));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductCompanyNameController::class,
            'update',
            \App\Http\Requests\ProductCompanyNameUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $productCompanyName = ProductCompanyName::factory()->create();
        $product_code = fake()->word();
        $company_code = fake()->word();
        $item_name = fake()->word();
        $size = fake()->word();
        $packing_details = fake()->word();
        $mfg_location = fake()->word();
        $weight_kg = fake()->randomFloat(/** double_attributes **/);
        $order_qty = fake()->randomFloat(/** double_attributes **/);
        $total_weight = fake()->randomFloat(/** double_attributes **/);
        $pu = fake()->word();
        $total_weight_pu = fake()->randomFloat(/** float_attributes **/);

        $response = $this->put(route('product-company-names.update', $productCompanyName), [
            'product_code' => $product_code,
            'company_code' => $company_code,
            'item_name' => $item_name,
            'size' => $size,
            'packing_details' => $packing_details,
            'mfg_location' => $mfg_location,
            'weight_kg' => $weight_kg,
            'order_qty' => $order_qty,
            'total_weight' => $total_weight,
            'pu' => $pu,
            'total_weight_pu' => $total_weight_pu,
        ]);

        $productCompanyName->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($product_code, $productCompanyName->product_code);
        $this->assertEquals($company_code, $productCompanyName->company_code);
        $this->assertEquals($item_name, $productCompanyName->item_name);
        $this->assertEquals($size, $productCompanyName->size);
        $this->assertEquals($packing_details, $productCompanyName->packing_details);
        $this->assertEquals($mfg_location, $productCompanyName->mfg_location);
        $this->assertEquals($weight_kg, $productCompanyName->weight_kg);
        $this->assertEquals($order_qty, $productCompanyName->order_qty);
        $this->assertEquals($total_weight, $productCompanyName->total_weight);
        $this->assertEquals($pu, $productCompanyName->pu);
        $this->assertEquals($total_weight_pu, $productCompanyName->total_weight_pu);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $productCompanyName = ProductCompanyName::factory()->create();

        $response = $this->delete(route('product-company-names.destroy', $productCompanyName));

        $response->assertNoContent();

        $this->assertModelMissing($productCompanyName);
    }
}
