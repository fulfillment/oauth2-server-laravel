<?php

/*
 * This file is part of Laravel OAuth 2.0.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Repositories;

use Illuminate\Auth\AuthManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

/**
 * This is the user repository class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var AuthManager
     */
    private $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     *
     * @return \League\OAuth2\Server\Entities\UserEntityInterface
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {

        // TODO: allow developer to choose which credentials key to use
        $credentials = [
            'email' => $username,
            'password' => $password,
        ];

        $user = $this->authManager->getProvider()->retrieveByCredentials($credentials);

        if (is_null($user)) {
            return;
        }

        // TODO: validate grant type and client for user

        return $this->authManager->getProvider()->validateCredentials($user, $credentials) ? $user : null;
    }
}
