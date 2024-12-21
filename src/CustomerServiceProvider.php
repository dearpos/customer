<?php

namespace Dearpos\Customer;

use Dearpos\Customer\Commands\CustomerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CustomerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('customer')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_customer_groups_table',
                'create_customers_table',
                'create_customer_addresses_table',
                'create_customer_contacts_table',
                'create_customer_audits_table',
                'create_customer_credit_history_table'
            ])
            ->hasCommand(CustomerCommand::class);
    }
}
