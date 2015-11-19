<?php
namespace Application\Credentials\GrantType;

use Application\Credentials\OAuth2AdapterAbstract;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\ResponseType\AccessTokenInterface;

/**
 * Class CredentialsAbstract
 *
 * @package Application\Credentials\GrantType
 */
abstract class CredentialsAbstract implements GrantTypeInterface
{
    /**
     * @var string
     */
    protected $_queryStringIdentifier;

    /**
     * @var \Application\Entity\Credentials
     */
    protected $_userInfo;

    /**
     * @var OAuth2AdapterAbstract
     */
    protected $_adapter;

    /**
     * @param OAuth2AdapterAbstract $adapter
     */
    public function __construct(OAuth2AdapterAbstract $adapter)
    {
        $this->_adapter = $adapter;
    }

    public function getQuerystringIdentifier()
    {
        return $this->_queryStringIdentifier;
    }

    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        if (!$this->_adapter->checkUserCredentials($request->request('identity'), $request->request('token'))) {
            $response->setError(401, 'invalid_grant', 'Invalid identity and token combination');
            return null;
        }

        $userInfo = $this->_adapter->getUserDetails($request->request('identity'));

        if (empty($userInfo)) {
            $response->setError(400, 'invalid_grant', 'Unable to retrieve user information');
            return null;
        }

        if (!isset($userInfo['user_id'])) {
            throw new \LogicException("You must set the user_id on the array returned by getUserDetails");
        }

        $this->_userInfo = $userInfo;

        return true;
    }

    public function getUserId()
    {
        return $this->_userInfo['user_id'];
    }

    public function getClientId()
    {
        return null;
    }

    public function getScope()
    {
        return null;
    }

    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }
}