<?php
namespace Application\Credentials;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use ZF\OAuth2\Adapter\PdoAdapter;

/**
 * Class OAuth2AdapterAbstract
 *
 * @package Application\Credentials
 */
abstract class OAuth2AdapterAbstract extends PdoAdapter
    implements ServiceLocatorAwareInterface, OAuth2AdapterInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @param  string $identity
     * @return \Application\Entity\Credentials
     */
    abstract protected function _getCredentials($identity);

    /**
     * @param  string $token
     * @param  string $hash
     * @return boolean
     */
    abstract protected function _verifyToken($token, $hash);

    /**
     * @param  string $connection
     * @param  array $config
     * @throws \RuntimeException If entity class is missed in the config
     */
    public function __construct($connection, $config = array())
    {
        parent::__construct($connection, $config);

        if ( !isset($config['entity']) ) {
            throw new \RuntimeException('Entity classname is missed');
        }
        $this->entityClass = $config['entity'];
    }

    /**
     * @inheritdoc
     * @see \OAuth2\Storage\UserCredentialsInterface::getUserDetails()
     * @see \OAuth2\Storage\Pdo::getUser()
     */
    final public function getUser($identity)
    {
        $credentials = $this->_getCredentials($identity);
        if (null === $credentials) {
            return false;
        }

        return array(
            'user_id'  => $credentials->getUser()->getId(),
            'password' => $credentials->getToken()
        );
    }

    /**
     * @inheritdoc
     */
    final protected function verifyHash($password, $hash)
    {
        return $this->_verifyToken($password, $hash);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException
     */
    final public function setUser($username, $password, $firstName = null, $lastName = null)
    {
        throw new \RuntimeException('Method is not supported in Credentials\OAuth2AdapterAbstract');
    }
}