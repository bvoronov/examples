<?php
namespace Application\Credentials;

use Application\Credentials\GrantType\CodeCredentials;
use Application\Credentials\GrantType\EmailCredentials;
use Application\Credentials\GrantType\FacebookCredentials;
use Application\Credentials\GrantType\GoogleCredentials;
use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use ZF\OAuth2\Factory\OAuth2ServerInstanceFactory as OverriddenInstanceFactory;

/**
 * Class OAuth2ServerInstanceFactory
 *
 * @package Application\Credentials
 */
class OAuth2ServerInstanceFactory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var OverriddenInstanceFactory
     */
    protected $_factory;

    /**
     * @var \OAuth2\Server
     */
    protected $_server;

    /**
     * Constructor
     *
     * @param OverriddenInstanceFactory $factory
     */
    public function __construct(OverriddenInstanceFactory $factory)
    {
        $this->_factory = $factory;
    }

    /**
     * @return \OAuth2\Server
     */
    public function __invoke()
    {
        if (null !== $this->_server) {
            return $this->_server;
        }

        $this->_server = $this->_factory->__invoke();
        // register custom grant types
        $this->_server->addGrantType(new EmailCredentials($this->getServiceLocator()->get('CredentialsAdapter\\Email')));
        $this->_server->addGrantType(new CodeCredentials($this->getServiceLocator()->get('CredentialsAdapter\\Code')));
        $this->_server->addGrantType(new FacebookCredentials($this->getServiceLocator()->get('CredentialsAdapter\\Facebook')));
        $this->_server->addGrantType(new GoogleCredentials($this->getServiceLocator()->get('CredentialsAdapter\\Google')));

        return $this->_server;
    }
}