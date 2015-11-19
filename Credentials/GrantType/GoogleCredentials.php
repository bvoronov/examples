<?php
namespace Application\Credentials\GrantType;

use Application\Enum\CredentialsType;

/**
 * Class GoogleCredentials
 *
 * @package Application\Credentials\GrantType
 */
class GoogleCredentials extends CredentialsAbstract
{
    /**
     * @var string
     */
    protected $_queryStringIdentifier = CredentialsType::GOOGLE;
}