<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_a_product_with_a_null_name_keeps_the_existing_name(): void
    {
        $product = Product::create([
            'name' => 'Original Product',
            'description' => 'Original description',
            'price' => 100,
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => null,
            'price' => 250,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Original Product');
        $response->assertJsonPath('data.price', 250);
    }
}
