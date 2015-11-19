<?php
namespace Application\Credentials\Adapter;

use Application\Credentials\CredentialsAdapterAbstract;
use Application\Credentials\Exception\InvalidIdentityException;
use Application\Entity\Credentials;
use Application\Enum\CredentialsStatus;
use Application\Enum\CredentialsType;

/**
 * Class EmailAdapter
 *
 * @package Application\Credentials\Adapter
 */
class EmailAdapter extends CredentialsAdapterAbstract
{
    /**
     * @var string
     */
    protected $_credentialType = CredentialsType::EMAIL;

    /**
     * @param  string $identity Identifier of the user
     * @param  string $token    Token to authenticate the user
     * @param  array  $data     (Optional)
     * @return \Application\Entity\User
     */
    public function signup($identity, $token, array $data = array())
    {
        // first check existing credentials
        /** @var \Application\Entity\Credentials $credentials */
        $credentials = $this->_getCredentials($identity);
        if (null !== $credentials) {
            throw new InvalidIdentityException('Email address already exist');
        }

        $credentials = new Credentials;
        $credentials->setType($this->_credentialType)
            ->setIdentity($identity);

        // Encrypt token
        $this->updateToken($credentials, $token);

        // create or update user
        $user = $this->_findUserByEmailOrCreateNew($identity);
        $user->addCredential($credentials)
            ->setEmail($identity);

        $this->_updateUser($user, $data);

        return $user;
    }

    /**
     * @param  Credentials $credentials
     * @param  string      $token
     * @return void
     */
    public function updateToken(Credentials $credentials, $token)
    {
        $token = $this->getServiceLocator()
            ->get('ZF\OAuth2\Adapter\PdoAdapter')
            ->getBcrypt()
            ->create($token);

        $credentials->setToken($token);
    }

    /**
     * @param  string $identity
     * @return \Application\Entity\Credentials
     */
    protected function _getCredentials($identity)
    {
        $credentials = parent::_getCredentials($identity);
        if (null === $credentials || CredentialsStatus::NOT_CONFIRMED == $credentials->getStatus()) {
            return null;
        }

        return $credentials;
    }

    /**
     * @param  string $token
     * @param  string $hash
     * @return boolean
     */
    protected function _verifyToken($token, $hash)
    {
        return $token === $hash
            || $this->getBcrypt()->verify($token, $hash);
    }
}