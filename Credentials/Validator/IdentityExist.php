<?php
namespace Application\Credentials\Validator;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Validator\AbstractValidator;

/**
 * Class IdentityExist
 *
 * @package Application\Credentials\Validator
 */
class IdentityExist extends AbstractValidator
    implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const NOT_EXISTS = 'notExists';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_EXISTS => 'Identity does not exists'
    );

    /**
     * @param  string $value
     * @return boolean FALSE if no user was found with specified email
     */
    public function isValid($value)
    {
        // getServiceLocator x2: because the first is HydratorPluginManager and the second is "global" service manager
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getServiceLocator()
            ->getServiceLocator()
            ->get('em');

        /** @var $credentials \Application\Entity\Credentials */
        $credentials = $em->getRepository('Application\Entity\Credentials')
            ->findOneByIdentity($value);

        if (null == $credentials) {
            $this->error(self::NOT_EXISTS);
            return false;
        }

        return true;
    }
}