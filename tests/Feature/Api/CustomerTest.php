<?php

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerGroup;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->baseUrl = '/api/customers';
});

test('can list customers with pagination', function () {
    // Arrange
    Customer::factory()
        ->count(15)
        ->create();

    // Act
    $response = getJson($this->baseUrl . '?per_page=10');

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'group',
                    'code',
                    'name',
                    'email',
                    'phone',
                    'mobile',
                    'tax_number',
                    'credit_limit',
                    'current_balance',
                    'notes',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(10, 'data');
});

test('can create customer with addresses and contacts', function () {
    // Arrange
    $group = CustomerGroup::factory()->create();
    $data = [
        'group_id' => $group->id,
        'code' => 'CUST001',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '021-5555555',
        'mobile' => '08123456789',
        'tax_number' => '123456789',
        'credit_limit' => 5000000,
        'notes' => 'Test notes',
        'status' => 'active',
        'addresses' => [
            [
                'address_type' => 'billing',
                'address_line_1' => 'Jl. Test No. 1',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12345',
                'country' => 'Indonesia',
                'is_default' => true,
            ],
        ],
        'contacts' => [
            [
                'name' => 'Jane Doe',
                'position' => 'Manager',
                'phone' => '021-5555556',
                'mobile' => '08123456780',
                'email' => 'jane@example.com',
                'is_primary' => true,
            ],
        ],
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'group',
                'code',
                'name',
                'addresses',
                'contacts',
            ],
        ]);
});

test('can show customer with relationships', function () {
    // Arrange
    $customer = Customer::factory()
        ->has(CustomerGroup::factory(), 'group')
        ->create();

    // Act
    $response = getJson($this->baseUrl . '/' . $customer->id);

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'group',
                'code',
                'name',
                'email',
                'phone',
                'mobile',
                'tax_number',
                'credit_limit',
                'current_balance',
                'notes',
                'status',
                'created_at',
                'updated_at',
                'addresses',
                'contacts',
            ],
        ]);
});

test('can update customer', function () {
    // Arrange
    $customer = Customer::factory()
        ->has(CustomerGroup::factory(), 'group')
        ->create();

    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '021-5555557',
        'mobile' => '08123456781',
        'tax_number' => '987654321',
        'credit_limit' => 10000000,
        'notes' => 'Updated notes',
        'status' => 'inactive',
    ];

    // Act
    $response = putJson($this->baseUrl . '/' . $customer->id, $data);

    // Assert
    $response->assertOk()
        ->assertJsonPath('data.name', $data['name'])
        ->assertJsonPath('data.email', $data['email'])
        ->assertJsonPath('data.phone', $data['phone'])
        ->assertJsonPath('data.mobile', $data['mobile'])
        ->assertJsonPath('data.tax_number', $data['tax_number'])
        ->assertJsonPath('data.credit_limit', $data['credit_limit'])
        ->assertJsonPath('data.notes', $data['notes'])
        ->assertJsonPath('data.status', $data['status']);
});

test('can delete customer without balance', function () {
    // Arrange
    $customer = Customer::factory()
        ->state(['current_balance' => 0])
        ->create();

    // Act
    $response = deleteJson($this->baseUrl . '/' . $customer->id);

    // Assert
    $response->assertOk()
        ->assertJsonPath('message', 'Customer deleted successfully.');

    $this->assertSoftDeleted($customer);
});

test('cannot delete customer with balance', function () {
    // Arrange
    $customer = Customer::factory()
        ->state(['current_balance' => 1000000])
        ->create();

    // Act
    $response = deleteJson($this->baseUrl . '/' . $customer->id);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete customer with outstanding balance.');
});
