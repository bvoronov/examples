<?php
namespace Application\Credentials;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Factory\OAuth2ServerInstanceFactory as ServerInstanceFactory;

/**
 * Class OAuth2ServerFactory
 *
 * @package Application\Credentials
 */
class OAuth2ServerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \OAuth2\Server
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['zf-oauth2']) ? $config['zf-oauth2'] : [];

        $serverInstanceFactory   = new ServerInstanceFactory($config, $serviceLocator);
        $serverInstanceDecorator = new OAuth2ServerInstanceFactory($serverInstanceFactory);
        $serverInstanceDecorator->setServiceLocator($serviceLocator);

        return $serverInstanceDecorator;
    }
}