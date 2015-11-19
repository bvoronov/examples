<?php
namespace Application\Credentials\GrantType;

use Application\Enum\CredentialsType;

/**
 * Class CodeCredentials
 *
 * @package Application\Credentials\GrantType
 */
class CodeCredentials extends CredentialsAbstract
{
    /**
     * @var string
     */
    protected $_queryStringIdentifier = CredentialsType::CODE;
}