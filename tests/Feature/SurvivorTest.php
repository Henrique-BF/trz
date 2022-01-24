<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\LastLocation;
use App\Models\Survivor;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurvivorTest extends TestCase
{
    use RefreshDatabase;

    protected $survivor_1;
    protected $survivor_2;
    protected $items;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ItemSeeder::class);
        $this->items = Item::all();
        $this->survivor_1 = Survivor::factory()->notInfected()->create();
        $this->survivor_2 = Survivor::factory()->notInfected()->create();
    }

    public function test_index_all_survivors()
    {
        $uri = route('survivors.index');
        $this
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJsonCount(2);
    }

    public function test_store_survivor()
    {
        $survivor = Survivor::factory()->make()->toArray();
        $last_location = LastLocation::factory()->make()->toArray();
        $items = [
            'items' => [
                [
                    'id' => $this->items->first()->id,
                    'qty' => 3
                ],
                [
                    'id' => $this->items->last()->id,
                    'qty' => 3
                ]
            ]
        ];
        $payload = $survivor + $last_location + $items;

        $uri = route('survivors.store', $payload);

        $response = $this->postJson($uri);
        
        $response->assertSuccessful();

        $this
            ->assertDatabaseHas('survivors', $survivor)
            ->assertDatabaseHas('last_locations', $last_location)
            ->assertDatabaseHas('inventory_item', [
                'inventory_id' => $response->json()['inventory']['id'],
                'item_id' => $payload['items'][0]['id'],
                'qty' => $payload['items'][0]['qty']
            ])
            ->assertDatabaseHas('inventory_item', [
                'inventory_id' => $response->json()['inventory']['id'],
                'item_id' => $payload['items'][1]['id'],
                'qty' => $payload['items'][1]['qty']
            ]);
    }

    public function test_show_survivor()
    {
        $uri = route('survivors.show', ['survivor' => $this->survivor_1->id]);

        $this
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJsonFragment(
                $this->survivor_1
                    ->load('inventory', 'lastLocation', 'inventory.items', 'infected')
                    ->toArray()
            );
    }

    public function test_update_survivor()
    {
        $payload = [
            'survivor' => $this->survivor_1->id,
            'name' => 'João Borges',
	        'age' => 19
        ];

        $uri = route('survivors.show', $payload);

        $this
            ->putJson($uri)
            ->assertSuccessful();
        $this
            ->assertDatabaseHas('survivors', [
                'id' => $payload['survivor'],
                'name' => $payload['name'],
	            'age' => $payload['age']
            ]);
    }

    public function test_update_last_location_survivor()
    {
        $last_location = LastLocation::factory()->make()->toArray();
        $payload = ['survivor' => $this->survivor_1->id] + $last_location;

        $uri = route('survivors.update.last_location', $payload);

        $this
            ->postJson($uri)
            ->assertSuccessful();
        $this
            ->assertDatabaseHas('last_locations',
                ['id' => $payload['survivor']] + $last_location
            );
    }
}
