<?php

namespace Dearpos\Customer\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creditLimit = config('customer.credit_limit');
        $addressTypes = array_keys(config('customer.address.types'));
        $statuses = array_keys(config('customer.status'));

        return [
            'group_id' => ['required', 'uuid', 'exists:customer_groups,id'],
            'code' => ['required', 'string', 'max:50', 'unique:customers,code'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['required', 'numeric', 'min:' . $creditLimit['min'], 'max:' . $creditLimit['max']],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in($statuses)],

            // Address
            'addresses' => ['required', 'array', 'min:' . config('customer.address.min_addresses')],
            'addresses.*.address_type' => ['required', Rule::in($addressTypes)],
            'addresses.*.address_line_1' => ['required', 'string', 'max:255'],
            'addresses.*.address_line_2' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['required', 'string', 'max:100'],
            'addresses.*.state' => ['required', 'string', 'max:100'],
            'addresses.*.postal_code' => ['required', 'string', 'max:20'],
            'addresses.*.country' => ['required', 'string', 'max:100'],
            'addresses.*.is_default' => ['boolean'],

            // Contact
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.name' => ['required', 'string', 'max:100'],
            'contacts.*.position' => ['nullable', 'string', 'max:100'],
            'contacts.*.phone' => config('customer.contact.require_phone_or_mobile') ? ['required_without:contacts.*.mobile', 'nullable', 'string', 'max:20'] : ['nullable', 'string', 'max:20'],
            'contacts.*.mobile' => config('customer.contact.require_phone_or_mobile') ? ['required_without:contacts.*.phone', 'nullable', 'string', 'max:20'] : ['nullable', 'string', 'max:20'],
            'contacts.*.email' => config('customer.contact.validate_email') ? ['nullable', 'email:rfc,dns', 'max:100'] : ['nullable', 'string', 'max:100'],
            'contacts.*.is_primary' => ['boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                // Validate at least one default address if required
                if (config('customer.address.require_default') && !collect($this->input('addresses'))->contains('is_default', true)) {
                    $validator->errors()->add('addresses', 'At least one address must be set as default.');
                }

                // Validate at least one primary contact if required
                if (config('customer.contact.require_primary') && !collect($this->input('contacts'))->contains('is_primary', true)) {
                    $validator->errors()->add('contacts', 'At least one contact must be set as primary.');
                }
            }
        ];
    }
}
