<?php

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerGroup;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->baseUrl = '/api/v1/customers';
    $this->group = CustomerGroup::factory()->create();
});

test('validates required fields when creating customer', function () {
    // Act
    $response = postJson($this->baseUrl, []);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'group_id',
            'code',
            'name',
            'credit_limit',
            'status',
            'addresses',
            'contacts',
        ]);
});

test('validates email format', function () {
    // Arrange
    $data = [
        'group_id' => $this->group->id,
        'code' => 'CUST001',
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'credit_limit' => 5000000,
        'status' => 'active',
        'addresses' => [
            [
                'address_type' => 'billing',
                'address_line_1' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
                'is_default' => true,
            ],
        ],
        'contacts' => [
            [
                'name' => 'Test Contact',
                'phone' => '08123456789',
                'is_primary' => true,
            ],
        ],
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('validates unique code', function () {
    // Arrange
    $existingCustomer = Customer::factory()->create();
    $data = [
        'group_id' => $this->group->id,
        'code' => $existingCustomer->code,
        'name' => 'John Doe',
        'credit_limit' => 5000000,
        'status' => 'active',
        'addresses' => [
            [
                'address_type' => 'billing',
                'address_line_1' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
                'is_default' => true,
            ],
        ],
        'contacts' => [
            [
                'name' => 'Test Contact',
                'phone' => '08123456789',
                'is_primary' => true,
            ],
        ],
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

test('validates credit limit range', function () {
    // Arrange
    $data = [
        'group_id' => $this->group->id,
        'code' => 'CUST001',
        'name' => 'John Doe',
        'credit_limit' => -1000,
        'status' => 'active',
        'addresses' => [
            [
                'address_type' => 'billing',
                'address_line_1' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
                'is_default' => true,
            ],
        ],
        'contacts' => [
            [
                'name' => 'Test Contact',
                'phone' => '08123456789',
                'is_primary' => true,
            ],
        ],
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['credit_limit']);
});

test('validates status values', function () {
    // Arrange
    $data = [
        'group_id' => $this->group->id,
        'code' => 'CUST001',
        'name' => 'John Doe',
        'credit_limit' => 5000000,
        'status' => 'invalid-status',
        'addresses' => [
            [
                'address_type' => 'billing',
                'address_line_1' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
                'is_default' => true,
            ],
        ],
        'contacts' => [
            [
                'name' => 'Test Contact',
                'phone' => '08123456789',
                'is_primary' => true,
            ],
        ],
    ];

    // Act
    $response = postJson($this->baseUrl, $data);

    // Assert
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});
