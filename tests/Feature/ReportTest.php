<?php

namespace Tests\Feature;

use App\Models\Survivor;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $survivor_infected;
    protected $survivor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ItemSeeder::class);
    }

    public function test_resume_reports()
    {
        Survivor::factory()->infected()->create();

        $uri = route('get_resume_reports');
        $response = $this->getJson($uri);

        $response->assertSuccessful();
    }

    public function test_flag_survivor_as_infected()
    {
        $flag_survivor = Survivor::factory()->notInfected()->create();
        $survivor = Survivor::factory()->notInfected()->create();

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $flag_survivor->id,
        ]);
        $this
            ->postJson($uri)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'reported',
                'count' => 1,
                'infected' => false
            ]);
    }

    public function test_flag_survivor_as_infected_and_become_infected()
    {
        $survivor = Survivor::factory()->notInfected()->create();
        $flag_survivor = Survivor::factory()->notInfected()->create();

        $this->setUpSurvivorWithFourReports($flag_survivor);

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $flag_survivor->id,
        ]);
        $this
            ->postJson($uri)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'reported',
                'count' => 5,
                'infected' => true
            ]);
    }

    public function test_report_survivor_as_infected_when_survivor_is_already_infected()
    {
        $survivor_infected = Survivor::factory()->infected()->create();
        $survivor = Survivor::factory()->notInfected()->create();

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $survivor_infected->id,
        ]);
        $this
            ->postJson($uri)
            ->assertSuccessful()
            ->assertJsonFragment(['message' => 'was infected']);
    }

    public function test_report_a_survivor_as_infected_when_reporter_already_make_report()
    {
        $survivor_infected = Survivor::factory()->infected()->create();
        $survivor = Survivor::first();

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $survivor_infected->id,
        ]);
        $this
            ->postJson($uri)
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'You can not do this!']);
    }

    public function test_report_a_survivor_as_infected_when_reporter_is_infected()
    {
        $survivor_infected = Survivor::factory()->infected()->create();
        $survivor = Survivor::first();

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor_infected->id,
            'flag_survivor_id' => $survivor->id,
        ]);
        $this
            ->postJson($uri)
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'You can not do this!']);
    }

    public function test_report_yourself()
    {
        $survivor = Survivor::factory()->notInfected()->create();

        $uri = route('flag_survivor_infected', [
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $survivor->id,
        ]);
        $this
            ->postJson($uri)
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'You can not do this!']);
    }

    private function setUpSurvivorWithFourReports($flag_survivor)
    {
        $survivors = Survivor::factory()->notInfected()->count(4)->create();

        foreach($survivors as $survivor)
        {
            $survivor->reports()->create([
                'survivor_id' => $survivor->id,
                'flag_survivor_id' => $flag_survivor->id,
            ]);
        }
    }
}
