<?php

namespace Dearpos\Customer\Http\Controllers;

use Dearpos\Customer\Http\Requests\Customer\StoreCustomerRequest;
use Dearpos\Customer\Http\Requests\Customer\UpdateCustomerRequest;
use Dearpos\Customer\Http\Resources\CustomerResource;
use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $customers = Customer::query()
            ->with(['group', 'addresses', 'contacts'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when($request->group_id, function ($query, $groupId) {
                $query->where('group_id', $groupId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): CustomerResource
    {
        $customer = DB::transaction(function () use ($request) {
            // Create customer
            $customer = Customer::create($request->safe()->except(['addresses', 'contacts']));

            // Create addresses
            foreach ($request->addresses as $address) {
                $customer->addresses()->create($address);
            }

            // Create contacts
            foreach ($request->contacts as $contact) {
                $customer->contacts()->create($contact);
            }

            // Create audit log if enabled
            if (config('customer.audit.enabled')) {
                CustomerAudit::create([
                    'auditable_type' => Customer::class,
                    'auditable_id' => $customer->id,
                    'event' => 'created',
                    'old_values' => null,
                    'new_values' => $customer->toArray(),
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            }

            return $customer;
        });

        $customer->load(['group', 'addresses', 'contacts']);

        return new CustomerResource($customer);
    }

    public function show(Customer $customer): CustomerResource
    {
        $customer->load(['group', 'addresses', 'contacts']);

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        DB::transaction(function () use ($request, $customer) {
            $oldValues = $customer->toArray();

            // Update customer
            $customer->update($request->safe()->except(['addresses', 'contacts']));

            // Update addresses
            if ($request->has('addresses')) {
                $customer->addresses()->delete();
                foreach ($request->addresses as $address) {
                    $customer->addresses()->create($address);
                }
            }

            // Update contacts
            if ($request->has('contacts')) {
                $customer->contacts()->delete();
                foreach ($request->contacts as $contact) {
                    $customer->contacts()->create($contact);
                }
            }

            // Create audit log if enabled
            if (config('customer.audit.enabled')) {
                CustomerAudit::create([
                    'auditable_type' => Customer::class,
                    'auditable_id' => $customer->id,
                    'event' => 'updated',
                    'old_values' => $oldValues,
                    'new_values' => $customer->toArray(),
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            }
        });

        $customer->load(['group', 'addresses', 'contacts']);

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        if ($customer->current_balance > 0) {
            return response()->json([
                'message' => 'Cannot delete customer with outstanding balance.',
            ], 422);
        }

        $oldValues = $customer->toArray();
        $customerId = $customer->id;

        DB::transaction(function () use ($customer, $oldValues, $customerId) {
            $customer->addresses()->delete();
            $customer->contacts()->delete();
            $customer->delete();

            // Create audit log if enabled
            if (config('customer.audit.enabled')) {
                CustomerAudit::create([
                    'auditable_type' => Customer::class,
                    'auditable_id' => $customerId,
                    'event' => 'deleted',
                    'old_values' => $oldValues,
                    'new_values' => null,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            }
        });

        return response()->json([
            'message' => 'Customer deleted successfully.',
        ]);
    }
}
