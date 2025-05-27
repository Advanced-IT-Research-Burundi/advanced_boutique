<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\CreatedBy;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductController
 */
final class ProductControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertViewIs('product.index');
        $response->assertViewHas('products');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('products.create'));

        $response->assertOk();
        $response->assertViewIs('product.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'store',
            \App\Http\Requests\ProductStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $category = Category::factory()->create();
        $purchase_price = fake()->randomFloat(/** decimal_attributes **/);
        $sale_price = fake()->randomFloat(/** decimal_attributes **/);
        $unit = fake()->word();
        $alert_quantity = fake()->randomFloat(/** float_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('products.store'), [
            'name' => $name,
            'category_id' => $category->id,
            'purchase_price' => $purchase_price,
            'sale_price' => $sale_price,
            'unit' => $unit,
            'alert_quantity' => $alert_quantity,
            'created_by' => $created_by->id,
        ]);

        $products = Product::query()
            ->where('name', $name)
            ->where('category_id', $category->id)
            ->where('purchase_price', $purchase_price)
            ->where('sale_price', $sale_price)
            ->where('unit', $unit)
            ->where('alert_quantity', $alert_quantity)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $products);
        $product = $products->first();

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('product.id', $product->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertViewIs('product.show');
        $response->assertViewHas('product');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));

        $response->assertOk();
        $response->assertViewIs('product.edit');
        $response->assertViewHas('product');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'update',
            \App\Http\Requests\ProductUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $product = Product::factory()->create();
        $name = fake()->name();
        $category = Category::factory()->create();
        $purchase_price = fake()->randomFloat(/** decimal_attributes **/);
        $sale_price = fake()->randomFloat(/** decimal_attributes **/);
        $unit = fake()->word();
        $alert_quantity = fake()->randomFloat(/** float_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('products.update', $product), [
            'name' => $name,
            'category_id' => $category->id,
            'purchase_price' => $purchase_price,
            'sale_price' => $sale_price,
            'unit' => $unit,
            'alert_quantity' => $alert_quantity,
            'created_by' => $created_by->id,
        ]);

        $product->refresh();

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('product.id', $product->id);

        $this->assertEquals($name, $product->name);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals($purchase_price, $product->purchase_price);
        $this->assertEquals($sale_price, $product->sale_price);
        $this->assertEquals($unit, $product->unit);
        $this->assertEquals($alert_quantity, $product->alert_quantity);
        $this->assertEquals($created_by->id, $product->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));

        $this->assertSoftDeleted($product);
    }
}
