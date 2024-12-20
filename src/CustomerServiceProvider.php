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
            ->hasMigration('create_customer_table')
            ->hasCommand(CustomerCommand::class);
    }
}
