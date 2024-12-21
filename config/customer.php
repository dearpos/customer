<?php

// config for Dearpos/Customer
return [

    /*
    |--------------------------------------------------------------------------
    | Default Customer Group
    |--------------------------------------------------------------------------
    |
    | This option controls the default customer group that will be used when
    | creating a new customer. The value should be the name of the group.
    |
    */
    'default_group' => 'Regular',

    /*
    |--------------------------------------------------------------------------
    | Credit Limit Settings
    |--------------------------------------------------------------------------
    |
    | These options control the default credit limit settings for customers.
    |
    */
    'credit_limit' => [
        'default' => 0,
        'min' => 0,
        'max' => 1000000000, // 1 billion
    ],

    /*
    |--------------------------------------------------------------------------
    | Address Settings
    |--------------------------------------------------------------------------
    |
    | These options control the address settings for customers.
    |
    */
    'address' => [
        'types' => [
            'billing' => 'Billing',
            'shipping' => 'Shipping',
            'both' => 'Billing & Shipping',
        ],
        'require_default' => true,
        'min_addresses' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Settings
    |--------------------------------------------------------------------------
    |
    | These options control the contact settings for customers.
    |
    */
    'contact' => [
        'require_primary' => true,
        'require_phone_or_mobile' => true,
        'validate_email' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Settings
    |--------------------------------------------------------------------------
    |
    | These options define the available customer statuses and their labels.
    |
    */
    'status' => [
        'active' => [
            'label' => 'Active',
            'description' => 'Customer is active and can make transactions',
        ],
        'inactive' => [
            'label' => 'Inactive',
            'description' => 'Customer is temporarily inactive',
        ],
        'blocked' => [
            'label' => 'Blocked',
            'description' => 'Customer is blocked from making transactions',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Settings
    |--------------------------------------------------------------------------
    |
    | These options control the audit logging settings.
    |
    */
    'audit' => [
        'enabled' => true,
        'events' => [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'status_changed' => 'Status Changed',
            'credit_changed' => 'Credit Changed',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit History Settings
    |--------------------------------------------------------------------------
    |
    | These options control the credit history settings.
    |
    */
    'credit_history' => [
        'transaction_types' => [
            'increase' => 'Increase',
            'decrease' => 'Decrease',
            'adjustment' => 'Adjustment',
        ],
        'reference_types' => [
            'sales_order' => 'Sales Order',
            'payment' => 'Payment',
            'credit_note' => 'Credit Note',
            'manual' => 'Manual',
        ],
        'require_notes_for_adjustment' => true,
    ],
];
