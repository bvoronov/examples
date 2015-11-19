<?php
namespace Application\Credentials\Adapter;

use Application\Credentials\CredentialsAdapterAbstract;
use Application\Credentials\Exception\InvalidTokenException;
use Application\Entity\Credentials;
use Application\Enum\CredentialsStatus;
use Application\Enum\CredentialsType;
use Application\Enum\UserStatus;

/**
 * Class FacebookAdapter
 *
 * @package Application\Credentials\Adapter
 */
class FacebookAdapter extends CredentialsAdapterAbstract
{
    /**
     * @var string
     */
    protected $_credentialType = CredentialsType::FACEBOOK;

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
        $fbUser = $this->_requestUserData($token);
        if ($fbUser->getId() !== $identity) {
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

        // register new user
        $credentials = new Credentials;
        $credentials->setIdentity($identity)
            ->setToken($token)
            ->setType($this->_credentialType)
            ->setStatus(CredentialsStatus::CONFIRMED);

        // retrieve or create new user
        $user = null === $credentials->getUser()
            ? $this->_findUserByEmailOrCreateNew($fbUser->getField('email'))
            : $credentials->getUser();
        $user = $this->_updateUser($user, array(
            'email'     => $fbUser->getField('email'),
            'birthday'  => $fbUser->getBirthday(),
            'firstname' => $fbUser->getFirstName(),
            'lastname'  => $fbUser->getLastName(),
            'gender'    => $fbUser->getGender()
        ), false);
        $user->addCredential($credentials)
            ->setStatus(UserStatus::ACTIVE);

        return $user;
    }

    /**
     * @param  string $token
     * @return \Facebook\GraphNodes\GraphUser
     */
    protected function _requestUserData($token)
    {
        $sm = $this->getServiceLocator();
        $config = $sm->get('Config');
        $infoEndpoint = $config['credentials']['social']['facebook']['endpoints']['info'];

        /** @var $facebook \Facebook\Facebook */
        $facebook = $sm->get('FacebookService');
        try {
            /** @var $response \Facebook\FacebookResponse */
            $response = $facebook->get($infoEndpoint, $token);
        } catch (\Exception $e) {
            throw new InvalidTokenException('Invalid token');
        }

        return $response->getGraphUser();
    }
}