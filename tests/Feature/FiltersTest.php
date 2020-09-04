<?php

namespace Lagumen\LaravelEssential\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Lagumen\LaravelEssential\Tests\FeatureTest;
use Lagumen\LaravelEssential\Tests\Models\User;

class FiltersTest extends FeatureTest
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Config::set('laravel_essential.filter_namespace', 'Lagumen\LaravelEssential\Tests\Filters');
    }

    /** @test */
    public function it_can_perform_basic_search_query()
    {
        $user1 = factory(User::class)->create(['name' => 'Ralph Lagumen']);
        $user2 = factory(User::class)->create(['name' => 'John Doe']);
        $user3 = factory(User::class)->create(['name' => 'Rodrigo Duterte']);

        $this->getJson(route('users.index', ['search' => 'Lagumen']))
            ->assertJsonFragment([
                'id'   => $user1->id,
                'name' => $user1->name,
            ])
            ->assertJsonMissing(['id' => $user2->id])
            ->assertJsonMissing(['id' => $user3->id]);
    }

    /** @test */
    public function it_can_perform_search_through_model_relationship()
    {
        $user1 = factory(User::class)->create(['name' => 'Ralph Lagumen']);
        $user2 = factory(User::class)->create(['name' => 'John Doe']);
        $user3 = factory(User::class)->create(['name' => 'Rodrigo Duterte']);

        $user1->setting()->create(['timezone' => 'Asia/Manila']);

        $this->getJson(route('users.index', ['search' => 'Asia/Manila']))
            ->assertJsonFragment([
                'id'   => $user1->id,
                'name' => $user1->name,
            ])
            ->assertJsonMissing(['id' => $user2->id])
            ->assertJsonMissing(['id' => $user3->id]);
    }

    /** @test */
    public function it_can_perform_filtering()
    {
        $this->withoutExceptionHandling();
        $user1 = factory(User::class)->create(['active' => true]);
        $user2 = factory(User::class)->create(['active' => false]);
        $user3 = factory(User::class)->create(['active' => true]);

        $this->getJson(route('users.index', ['active' => true]))
            ->assertJsonFragment(['id' => $user1->id])
            ->assertJsonFragment(['id' => $user3->id])
            ->assertJsonMissing(['id' => $user2->id]);
    }

    /** @test */
    public function it_can_perform_sorting()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $response = $this->getJson(route('users.index', ['sort' => 'id|desc']))
            ->assertSuccessful();

        $this->assertTrue($response->data()->first()->is($user3));
        $this->assertTrue($response->data()->last()->is($user1));
    }

    /** @test */
    public function it_can_perform_multiple_filtering_actions()
    {
        $user1 = factory(User::class)->create(['name' => 'Ralph Doe', 'active' => true]);
        $user2 = factory(User::class)->create(['name' => 'John Doe', 'active' => true]);
        $user3 = factory(User::class)->create(['name' => 'Sunshine Doe', 'active' => false]);
        $user4 = factory(User::class)->create(['name' => 'Rodrigo Duterte', 'active' => false]);

        $response = $this->getJson(route('users.index', [
            'search' => 'Doe',
            'active' => true,
            'sort'   => 'id|desc',
        ]))
            ->assertJsonFragment(['id' => $user1->id])
            ->assertJsonFragment(['id' => $user2->id])
            ->assertJsonMissing(['id' => $user3->id])
            ->assertJsonMissing(['id' => $user4->id]);

        //sort..
        $this->assertTrue($response->data()->first()->is($user2));
        $this->assertTrue($response->data()->last()->is($user1));
    }
}
