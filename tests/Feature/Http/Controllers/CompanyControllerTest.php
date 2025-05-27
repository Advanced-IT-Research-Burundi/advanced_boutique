<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\CreatedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CompanyController
 */
final class CompanyControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $companies = Company::factory()->count(3)->create();

        $response = $this->get(route('companies.index'));

        $response->assertOk();
        $response->assertViewIs('company.index');
        $response->assertViewHas('companies');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('companies.create'));

        $response->assertOk();
        $response->assertViewIs('company.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CompanyController::class,
            'store',
            \App\Http\Requests\CompanyStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $tp_name = fake()->word();
        $tp_type = fake()->word();
        $tp_TIN = fake()->word();
        $is_actif = fake()->boolean();
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('companies.store'), [
            'tp_name' => $tp_name,
            'tp_type' => $tp_type,
            'tp_TIN' => $tp_TIN,
            'is_actif' => $is_actif,
            'created_by' => $created_by->id,
        ]);

        $companies = Company::query()
            ->where('tp_name', $tp_name)
            ->where('tp_type', $tp_type)
            ->where('tp_TIN', $tp_TIN)
            ->where('is_actif', $is_actif)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $companies);
        $company = $companies->first();

        $response->assertRedirect(route('companies.index'));
        $response->assertSessionHas('company.id', $company->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $company = Company::factory()->create();

        $response = $this->get(route('companies.show', $company));

        $response->assertOk();
        $response->assertViewIs('company.show');
        $response->assertViewHas('company');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $company = Company::factory()->create();

        $response = $this->get(route('companies.edit', $company));

        $response->assertOk();
        $response->assertViewIs('company.edit');
        $response->assertViewHas('company');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CompanyController::class,
            'update',
            \App\Http\Requests\CompanyUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $company = Company::factory()->create();
        $tp_name = fake()->word();
        $tp_type = fake()->word();
        $tp_TIN = fake()->word();
        $is_actif = fake()->boolean();
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('companies.update', $company), [
            'tp_name' => $tp_name,
            'tp_type' => $tp_type,
            'tp_TIN' => $tp_TIN,
            'is_actif' => $is_actif,
            'created_by' => $created_by->id,
        ]);

        $company->refresh();

        $response->assertRedirect(route('companies.index'));
        $response->assertSessionHas('company.id', $company->id);

        $this->assertEquals($tp_name, $company->tp_name);
        $this->assertEquals($tp_type, $company->tp_type);
        $this->assertEquals($tp_TIN, $company->tp_TIN);
        $this->assertEquals($is_actif, $company->is_actif);
        $this->assertEquals($created_by->id, $company->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $company = Company::factory()->create();

        $response = $this->delete(route('companies.destroy', $company));

        $response->assertRedirect(route('companies.index'));

        $this->assertSoftDeleted($company);
    }
}
