<?php

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerGroup;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->baseUrl = '/api/customer-groups';
});

test('can list customer groups with pagination', function () {
    // Arrange
    CustomerGroup::factory()->count(15)->create();

    // Act
    $response = getJson($this->baseUrl.'?per_page=10');

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'discount_percentage',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'customers_count',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(10, 'data');
});

test('can create customer group', function () {
    // Arrange
    $data = [
        'name' => 'Premium',
        'description' => 'Premium customers with special benefits',
        'discount_percentage' => 15.00,
        'is_active' => true,
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'discount_percentage',
                'is_active',
                'created_at',
                'updated_at',
                'customers_count',
            ],
        ])
        ->assertJsonPath('data.name', $data['name'])
        ->assertJsonPath('data.description', $data['description'])
        ->assertJsonPath('data.discount_percentage', $data['discount_percentage'])
        ->assertJsonPath('data.is_active', $data['is_active'])
        ->assertJsonPath('data.customers_count', 0);
});

test('can show customer group', function () {
    // Arrange
    $group = CustomerGroup::factory()->create();

    // Act
    $response = getJson($this->baseUrl.'/'.$group->id);

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'discount_percentage',
                'is_active',
                'created_at',
                'updated_at',
                'customers_count',
            ],
        ])
        ->assertJsonPath('data.id', $group->id)
        ->assertJsonPath('data.name', $group->name);
});

test('can update customer group', function () {
    // Arrange
    $group = CustomerGroup::factory()->create();
    $data = [
        'name' => 'Updated Premium',
        'description' => 'Updated description',
        'discount_percentage' => 20.00,
        'is_active' => false,
    ];

    // Act
    $response = putJson($this->baseUrl.'/'.$group->id, $data);

    // Assert
    $response->assertOk()
        ->assertJsonPath('data.name', $data['name'])
        ->assertJsonPath('data.description', $data['description'])
        ->assertJsonPath('data.discount_percentage', $data['discount_percentage'])
        ->assertJsonPath('data.is_active', $data['is_active']);
});

test('cannot delete customer group with existing customers', function () {
    // Arrange
    $group = CustomerGroup::factory()
        ->has(Customer::factory())
        ->create();

    // Act
    $response = deleteJson($this->baseUrl.'/'.$group->id);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete customer group with existing customers.');
});

test('can delete customer group without customers', function () {
    // Arrange
    $group = CustomerGroup::factory()->create();

    // Act
    $response = deleteJson($this->baseUrl.'/'.$group->id);

    // Assert
    $response->assertOk()
        ->assertJsonPath('message', 'Customer group deleted successfully.');

    $this->assertModelMissing($group);
});
