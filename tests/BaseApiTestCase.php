<?php
namespace App;

use App\Models\User\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\TestResponse;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use UserTableSeeder;

abstract class BaseApiTestCase extends BaseFunctionalTestCase
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_POST_NO_JSON = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    // The access token to use for authenticated tests
    private $accessToken;

    /**
     * The scope to use when generating an access token. Override when you need
     * to access a different token scope.
     *
     * @var string
     */
    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER,
    ];

    /**
     * The user id to use when creating a new token scope.
     *
     * @var integer
     */
    protected static $tokenUser = UserTableSeeder::ACTIVE_USER_CID;

    /**
     * Setup Function
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->regenerateAccessToken(static::$tokenScope, static::$tokenUser);
    }

    /**
     * Regenerate the tests access token under a given user id
     *
     * @param array $tokenScope
     * @param integer $userId
     * @return void
     */
    protected function regenerateAccessToken(array $tokenScope = [], int $userId = 0)
    {
        $this->accessToken = User::findOrFail($userId)->createToken('access', $tokenScope)->accessToken;
    }

    /**
     * Makes an authenticated request to the API and returns the
     * utils object so that assertions may be made.
     *
     * @param  string $method HTTP verb to use
     * @param  string $route API route to use
     * @param  array $data Array to pass as JSON
     * @return TestResponse
     */
    protected function makeAuthenticatedApiRequest(string $method, string $route, array $data = [])
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json'
        ];

        return $this->makeApiRequest($method, $route, $headers, $data);
    }

    /**
     * Makes an authenticated request to the API on a github webhook and returns the
     * utils object so that assertions may be made.
     *
     * @param  string $method HTTP verb to use
     * @param  string $route API route to use
     * @param  array $data Array to pass as JSON
     * @return TestResponse
     */
    protected function makeAuthenticatedApiGithubRequest(string $route, array $data)
    {
        $headers = [
            'X-Hub-Signature' => 'sha1=' . hash_hmac('sha1', json_encode($data), config('github.secret')),
            'Accept' => 'application/json'
        ];

        return $this->makeApiRequest('POST', $route, $headers, $data);
    }

    /**
     * Makes an unauthenticated request to the API and returns the
     * utils object so that assertions may be made.
     *
     * @param string $method HTTP verb to use
     * @param string $route API route to use
     * @param array $data Array to pass as JSON
     * @param array $query
     * @return TestResponse
     */
    protected function makeUnauthenticatedApiRequest(string $method, string $route, array $data = [], array $query = [])
    {
        $headers = [
            'Accept' => 'application/json'
        ];

        if (count($query)) {
            $route .= '?';

            foreach ($query as $key => $value) {
                $route .= sprintf('%s=%s&', $key, $value);
            }

            $route = rtrim($route, '&');
        }

        return $this->makeApiRequest($method, $route, $headers, $data);
    }

    /**
     * @param string $method
     * @param string $route
     * @param array $headers
     * @param array $data
     * @return TestResponse
     */
    private function makeApiRequest(string $method, string $route, array $headers = [], array $data = [])
    {
        switch ($method) {
            case self::METHOD_GET:
                return $this->get($route, $headers);
            case self::METHOD_POST:
                return $this->json(
                    self::METHOD_POST,
                    $route,
                    $data,
                    $headers
                );
            case self::METHOD_POST_NO_JSON:
                return $this->post($route, $data, $headers);
            case self::METHOD_PATCH:
                return $this->patch($route, $data, $headers);
            case self::METHOD_PUT:
                return $this->json(
                    self::METHOD_PUT,
                    $route,
                    $data,
                    $headers
                );
            case self::METHOD_DELETE:
                return $this->delete($route, $data, $headers);
            default:
                throw new InvalidArgumentException('Invalid HTTP Verb');
        }
    }
}
