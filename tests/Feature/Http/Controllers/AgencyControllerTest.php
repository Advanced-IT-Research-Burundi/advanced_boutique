<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Agency;
use App\Models\Company;
use App\Models\CreatedBy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AgencyController
 */
final class AgencyControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $agencies = Agency::factory()->count(3)->create();

        $response = $this->get(route('agencies.index'));

        $response->assertOk();
        $response->assertViewIs('agency.index');
        $response->assertViewHas('agencies');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('agencies.create'));

        $response->assertOk();
        $response->assertViewIs('agency.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AgencyController::class,
            'store',
            \App\Http\Requests\AgencyStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $company = Company::factory()->create();
        $code = fake()->word();
        $name = fake()->name();
        $adresse = fake()->word();
        $is_main_office = fake()->boolean();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();
        $agency = Agency::factory()->create();

        $response = $this->post(route('agencies.store'), [
            'company_id' => $company->id,
            'code' => $code,
            'name' => $name,
            'adresse' => $adresse,
            'is_main_office' => $is_main_office,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
            'agency_id' => $agency->id,
        ]);

        $agencies = Agency::query()
            ->where('company_id', $company->id)
            ->where('code', $code)
            ->where('name', $name)
            ->where('adresse', $adresse)
            ->where('is_main_office', $is_main_office)
            ->where('created_by', $created_by->id)
            ->where('user_id', $user->id)
            ->where('agency_id', $agency->id)
            ->get();
        $this->assertCount(1, $agencies);
        $agency = $agencies->first();

        $response->assertRedirect(route('agencies.index'));
        $response->assertSessionHas('agency.id', $agency->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $agency = Agency::factory()->create();

        $response = $this->get(route('agencies.show', $agency));

        $response->assertOk();
        $response->assertViewIs('agency.show');
        $response->assertViewHas('agency');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $agency = Agency::factory()->create();

        $response = $this->get(route('agencies.edit', $agency));

        $response->assertOk();
        $response->assertViewIs('agency.edit');
        $response->assertViewHas('agency');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AgencyController::class,
            'update',
            \App\Http\Requests\AgencyUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $agency = Agency::factory()->create();
        $company = Company::factory()->create();
        $code = fake()->word();
        $name = fake()->name();
        $adresse = fake()->word();
        $is_main_office = fake()->boolean();
        $created_by = CreatedBy::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('agencies.update', $agency), [
            'company_id' => $company->id,
            'code' => $code,
            'name' => $name,
            'adresse' => $adresse,
            'is_main_office' => $is_main_office,
            'created_by' => $created_by->id,
            'user_id' => $user->id,
            'agency_id' => $agency->id,
        ]);

        $agency->refresh();

        $response->assertRedirect(route('agencies.index'));
        $response->assertSessionHas('agency.id', $agency->id);

        $this->assertEquals($company->id, $agency->company_id);
        $this->assertEquals($code, $agency->code);
        $this->assertEquals($name, $agency->name);
        $this->assertEquals($adresse, $agency->adresse);
        $this->assertEquals($is_main_office, $agency->is_main_office);
        $this->assertEquals($created_by->id, $agency->created_by);
        $this->assertEquals($user->id, $agency->user_id);
        $this->assertEquals($agency->id, $agency->agency_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $agency = Agency::factory()->create();

        $response = $this->delete(route('agencies.destroy', $agency));

        $response->assertRedirect(route('agencies.index'));

        $this->assertSoftDeleted($agency);
    }
}
