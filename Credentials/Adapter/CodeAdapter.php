<?php
namespace Application\Credentials\Adapter;

use Application\Credentials\CredentialsAdapterAbstract;
use Application\Credentials\Exception\SignupIsNotSupportedException;
use Application\Enum\CredentialsType;

/**
 * Class CodeAdapter
 *
 * @package Application\Credentials\Adapter
 */
class CodeAdapter extends CredentialsAdapterAbstract
{
    /**
     * @var string
     */
    protected $_credentialType = CredentialsType::CODE;

    /**
     * @var \Application\Entity\Credentials
     */
    protected $_credentials;

    /**
     * @param  string $identity Identifier of the user
     * @param  string $token    Token to authenticate the user
     * @param  array  $data     (Optional)
     * @return \Application\Entity\User
     */
    public function signup($identity, $token, array $data = array())
    {
        throw new SignupIsNotSupportedException('Signup is not supported for the specified type');
    }

    /**
     * @param  string $identity
     * @return \Application\Entity\Credentials
     */
    protected function _getCredentials($identity)
    {
        if (null !== $this->_credentials) {
            return $this->_credentials;
        }

        $credentials = parent::_getCredentials($identity);
        $this->_credentials = $credentials;

        $sm = $this->getServiceLocator();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $sm->get('em');

        // code is available only once
        $em->remove($credentials);
        $em->flush($credentials);

        return $this->_credentials;
    }
}