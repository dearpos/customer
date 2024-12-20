<?php

namespace Dearpos\Customer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Dearpos\Customer\Commands\CustomerCommand;

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
            ->hasMigration('create_customer_table')
            ->hasCommand(CustomerCommand::class);
    }
}
