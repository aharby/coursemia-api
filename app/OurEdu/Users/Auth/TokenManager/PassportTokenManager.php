<?php


namespace App\OurEdu\Users\Auth\TokenManager;

use App\OurEdu\Users\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;

class PassportTokenManager implements TokenManagerInterface
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * PassportTokenManager constructor.
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function createAuthToken(string $identifier): string
    {
        return $this->createUserToken($identifier, auth()->user());
    }

    /**
     * @param string $identifier
     * @param User $user
     * @return string
     */
    public function createUserToken(string $identifier, User $user): string
    {
        return $user->createToken($identifier)->accessToken;
    }

    /**
     *
     */
    public function revokeAuthAccessToken(): void
    {
        $token = Auth::user()->token();
        if ($token) {
            $this->revokeAccessToken($token->id);
        }
    }

    /**
     *
     */
    public function revokeAuthAllAccessTokens(): void
    {
        $this->revokeUserAccessTokenBy([], Auth::user());
    }

    /**
     * @param string $tokenIdentifier
     */
    public function revokeAccessToken(string $tokenIdentifier): void
    {
        $this->tokenRepository->revokeAccessToken($tokenIdentifier);
    }

    /**
     * @param array $attributes
     */
    public function revokeAccessTokenBy(array $attributes): void
    {
        if (!count($attributes)) {
            return;
        }

        $tokens = Passport::token()
            ->where('revoked', 0)
            ->where('expires_at', '>', Carbon::now());

        foreach ($attributes as $key => $value) {
            $tokens->where($key, $value);
        }

        $tokens = $tokens->get();

        foreach ($tokens as $token) {
            $this->revokeAccessToken($token->id);
        }
    }

    /**
     * @param array $attributes
     * @param User $user
     */
    public function revokeUserAccessTokenBy(array $attributes, User $user): void
    {
        $attributes = array_merge($attributes, ["user_id" => $user->id]);

        $this->revokeAccessTokenBy($attributes);
    }
}
