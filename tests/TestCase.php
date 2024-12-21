<?php

namespace Dearpos\Customer\Tests;

use Dearpos\Customer\CustomerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Dearpos\\Customer\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CustomerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Run the migrations
        $migrations = [
            include __DIR__.'/../database/migrations/create_customer_groups_table.php.stub',
            include __DIR__.'/../database/migrations/create_customers_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_addresses_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_contacts_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_audits_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_credit_history_table.php.stub',
        ];

        foreach ($migrations as $migration) {
            $migration->up();
        }
    }

    protected function tearDown(): void
    {
        // Clean up the database after each test
        $migrations = [
            include __DIR__.'/../database/migrations/create_customer_groups_table.php.stub',
            include __DIR__.'/../database/migrations/create_customers_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_addresses_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_contacts_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_audits_table.php.stub',
            include __DIR__.'/../database/migrations/create_customer_credit_history_table.php.stub',
        ];

        foreach ($migrations as $migration) {
            $migration->down();
        }

        parent::tearDown();
    }

    protected function defineRoutes($router)
    {
        require __DIR__.'/../routes/api.php';
    }
}
