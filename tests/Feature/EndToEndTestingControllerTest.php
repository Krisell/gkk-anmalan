<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Tests\TestCase;

class EndToEndTestingControllerTest extends TestCase
{
    /** @test */
    public function the_e2e_routes_are_only_available_in_the_testing_environment()
    {
        app()->detectEnvironment(fn () => 'local');

        // The e2e routes are only loaded in the testing environment, however in this
        // test, the RouteServiceProvider has already run at this point. However there is
        // also a guard in the constructor of EndToEndTestingController which aborts with a 404
        // in case the invironment is anything other than testing. An additional layer
        // of testing could be executing `php artisan route:list` and grepping for
        // those at container build-time, asserting they don't exist.
        $this->post('__e2e__/php', ['command' => '2 * 7'])->assertNotFound();
        $this->post('__e2e__/loginSuperadmin')->assertNotFound();
    }

    /** @test */
    public function it_can_login_a_new_user()
    {
        $this->assertNull(auth()->user());
        $this->assertDatabaseCount(User::class, 0);

        $response = $this->post('__e2e__/login');

        $user = User::first();
        $response->assertJson([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]);

        $this->assertNotNull(auth()->user());
        $this->assertDatabaseCount(User::class, 1);
    }

    /** @test */
    public function it_can_login_a_new_user_with_specified_attributes()
    {
        $this->assertNull(auth()->user());
        User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);

        $response = $this->post('__e2e__/login', [
            'attributes' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ],
        ]);

        $response->assertJson([
            'id' => User::orderByDesc('id')->first()->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertNotNull(auth()->user());
        $this->assertDatabaseCount(User::class, 2);
    }

    /** @test */
    public function it_can_login_an_existing_user()
    {
        $this->assertNull(auth()->user());

        $user = User::factory()->create();

        $response = $this->post('__e2e__/login', [
            'attributes' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ],
        ]);

        $response->assertJson([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ]);

        $this->assertNotNull(auth()->user());
        $this->assertDatabaseCount(User::class, 1);
    }

    /** @test */
    public function a_specific_model_can_be_found_and_defaults_to_app_namespace()
    {
        Document::factory()->create();
        $document = Document::factory()->create(['name' => 'My document']);
        Document::factory()->create();

        $this->post('__e2e__/first', [
            'model' => 'Document',
            'attributes' => [
                'name' => 'My document',
            ],
        ])->assertJson($document->fresh()->toArray());
    }

    /**
     * @test
     **/
    public function unrecognized_models_returns_not_found()
    {
        $this->post('__e2e__/factory', [
            'model' => 'SomeModel',
        ])->assertNotFound();
    }

    /** @test */
    public function a_model_can_be_specified_with_fhe_fully_qualified_classname()
    {
        Document::factory()->create();
        $document = Document::factory()->create(['name' => 'Some document']);
        Document::factory()->create();

        $this->post('__e2e__/first', [
            'model' => 'App\Models\Document',
            'attributes' => [
                'name' => 'Some document',
            ],
        ])->assertJson($document->fresh()->toArray());
    }

    /** @test */
    public function a_model_can_be_created_via_a_factory()
    {
        $this->assertCount(0, Document::all());

        $this->post('__e2e__/factory', [
            'model' => 'Document',
        ])->assertCreated();

        $this->assertCount(1, Document::all());
    }

    /** @test */
    public function a_model_can_be_created_with_specified_attributes()
    {
        $this->post('__e2e__/factory', [
            'model' => 'Document',
            'attributes' => [
                'name' => 'Some doc',
            ],
        ])->assertCreated();

        $this->assertDatabaseHas(Document::class, [
            'name' => 'Some doc',
        ]);
    }

    /** @test */
    public function a_model_can_be_updated()
    {
        $document = Document::factory()->create(['name' => 'Before']);

        $this->post('__e2e__/update', [
            'model' => 'Document',
            'attributes' => [
                'id' => $document->id,
            ],
            'values' => [
                'name' => 'After',
            ],
        ])->assertOk();

        $this->assertEquals('After', $document->fresh()->name);
    }

    /** @test */
    public function multiple_models_can_be_created()
    {
        $this->post('__e2e__/factory', [
            'model' => 'Document',
            'times' => 3,
        ])->assertOk();

        $this->assertCount(3, Document::all());
    }

    /** @test */
    public function arbitrary_php_can_be_executed()
    {
        $this->post('__e2e__/php', [
            'command' => 'return "PHP"[1] . 2 * 7 . "HELLO"[2];',
        ])->assertJson(['result' => 'H14L']);
    }

    /** @test */
    public function the_trailing_semicolon_can_be_omitted_for_php_code_for_convenience()
    {
        $this->post('__e2e__/php', [
            'command' => 'return 2 + 3',
        ])->assertJson(['result' => '5']);
    }

    /** @test */
    public function the_leading_return_can_be_omitted()
    {
        $this->post('__e2e__/php', [
            'command' => '2 + 3;',
        ])->assertJson(['result' => '5']);
    }

    /** @test */
    public function code_is_never_evaled_outside_of_the_testing_environment()
    {
        // Cause the testing-env check in the EndToEndTestingController constructor to not abort
        config(['app.testing-non-testing-environment' => true]);

        app()->detectEnvironment(fn () => 'local');
        $this->post('__e2e__/php', ['command' => '2 + 3;'])->assertNotFound();

        app()->detectEnvironment(fn () => 'production');
        $this->post('__e2e__/php', ['command' => '2 + 3;'])->assertNotFound();

        app()->detectEnvironment(fn () => 'testing');
        $this->post('__e2e__/php', ['command' => '2 + 3;'])->assertJson(['result' => '5']);
    }
}
