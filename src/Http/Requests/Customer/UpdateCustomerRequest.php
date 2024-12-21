<?php

namespace Dearpos\Customer\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creditLimit = config('customer.credit_limit');
        $statuses = array_keys(config('customer.status'));

        return [
            'group_id' => ['required', 'uuid', 'exists:customer_groups,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('customers')->ignore($this->customer)],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('customers')->ignore($this->customer)],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['required', 'numeric', 'min:'.$creditLimit['min'], 'max:'.$creditLimit['max']],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in($statuses)],
        ];
    }
}
