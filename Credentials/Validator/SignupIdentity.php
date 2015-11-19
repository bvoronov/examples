<?php
namespace Application\Credentials\Validator;

use Application\Enum\CredentialsType;
use Application\Credentials\Exception\InvalidTokenException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Validator\AbstractValidator;

/**
 * Class SignupIdentity
 *
 * @package Application\Credentials\Validator
 */
class SignupIdentity extends AbstractValidator
    implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const EMAIL_INVALID    = 'emailInvalid';
    const EMAIL_NOT_UNIQUE = 'emailNotUnique';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::EMAIL_INVALID    => 'Email is invalid',
        self::EMAIL_NOT_UNIQUE => 'Email already exists'
    );

    /**
     * @param  string $value
     * @param  array  $context (Optional)
     * @return boolean FALSE if no user was found with specified email
     * @throws \LogicException if context is missing
     * @throws InvalidTokenException if invalid type was specified in context
     */
    public function isValid($value, $context = null)
    {
        if (!is_array($context) || !isset($context['type']) || !isset($context['token'])) {
            throw new \LogicException('Context is not defined');
        }
        $type  = $context['type'];
        $token = $context['token'];

        switch ($context['type']) {
            case CredentialsType::EMAIL:
                $result = $this->_validateEmail($value);
                break;
            case CredentialsType::FACEBOOK:
                $result = $this->_validateFacebook($value, $token);
                break;
            case CredentialsType::GOOGLE:
                $result = $this->_validateGoogle($value, $token);
                break;
            case CredentialsType::CODE:
                $result = $this->_validateCode($value);
                break;
            default:
                throw new InvalidTokenException('Undefined type of registration was specified');
                break;
        }

        return $result;
    }

    /**
     * @param  string $email
     * @return boolean
     */
    protected function _validateEmail($email)
    {
        // valid email address
        $validator = $this->getServiceLocator()
            ->get('Application\Validator\EmailAddress');
        if (!$validator->isValid($email)) {
            $this->error(self::EMAIL_INVALID);
            return false;
        }

        // unique email address
        $validator = $this->getServiceLocator()
            ->get('Application\Credentials\Validator\UniqueIdentity');
        if (!$validator->isValid($email)) {
            $this->error(self::EMAIL_NOT_UNIQUE);
            return false;
        }

        return true;
    }

    /**
     * @param  string $identity
     * @param  string $token
     * @return boolean
     */
    protected function _validateFacebook($identity, $token)
    {
        return true;
    }

    /**
     * @param  string $identity
     * @param  string $token
     * @return boolean
     */
    protected function _validateGoogle($identity, $token)
    {
        return true;
    }

    /**
     * @param  string $code
     * @return boolean
     */
    protected function _validateCode($code)
    {
        return true;
    }
}