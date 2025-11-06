<?php

namespace Tests\Unit\Models;

use App\Models\Manga;
use App\Models\Tome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TomeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_tome(): void
    {
        $manga = Manga::factory()->create();
        $tome = Tome::factory()->create([
            'manga_id' => $manga->id,
            'numero' => 1,
        ]);

        $this->assertDatabaseHas('tomes', [
            'manga_id' => $manga->id,
            'numero' => 1,
        ]);
    }

    #[Test]
    public function it_belongs_to_a_manga(): void
    {
        $manga = Manga::factory()->create();
        $tome = Tome::factory()->create(['manga_id' => $manga->id]);

        $this->assertInstanceOf(Manga::class, $tome->manga);
        $this->assertEquals($manga->id, $tome->manga->id);
    }

    #[Test]
    public function it_casts_possede_to_boolean(): void
    {
        $tome = Tome::factory()->create(['possede' => 1]);

        $this->assertIsBool($tome->possede);
        $this->assertTrue($tome->possede);
    }

    #[Test]
    public function it_casts_date_achat_to_date(): void
    {
        $tome = Tome::factory()->create([
            'date_achat' => '2024-01-15',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $tome->date_achat);
        $this->assertEquals('2024-01-15', $tome->date_achat->format('Y-m-d'));
    }

    #[Test]
    public function date_achat_can_be_null(): void
    {
        $tome = Tome::factory()->create(['date_achat' => null]);

        $this->assertNull($tome->date_achat);
    }

    #[Test]
    public function fillable_attributes_are_mass_assignable(): void
    {
        $manga = Manga::factory()->create();
        $data = [
            'manga_id' => $manga->id,
            'numero' => 5,
            'possede' => true,
            'date_achat' => '2024-01-15',
        ];

        $tome = Tome::create($data);

        $this->assertEquals(5, $tome->numero);
        $this->assertTrue($tome->possede);
        $this->assertEquals('2024-01-15', $tome->date_achat->format('Y-m-d'));
    }

    #[Test]
    public function a_manga_can_have_multiple_tomes(): void
    {
        $manga = Manga::factory()->create();
        
        Tome::factory()->count(3)->create(['manga_id' => $manga->id]);

        $this->assertCount(3, $manga->fresh()->tomes);
    }

    #[Test]
    public function tome_possede_status_can_be_updated(): void
    {
        $tome = Tome::factory()->create(['possede' => false]);

        $tome->update(['possede' => true]);

        $this->assertTrue($tome->fresh()->possede);
    }

    #[Test]
    public function tome_numero_can_be_any_integer(): void
    {
        $manga = Manga::factory()->create();
        
        $tome1 = Tome::factory()->create(['manga_id' => $manga->id, 'numero' => 1]);
        $tome100 = Tome::factory()->create(['manga_id' => $manga->id, 'numero' => 100]);

        $this->assertEquals(1, $tome1->numero);
        $this->assertEquals(100, $tome100->numero);
    }
}
