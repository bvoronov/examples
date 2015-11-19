<?php
namespace Application\Credentials;

/**
 * Interface SignupAdapterInterface
 *
 * @package Application\Credentials
 */
interface SignupAdapterInterface
{
    /**
     * @param  string $identity Identifier of the user
     * @param  string $token    Token to authenticate the user
     * @param  array  $data     (Optional) Additional info
     * @return \Application\Entity\User|NULL
     * @throws \Application\Credentials\Exception\InvalidTokenException
     * @throws \Application\Credentials\Exception\SignupIsNotSupportedException
     */
    public function signup($identity, $token, array $data = array());
}