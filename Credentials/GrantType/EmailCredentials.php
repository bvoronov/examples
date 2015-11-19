<?php
namespace Application\Credentials\GrantType;

use Application\Enum\CredentialsType;

/**
 * Class EmailCredentials
 *
 * @package Application\Credentials\GrantType
 */
class EmailCredentials extends CredentialsAbstract
{
    /**
     * @var string
     */
    protected $_queryStringIdentifier = CredentialsType::EMAIL;
}