<?php
namespace Application\Credentials;

use Application\Entity\Credentials;

/**
 * Interface CredentialsAdapterInterface
 *
 * @package Application\Credentials
 */
interface CredentialsAdapterInterface extends OAuth2AdapterInterface, SignupAdapterInterface
{
    /**
     * @param  \Application\Entity\Credentials
     * @param  string $token
     * @return \Application\Credentials\CredentialsAdapterInterface
     */
    public function updateToken(Credentials $credentials, $token);
}