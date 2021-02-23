<?php

/*
 * This file is part of Laravel OAuth 2.0.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server;

<<<<<<< HEAD
use DateInterval;
=======
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;
>>>>>>> 9d64db1b22cda7df7e7e71d154ed7b04fd40b0d8
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;

/**
 * This is the oauth2 server service provider class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class OAuth2ServerServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();

        $this->bootGuard();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'oauth2server');
    }

    /**
     * Setup the config.
     *
<<<<<<< HEAD
=======
     * @param \Illuminate\Contracts\Container\Container $app
     *
>>>>>>> 9d64db1b22cda7df7e7e71d154ed7b04fd40b0d8
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/oauth2.php');

        $this->publishes([$source => config_path('oauth2.php')]);

        $this->mergeConfigFrom($source, 'oauth2');
    }

    /**
     * Setup the migrations.
     *
<<<<<<< HEAD
=======
     * @param \Illuminate\Contracts\Container\Container $app
     *
>>>>>>> 9d64db1b22cda7df7e7e71d154ed7b04fd40b0d8
     * @return void
     */
    protected function setupMigrations()
    {
        $source = realpath(__DIR__.'/../database/migrations/');

        $this->publishes([$source => database_path('migrations')], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGrantTypes();
        $this->registerServer();
    }

<<<<<<< HEAD
    protected function registerServer()
    {
        $this->app->singleton(AuthorizationServer::class, function ($app) {
            $server = new AuthorizationServer(
                $app->make(ClientRepositoryInterface::class),
                $app->make(AccessTokenRepositoryInterface::class),
                $app->make(ScopeRepositoryInterface::class),
                new CryptKey($app['config']->get('oauth2.private_key_path'), $app['config']->get('oauth2.key_passphrase')),
                new CryptKey($app['config']->get('oauth2.public_key_path'), $app['config']->get('oauth2.key_passphrase')),
                $app->make($app['config']->get('oauth2.response_type')),
                $app->make($app['config']->get('oauth2.authorization_validator'))
            );

            foreach ($app['config']->get('oauth2.grant_types') as $grantType) {
                $server->enableGrantType(
                    $app->make($grantType['class'], $grantType),
                    new DateInterval('PT'.$grantType['access_token_ttl'].'S')
                );
=======
    /**
     * Register the Authorization server with the IoC container.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function registerAuthorizer(Application $app)
    {
        $app->singleton('oauth2-server.authorizer', function ($app) {
            $config = $app['config']->get('oauth2');
            $issuer = $app->make(AuthorizationServer::class)
                ->setClientStorage($app->make(ClientInterface::class))
                ->setSessionStorage($app->make(SessionInterface::class))
                ->setAuthCodeStorage($app->make(AuthCodeInterface::class))
                ->setAccessTokenStorage($app->make(AccessTokenInterface::class))
                ->setRefreshTokenStorage($app->make(RefreshTokenInterface::class))
                ->setScopeStorage($app->make(ScopeInterface::class))
                ->requireScopeParam($config['scope_param'])
                ->setDefaultScope($config['default_scope'])
                ->requireStateParam($config['state_param'])
                ->setScopeDelimiter($config['scope_delimiter'])
                ->setAccessTokenTTL($config['access_token_ttl']);

            // add the supported grant types to the authorization server
            foreach ($config['grant_types'] as $grantIdentifier => $grantParams) {
                $grant = $app->make($grantParams['class']);
                $grant->setAccessTokenTTL($grantParams['access_token_ttl']);

                if (array_key_exists('callback', $grantParams)) {
                    list($className, $method) = array_pad(explode('@', $grantParams['callback']), 2, 'verify');
                    $verifier = $app->make($className);
                    $grant->setVerifyCredentialsCallback([$verifier, $method]);
                }

                if (array_key_exists('auth_token_ttl', $grantParams)) {
                    $grant->setAuthTokenTTL($grantParams['auth_token_ttl']);
                }

                if (array_key_exists('refresh_token_ttl', $grantParams)) {
                    $grant->setRefreshTokenTTL($grantParams['refresh_token_ttl']);
                }

                if (array_key_exists('rotate_refresh_tokens', $grantParams)) {
                    $grant->setRefreshTokenRotation($grantParams['rotate_refresh_tokens']);
                }

                $issuer->addGrantType($grant, $grantIdentifier);
>>>>>>> 9d64db1b22cda7df7e7e71d154ed7b04fd40b0d8
            }

            return $server;
        });

        $this->app->singleton(ResourceServer::class, function ($app) {
            $server = new ResourceServer(
                $app->make(AccessTokenRepositoryInterface::class),
                new CryptKey($app['config']->get('oauth2.public_key_path'), $app['config']->get('oauth2.key_passphrase')),
                $app->make($app['config']->get('oauth2.authorization_validator'))
            );

            return $server;
        });
    }

<<<<<<< HEAD
    protected function registerGrantTypes()
=======
    /**
     * Register the Middleware to the IoC container because
     * some middleware need additional parameters.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function registerMiddlewareBindings(Application $app)
>>>>>>> 9d64db1b22cda7df7e7e71d154ed7b04fd40b0d8
    {
        $this->app->bind(AuthCodeGrant::class, function ($app, $parameters = []) {
            $grant = new AuthCodeGrant(
                $app->make(AuthCodeRepositoryInterface::class),
                $app->make(RefreshTokenRepositoryInterface::class),
                new DateInterval('PT'.$parameters['auth_code_ttl'].'S')
            );

            if (array_key_exists('code_exchange_proof', $parameters)) {
                if ($parameters['code_exchange_proof'] === true) {
                    $grant->enableCodeExchangeProof();
                }
            }

            return $grant;
        });

        $this->app->bind(ImplicitGrant::class, function ($app, $parameters = []) {
            return new ImplicitGrant(
                $app->make(UserRepositoryInterface::class)
            );
        });

        $this->app->bind(PasswordGrant::class, function ($app, $parameters = []) {
            return new PasswordGrant(
                $app->make(UserRepositoryInterface::class),
                $app->make(RefreshTokenRepositoryInterface::class)
            );
        });

        $this->app->bind(RefreshTokenGrant::class, function ($app, $parameters = []) {
            return new RefreshTokenGrant(
                $app->make(RefreshTokenRepositoryInterface::class)
            );
        });
    }

    protected function bootGuard()
    {
        $this->app['auth']->extend('oauth2', function ($app, $name, array $config) {
            $guard = new Guard(
                $app['auth']->createUserProvider($config['provider']),
                $app->make(ResourceServer::class),
                $app['request'],
                $app->make(ClientRepositoryInterface::class)
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
