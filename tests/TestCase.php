<?php

namespace RedJasmine\Order\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\Concerns\WithWorkbench;
use function Orchestra\Testbench\artisan;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config) {
            $config->set('app.faker_locale', 'zh_CN');
            $config->set('database.default', 'mysql');


            // // Setup queue database connections.
            // $config([
            //             'queue.batching.database' => 'testbench',
            //             'queue.failed.database' => 'testbench',
            //         ]);
        });
    }


    /**
     * Get the application timezone.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'Asia/Shanghai';
    }

    /**
     * Automatically enables package discoveries.
     *
     * @var bool
     */
    protected $enablesPackageDiscoveries = true;
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            'RedJasmine\Order\OrderDomainServiceProvider',
        ];
    }

    protected function defineDatabaseMigrations()
    {
        // artisan($this, 'migrate', ['--database' => 'testbench']);
        //
        // $this->beforeApplicationDestroyed(
        //     fn () => artisan($this, 'migrate:rollback', ['--database' => 'testbench'])
        // );
    }

}
