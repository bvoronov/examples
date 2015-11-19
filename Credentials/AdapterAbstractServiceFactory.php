<?php
namespace Application\Credentials;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AdapterAbstractServiceFactory
 *
 * @package Application\Credentials
 */
class AdapterAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return Adapter
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $oauthConfig = $services->get('Config');
        $oauthConfig = $oauthConfig['zf-oauth2'];

        if (!isset($oauthConfig['db']) || empty($oauthConfig['db'])) {
            throw new Exception\RuntimeException(
                'The database configuration [\'zf-oauth2\'][\'db\'] for OAuth2 is missing'
            );
        }

        $username = isset($oauthConfig['db']['username'])
            ? $oauthConfig['db']['username'] : null;
        $password = isset($oauthConfig['db']['password'])
            ? $oauthConfig['db']['password'] : null;

        $config  = $this->getConfig($services);
        $service = new $config[$requestedName](
            array(
                'dsn'      => $oauthConfig['db']['dsn'],
                'username' => $username,
                'password' => $password,
            ),
            isset($oauthConfig['options']) ? $oauthConfig['options'] : array()
        );

        return $service;
    }

    /**
     * @param  ServiceLocatorInterface $services
     * @param  string $name
     * @param  string $requestedName
     * @return boolean
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return isset($config[$requestedName]);
    }

    /**
     * Get credentials configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config['credentials'])
            || !is_array($config['credentials'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $config = $config['credentials'];
        if (!isset($config['adapters'])
            || !is_array($config['adapters'])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config['adapters'];
        return $this->config;
    }
}