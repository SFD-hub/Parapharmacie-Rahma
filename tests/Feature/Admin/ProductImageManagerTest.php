<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProductImageManager;
use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImageManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_admin_can_upload_a_product_image(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin');

        Livewire::test(ProductImageManager::class, ['product' => $product])
            ->set('newImages', [UploadedFile::fake()->image('photo.jpg')])
            ->assertHasNoErrors();

        $this->assertDatabaseCount('product_images', 1);
        $this->assertDatabaseHas('product_images', ['product_id' => $product->id, 'is_primary' => true]);
    }

    public function test_only_the_first_uploaded_image_becomes_primary(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin');

        $component = Livewire::test(ProductImageManager::class, ['product' => $product]);
        $component->set('newImages', [UploadedFile::fake()->image('one.jpg')]);
        $component->set('newImages', [UploadedFile::fake()->image('two.jpg')]);

        $this->assertDatabaseCount('product_images', 2);
        $this->assertSame(1, $product->images()->where('is_primary', true)->count());
    }

    public function test_admin_can_set_a_different_image_as_primary(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $first = $product->images()->create(['path' => 'a.jpg', 'is_primary' => true, 'position' => 1]);
        $second = $product->images()->create(['path' => 'b.jpg', 'is_primary' => false, 'position' => 2]);

        $this->actingAs($admin, 'admin');

        Livewire::test(ProductImageManager::class, ['product' => $product])
            ->call('setPrimary', $second->id);

        $this->assertTrue($second->fresh()->is_primary);
        $this->assertFalse($first->fresh()->is_primary);
    }

    public function test_admin_can_reorder_images(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $first = $product->images()->create(['path' => 'a.jpg', 'is_primary' => true, 'position' => 1]);
        $second = $product->images()->create(['path' => 'b.jpg', 'is_primary' => false, 'position' => 2]);

        $this->actingAs($admin, 'admin');

        Livewire::test(ProductImageManager::class, ['product' => $product])
            ->call('moveDown', $first->id);

        $this->assertSame(2, $first->fresh()->position);
        $this->assertSame(1, $second->fresh()->position);
    }

    public function test_deleting_the_primary_image_promotes_the_next_one(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $first = $product->images()->create(['path' => 'a.jpg', 'is_primary' => true, 'position' => 1]);
        $second = $product->images()->create(['path' => 'b.jpg', 'is_primary' => false, 'position' => 2]);

        $this->actingAs($admin, 'admin');

        Livewire::test(ProductImageManager::class, ['product' => $product])
            ->call('deleteImage', $first->id);

        $this->assertDatabaseMissing('product_images', ['id' => $first->id]);
        $this->assertTrue($second->fresh()->is_primary);
    }
}
