<?php
namespace Application\Credentials\SocialService;

use Facebook\Facebook;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FacebookServiceFactory
 *
 * @package Application\Credentials\SocialService
 */
class FacebookServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Facebook\Facebook
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['credentials']['social']['facebook'];

        return new Facebook($config);
    }
}