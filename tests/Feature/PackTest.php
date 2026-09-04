<?php

namespace Tests\Feature;

use App\Models\Pack;
use App\Models\PackItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_only_lists_available_packs(): void
    {
        $available = Pack::factory()->create(['name' => 'Pack visible', 'is_active' => true]);
        $inactive = Pack::factory()->create(['name' => 'Pack masqué', 'is_active' => false]);

        $response = $this->get(route('packs.index'));

        $response->assertOk()->assertSee('Pack visible')->assertDontSee('Pack masqué');
    }

    public function test_guest_can_view_a_pack_detail_page(): void
    {
        $product = Product::factory()->create(['price' => 5000, 'sale_price' => null]);
        $pack = Pack::factory()->create(['name' => 'Pack complet', 'sale_price' => 4000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->get(route('packs.show', $pack));

        $response->assertOk()->assertSee('Pack complet')->assertSee($product->name);
    }

    public function test_inactive_pack_detail_returns_404(): void
    {
        $pack = Pack::factory()->create(['is_active' => false]);

        $this->get(route('packs.show', $pack))->assertNotFound();
    }

    public function test_guest_can_add_a_pack_to_the_cart(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $pack = Pack::factory()->create();
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->post(route('packs.addToCart', $pack));

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'pack_id' => $pack->id]);
    }
}
