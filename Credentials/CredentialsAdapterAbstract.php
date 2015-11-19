<?php
namespace Application\Credentials;

use Application\Entity\Credentials;
use Application\Entity\User;
use Zend\Stdlib\ArrayUtils;

/**
 * Class CredentialsAdapterAbstract
 *
 * @package Application\Credentials
 */
abstract class CredentialsAdapterAbstract extends OAuth2AdapterAbstract
    implements CredentialsAdapterInterface
{
    /**
     * @var string
     */
    protected $_credentialType;

    /**
     * @param  string $identity Identifier of the user
     * @param  string $token    Token to authenticate the user
     * @param  array  $data     (Optional) Additional info
     * @return \Application\Entity\User
     * @throws \Application\Credentials\Exception\InvalidTokenException
     */
    public function signup($identity, $token, array $data = array())
    {
        // first check existing credentials
        /** @var \Application\Entity\Credentials $credentials */
        $credentials = $this->_getCredentials($identity);
        $credentials = null !== $credentials ? $credentials : new Credentials;

        // Encrypt token
        $this->updateToken($credentials, $token);

        // set identity and type for new credentials
        if (null === $credentials->getId()) {
            $credentials->setType($this->_credentialType)
                ->setIdentity($identity);
        }

        // create new user if it is his first credentials
        if (null === ($user = $credentials->getUser())) {
            $user = $this->_findUserByEmailOrCreateNew($identity);
            $user->addCredential($credentials);
        }
        $this->_updateUser($user, $data);

        return $user;
    }

    /**
     * @param  \Application\Entity\Credentials
     * @param  string $token
     * @return \Application\Credentials\CredentialsAdapterInterface
     */
    public function updateToken(Credentials $credentials, $token)
    {
        $credentials->setToken($token);

        return $this;
    }

    /**
     * @param  string $identity
     * @return \Application\Entity\Credentials
     */
    protected function _getCredentials($identity)
    {
        $credentials = $this->getServiceLocator()
            ->get('em')
            ->getRepository($this->entityClass)
            ->findOneBy(array('identity' => $identity, 'type' => $this->_credentialType));
        if (null !== $credentials) {
            $this->_updateCredentialsLastUsage($credentials);
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
        return $token === $hash;
    }

    /**
     * @param  Credentials $credentials
     * @return void
     */
    protected function _updateCredentialsLastUsage(Credentials $credentials)
    {
        $sm = $this->getServiceLocator();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $sm->get('em');

        $credentials->setLastUsedAt(new \DateTime);
        $em->persist($credentials);
        $em->flush($credentials);
    }

    /**
     * @param  User    $user
     * @param  array   $newData
     * @param  boolean $override (Optional) TRUE to override old data or FALSE to complete missing
     * @return User
     */
    protected function _updateUser(User $user, array $newData, $override = true)
    {
        $sm = $this->getServiceLocator();
        // FIXME should use aliases but not reference to API module
        /** @var $hydrator \Zend\Stdlib\Hydrator\HydratorInterface */
        $hydrator = $sm->get('HydratorManager')
            ->get('Api\\V1\\Rest\\User\\UserHydrator');

        // filter the data array
        $newData = ArrayUtils::filter($newData, function ($value, $key) {
            return in_array($key, array('email', 'birthday', 'firstname', 'lastname', 'gender')) && !empty($value);
        }, ArrayUtils::ARRAY_FILTER_USE_BOTH);

        // retrieve and filter current values
        $oldData = $hydrator->extract($user);
        $oldData = ArrayUtils::filter($oldData, function ($value, $key) {
            return in_array($key, array('email', 'birthday', 'firstname', 'lastname', 'gender')) && !empty($value);
        }, ArrayUtils::ARRAY_FILTER_USE_BOTH);

        $data = $override
            ? ArrayUtils::merge($oldData, $newData)
            : ArrayUtils::merge($newData, $oldData);
        $user = $hydrator->hydrate($data, $user);

        return $user;
    }

    /**
     * @param  string $email
     * @return \Application\Entity\User
     */
    protected function _findUserByEmailOrCreateNew($email)
    {
        $sm = $this->getServiceLocator();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $sm->get('em');

        $user = $em->getRepository('Application\Entity\User')
            ->findOneByEmail($email);
        $user = null !== $user ? $user : new User;

        return $user;
    }
}