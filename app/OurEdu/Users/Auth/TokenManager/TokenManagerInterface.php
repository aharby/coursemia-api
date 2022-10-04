<?php


namespace App\OurEdu\Users\Auth\TokenManager;

use App\OurEdu\Users\User;

interface TokenManagerInterface
{
    /**
     * @param string $identifier
     * @return string
     */
    public function createAuthToken(string $identifier): string;

    /**
     * @param string $identifier
     * @param User $user
     * @return string
     */
    public function createUserToken(string $identifier, User $user): string;

    /**
     *
     */
    public function revokeAuthAccessToken(): void;

    /**
     *
     */
    public function revokeAuthAllAccessTokens(): void;

    /**
     * @param string $tokenIdentifier
     */
    public function revokeAccessToken(string $tokenIdentifier): void;

    /**
     * @param array $attributes
     */
    public function revokeAccessTokenBy(array $attributes): void;

    /**
     * @param array $attributes
     * @param User $user
     */
    public function revokeUserAccessTokenBy(array $attributes, User $user): void;
}
