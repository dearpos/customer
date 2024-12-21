<?php

namespace Tests\Feature;

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerGroup;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = '/api/customers';
    }

    /** @test */
    public function can_list_customers()
    {
        Customer::factory()
            ->count(15)
            ->create();

        $response = $this->getJson($this->baseUrl . '?per_page=10');

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
    }

    /** @test */
    public function can_create_customer()
    {
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

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', fn (AssertableJson $json) =>
                    $json->where('code', 'CUST001')
                        ->where('name', 'John Doe')
                        ->has('addresses')
                        ->has('contacts')
                        ->etc()
                )
            );
    }

    /** @test */
    public function can_view_customer()
    {
        $customer = Customer::factory()
            ->has(CustomerGroup::factory(), 'group')
            ->create();

        $response = $this->getJson($this->baseUrl . '/' . $customer->id);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', fn (AssertableJson $json) =>
                    $json->where('id', $customer->id)
                        ->where('name', $customer->name)
                        ->has('group')
                        ->has('addresses')
                        ->has('contacts')
                        ->etc()
                )
            );
    }

    /** @test */
    public function can_update_customer()
    {
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

        $response = $this->putJson($this->baseUrl . '/' . $customer->id, $data);

        $response->assertOk()
            ->assertJsonPath('data.name', $data['name'])
            ->assertJsonPath('data.email', $data['email'])
            ->assertJsonPath('data.phone', $data['phone'])
            ->assertJsonPath('data.mobile', $data['mobile'])
            ->assertJsonPath('data.tax_number', $data['tax_number'])
            ->assertJsonPath('data.credit_limit', $data['credit_limit'])
            ->assertJsonPath('data.notes', $data['notes'])
            ->assertJsonPath('data.status', $data['status']);
    }

    /** @test */
    public function cannot_delete_customer_with_balance()
    {
        $customer = Customer::factory()
            ->state(['current_balance' => 1000000])
            ->create();

        $response = $this->deleteJson($this->baseUrl . '/' . $customer->id);

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'Cannot delete customer with outstanding balance.',
            ]);
    }

    /** @test */
    public function validation_rules()
    {
        $response = $this->postJson($this->baseUrl, []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'group_id',
                'code',
                'name',
            ]);
    }

    /** @test */
    public function unique_code_validation()
    {
        $existingCustomer = Customer::factory()->create(['code' => 'CUST001']);

        $data = [
            'group_id' => CustomerGroup::factory()->create()->id,
            'code' => 'CUST001',
            'name' => 'John Doe',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'code' => 'The code has already been taken.',
            ]);
    }
}
