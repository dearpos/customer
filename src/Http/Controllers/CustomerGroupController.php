<?php

namespace Dearpos\Customer\Http\Controllers;

use Dearpos\Customer\Http\Requests\CustomerGroup\StoreCustomerGroupRequest;
use Dearpos\Customer\Http\Requests\CustomerGroup\UpdateCustomerGroupRequest;
use Dearpos\Customer\Http\Resources\CustomerGroupResource;
use Dearpos\Customer\Models\CustomerGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CustomerGroupController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $customerGroups = CustomerGroup::query()
            ->withCount('customers')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->boolean('active'), function ($query) {
                $query->where('is_active', true);
            })
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return CustomerGroupResource::collection($customerGroups);
    }

    public function store(StoreCustomerGroupRequest $request): CustomerGroupResource
    {
        $customerGroup = CustomerGroup::create($request->validated());

        return new CustomerGroupResource($customerGroup->loadCount('customers'));
    }

    public function show(CustomerGroup $customerGroup): CustomerGroupResource
    {
        return new CustomerGroupResource($customerGroup->loadCount('customers'));
    }

    public function update(UpdateCustomerGroupRequest $request, CustomerGroup $customerGroup): CustomerGroupResource
    {
        $customerGroup->update($request->validated());

        return new CustomerGroupResource($customerGroup->loadCount('customers'));
    }

    public function destroy(CustomerGroup $customerGroup): JsonResponse
    {
        if ($customerGroup->customers()->exists()) {
            return response()->json([
                'message' => 'Cannot delete customer group with existing customers.',
            ], 422);
        }

        $customerGroup->delete();

        return response()->json([
            'message' => 'Customer group deleted successfully.',
        ]);
    }
}
