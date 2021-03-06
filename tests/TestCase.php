<?php

namespace Wingsline\Blog\Tests;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Csp\CspServiceProvider;
use Spatie\Feed\FeedServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Menu\Laravel\MenuServiceProvider;
use Spatie\ResponseCache\ResponseCacheServiceProvider;
use Wingsline\Blog\BlogServiceProvider;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/../database/factories');
        $this->app->instance('path.public', realpath(__DIR__.'/public'));
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set(
            'app.key',
            'base64:Uwel3ChfCJxTRmm/GnLlYeJOBexN6u3Wp2sfkgnzdkw='
        );
        config()->set('app.debug', true);
        config()->set('app.name', 'wingsline-blog');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        view()->getFinder()->prependLocation(__DIR__.'/sample-theme');

        // copy migration stubs from vendor packages

        // spatie/laravel-tags
        // copy(
        //     __DIR__ . '/../vendor/spatie/laravel-tags/database/migrations/create_tag_tables.php.stub',
        //     __DIR__ . '/vendor-migrations/0000_00_00_000000_create_tag_tables.php'
        // );
    }

    /**
     * Creates and logs in a sample user.
     *
     * @param mixed $attributes
     *
     * @return User
     */
    public function loginUser(
        $attributes = ['email' => 'foo@example.com', 'name' => 'Foo']
    ) {
        $attributes['password'] = bcrypt('secret');

        $user = new User();
        $user->unguard();
        $user->fill($attributes)->save();

        $this->be($user);

        return $user;
    }

    /**
     * Get application providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        $providers = array_merge(
            $app['config']['app.providers'],
            [
                TestingServiceProvider::class,
                BlogServiceProvider::class,
                FeedServiceProvider::class,
                MenuServiceProvider::class,
                ResponseCacheServiceProvider::class,
                CspServiceProvider::class,
                MediaLibraryServiceProvider::class,
            ]
        );

        return $providers;
    }
}
