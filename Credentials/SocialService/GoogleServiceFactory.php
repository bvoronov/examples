<?php
namespace Application\Credentials\SocialService;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GoogleServiceFactory
 *
 * @package Application\Credentials\SocialService
 */
class GoogleServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Google_Service_Plus
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['credentials']['social']['google'];

        $client = new \Google_Client($config);
        $client->setClientId($config['client_id']);
        $client->setClientSecret($config['client_secret']);

        return new \Google_Service_Plus($client);
    }
}