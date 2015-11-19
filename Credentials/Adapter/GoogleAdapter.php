<?php
namespace Application\Credentials\Adapter;

use Application\Credentials\CredentialsAdapterAbstract;
use Application\Credentials\Exception\InvalidTokenException;
use Application\Entity\Credentials;
use Application\Enum\CredentialsStatus;
use Application\Enum\CredentialsType;
use Application\Enum\UserStatus;

/**
 * Class GoogleAdapter
 *
 * @package Application\Credentials\Adapter
 */
class GoogleAdapter extends CredentialsAdapterAbstract
{
    /**
     * @var string
     */
    protected $_credentialType = CredentialsType::GOOGLE;

    /**
     * @param  string $identity
     * @param  string $token
     * @param  array  $data (Optional)
     * @return \Application\Entity\User
     * @throws InvalidTokenException
     */
    public function signup($identity, $token, array $data = array())
    {
        // validate token
        $gpUser = $this->_requestUserData($token);
        if ($gpUser->getUserId() !== $identity) {
            throw new InvalidTokenException('Invalid identity and token pair');
        }

        // first check existing credentials
        /** @var \Application\Entity\Credentials $credentials */
        $credentials = $this->_getCredentials($identity);
        if (null !== $credentials) {
            // only update token and return user
            $this->updateToken($credentials, $token);

            return $credentials->getUser();
        }

        $attributes    = $gpUser->getAttributes();
        $data['email'] = $attributes['payload']['email'];

        // register new user
        $credentials = new Credentials;
        $credentials->setIdentity($identity)
            ->setToken($token)
            ->setType($this->_credentialType)
            ->setStatus(CredentialsStatus::CONFIRMED);

        // retrieve or create new user
        $user = null === $credentials->getUser()
            ? $this->_findUserByEmailOrCreateNew($data['email'])
            : $credentials->getUser();
        $user = $this->_updateUser($user, $data, false);
        $user->addCredential($credentials)
            ->setStatus(UserStatus::ACTIVE);

        return $user;
    }

    /**
     * @param  string $token
     * @return \Google_Auth_LoginTicket
     */
    protected function _requestUserData($token)
    {
        $sm = $this->getServiceLocator();
        /** @var $facebook \Google_Service_Plus */
        $google = $sm->get('GoogleService');

        try {
            /** @var $ticket \Google_Auth_LoginTicket */
            $ticket = $google->getClient()->verifyIdToken($token);
        } catch (\Google_Auth_Exception $e) {
            throw new InvalidTokenException('Invalid token');
        }

        return $ticket;
    }
}