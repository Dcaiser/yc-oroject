<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_supplier_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/suppliers');

        $response->assertStatus(200);
        $response->assertViewIs('supplier.index');
    }

    public function test_supplier_create_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/suppliers/create');

        $response->assertStatus(200);
        $response->assertViewIs('supplier.create');
    }

    public function test_supplier_can_be_created(): void
    {
        $supplierData = [
            'supplier_code' => 'SUP001',
            'name' => 'Test Supplier',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'test@supplier.com',
            'address' => 'Test Address',
            'npwp' => '12.345.678.9-123.000',
        ];

        $response = $this->actingAs($this->user)->post('/suppliers', $supplierData);

        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', $supplierData);
    }

    public function test_supplier_requires_supplier_code(): void
    {
        $supplierData = [
            'name' => 'Test Supplier',
        ];

        $response = $this->actingAs($this->user)->post('/suppliers', $supplierData);

        $response->assertSessionHasErrors(['supplier_code']);
    }

    public function test_supplier_requires_name(): void
    {
        $supplierData = [
            'supplier_code' => 'SUP001',
        ];

        $response = $this->actingAs($this->user)->post('/suppliers', $supplierData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_supplier_code_must_be_unique(): void
    {
        // Create first supplier
        Supplier::create([
            'supplier_code' => 'SUP001',
            'name' => 'First Supplier',
        ]);

        // Try to create second supplier with same code
        $supplierData = [
            'supplier_code' => 'SUP001',
            'name' => 'Second Supplier',
        ];

        $response = $this->actingAs($this->user)->post('/suppliers', $supplierData);

        $response->assertSessionHasErrors(['supplier_code']);
    }
}
