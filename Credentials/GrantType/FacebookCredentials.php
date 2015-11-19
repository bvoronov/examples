<?php
namespace Application\Credentials\GrantType;

use Application\Enum\CredentialsType;

/**
 * Class FacebookCredentials
 *
 * @package Application\Credentials\GrantType
 */
class FacebookCredentials extends CredentialsAbstract
{
    /**
     * @var string
     */
    protected $_queryStringIdentifier = CredentialsType::FACEBOOK;
}