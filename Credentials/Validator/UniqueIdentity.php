<?php
namespace Application\Credentials\Validator;

use Application\Enum\CredentialsStatus;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Validator\AbstractValidator;

/**
 * Class UniqueIdentity
 *
 * @package Application\Credentials\Validator
 */
class UniqueIdentity extends AbstractValidator
    implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const NOT_UNIQUE = 'notUnique';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_UNIQUE => 'Identity already exists'
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

        if (null !== $credentials && $credentials->getStatus() != CredentialsStatus::REMOVED) {
            $this->error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }
}