<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Survivor;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeTest extends TestCase
{
    use RefreshDatabase;

    protected $survivor_1;
    protected $survivor_2;
    protected $survivor_infected;
    protected $items;

    protected function setUp():void
    {
        parent::setUp();

        $this->seed(ItemSeeder::class);
        $this->items = Item::all();
        $this->survivor_1 = Survivor::factory()->notInfected()->create();
        $this->survivor_2 = Survivor::factory()->notInfected()->create();
    }

    public function text_index_items()
    {
        $uri = route('get_items');

        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJsonCount($this->items->count())
            ->assertJsonFragment($this->items);
    }

    public function test_trade_with_correct_amount_of_points_and_items_on_inventory()
    {
        $this->setUpItemsInSurvivors();

        $uri = route('trade', [
            'survivor_1_id' => $this->survivor_1->id,
            'survivor_2_id' => $this->survivor_2->id,
            'items_survivor_1' => [
                [
                    'id' => $this->items->where('points', 14)->first()->id,
                    'qty' => '1',
                ],
                [
                    'id' => $this->items->where('points', 10)->first()->id,
                    'qty' => '1',
                ],
            ],
            'items_survivor_2' => [
                [
                    'id' => $this->items->where('points', 8)->first()->id,
                    'qty' => '3',
                ],
            ],
        ]);

        $this
            ->postJson($uri)->dump()
            ->assertSuccessful();

        $this
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_1->inventory->id,
                'item_id' => $this->items->where('points', 8)->first()->id,
                'qty' => 3,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_2->inventory->id,
                'item_id' => $this->items->where('points', 14)->first()->id,
                'qty' => 1,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_2->inventory->id,
                'item_id' => $this->items->where('points', 10)->first()->id,
                'qty' => 1,
            ])
            ->assertDatabaseCount('inventory_item', 3);
    }

    public function test_trade_with_incorrect_amount_of_points()
    {
        $this->setUpOneDiferentItemForEachSurvivor();

        $uri = route('trade', [
            'survivor_1_id' => $this->survivor_1->id,
            'survivor_2_id' => $this->survivor_2->id,
            'items_survivor_1' => [
                [
                    'id' => $this->survivor_1->inventory->items->first()->id,
                    'qty' => '1',
                ],
            ],
            'items_survivor_2' => [
                [
                    'id' => $this->survivor_2->inventory->items->first()->id,
                    'qty' => '1',
                ],
            ],
        ]);

        $this
            ->postJson($uri)->dump()
            ->assertUnprocessable();

        $this
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_1->inventory->id,
                'item_id' => $this->survivor_1->inventory->items->first()->id,
                'qty' => 1,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_2->inventory->id,
                'item_id' => $this->survivor_2->inventory->items->first()->id,
                'qty' => 1,
            ])
            ->assertDatabaseCount('inventory_item', 2);
    }

    public function test_trade_without_amount_of_items_survivor_1()
    {
        $this->setUpInsuficientItemsForSurvivorOne();

        $uri = route('trade', [
            'survivor_1_id' => $this->survivor_1->id,
            'survivor_2_id' => $this->survivor_2->id,
            'items_survivor_1' => [
                [
                    'id' => $this->items->where('points', 12)->first()->id,
                    'qty' => '2',
                ],
            ],
            'items_survivor_2' => [
                [
                    'id' => $this->items->where('points', 8)->first()->id,
                    'qty' => '3',
                ],
            ],
        ]);

        $this
            ->postJson($uri)->dump()
            ->assertUnprocessable();

        $this
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_1->inventory->id,
                'item_id' => $this->items->where('points', 12)->first()->id,
                'qty' => 1,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_2->inventory->id,
                'item_id' => $this->items->where('points', 8)->first()->id,
                'qty' => 3,
            ])
            ->assertDatabaseCount('inventory_item', 2);
    }

    public function test_trade_without_amount_of_items_survivor_2()
    {
        $this->setUpInsuficientItemsForSurvivorTwo();

        $uri = route('trade', [
            'survivor_1_id' => $this->survivor_1->id,
            'survivor_2_id' => $this->survivor_2->id,
            'items_survivor_1' => [
                [
                    'id' => $this->items->where('points', 12)->first()->id,
                    'qty' => '2',
                ],
            ],
            'items_survivor_2' => [
                [
                    'id' => $this->items->where('points', 8)->first()->id,
                    'qty' => '3',
                ],
            ],
        ]);

        $this
            ->postJson($uri)->dump()
            ->assertUnprocessable();

        $this
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_1->inventory->id,
                'item_id' => $this->items->where('points', 12)->first()->id,
                'qty' => 2,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_2->inventory->id,
                'item_id' => $this->items->where('points', 8)->first()->id,
                'qty' => 2,
            ])
            ->assertDatabaseCount('inventory_item', 2);
    }

    public function test_trade_with_survivor_infected()
    {
        $this->survivor_infected = Survivor::factory()->infected()->create();

        $this->setUpItemsWithSurvivorInfected();

        $uri = route('trade', [
            'survivor_1_id' => $this->survivor_1->id,
            'survivor_2_id' => $this->survivor_infected->id,
            'items_survivor_1' => [
                [
                    'id' => $this->items->where('points', 12)->first()->id,
                    'qty' => '2',
                ],
            ],
            'items_survivor_2' => [
                [
                    'id' => $this->items->where('points', 8)->first()->id,
                    'qty' => '3',
                ],
            ],
        ]);

        $this
            ->postJson($uri)
            ->assertForbidden();

        $this
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_1->inventory->id,
                'item_id' => $this->items->where('points', 12)->first()->id,
                'qty' => 2,
            ])
            ->assertDatabaseHas('inventory_item',[
                'inventory_id' => $this->survivor_infected->inventory->id,
                'item_id' => $this->items->where('points', 8)->first()->id,
                'qty' => 3,
            ]);
    }

    private function setUpItemsInSurvivors()
    {
        $this->survivor_1->inventory->items()->detach();
        $this->survivor_2->inventory->items()->detach();
        
        $this->survivor_1->inventory->items()->attach([
            $this->items->where('points', 14)->first()->id => ['qty' => 1],
            $this->items->where('points', 10)->first()->id => ['qty' => 1],
        ]);
        $this->survivor_2->inventory->items()->attach(
            $this->items->where('points', 8)->first()->id,
            ['qty' => 3]
        );
    }

    private function setUpOneDiferentItemForEachSurvivor()
    {
        $this->survivor_1->inventory->items()->detach();
        $this->survivor_2->inventory->items()->detach();
        
        $this->survivor_1->inventory->items()->attach(
            $this->items->where('points', 14)->first()->id,
            ['qty' => 1]);
        $this->survivor_2->inventory->items()->attach(
            $this->items->where('points', 8)->first()->id,
            ['qty' => 1]
        );
    }

    private function setUpInsuficientItemsForSurvivorOne()
    {
        $this->survivor_1->inventory->items()->detach();
        $this->survivor_2->inventory->items()->detach();
        
        $this->survivor_1->inventory->items()->attach(
            $this->items->where('points', 12)->first()->id,
            ['qty' => 1]);
        $this->survivor_2->inventory->items()->attach(
            $this->items->where('points', 8)->first()->id,
            ['qty' => 3]
        );
    }

    private function setUpInsuficientItemsForSurvivorTwo()
    {
        $this->survivor_1->inventory->items()->detach();
        $this->survivor_2->inventory->items()->detach();
        
        $this->survivor_1->inventory->items()->attach(
            $this->items->where('points', 12)->first()->id,
            ['qty' => 2]);
        $this->survivor_2->inventory->items()->attach(
            $this->items->where('points', 8)->first()->id,
            ['qty' => 2]
        );
    }

    private function setUpItemsWithSurvivorInfected()
    {
        $this->survivor_1->inventory->items()->detach();
        $this->survivor_infected->inventory->items()->detach();
        
        $this->survivor_1->inventory->items()->attach(
            $this->items->where('points', 12)->first()->id,
            ['qty' => 2]);
        $this->survivor_infected->inventory->items()->attach(
            $this->items->where('points', 8)->first()->id,
            ['qty' => 3]
        );
    }
}
