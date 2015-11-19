<?php
namespace Application\Credentials;

use OAuth2\Storage;
use OAuth2\OpenID;

/**
 * Interface OAuth2AdapterInterface
 *
 * @package Application\Credentials
 */
interface OAuth2AdapterInterface extends
    Storage\AccessTokenInterface,
    Storage\ClientCredentialsInterface,
    Storage\UserCredentialsInterface,
    Storage\RefreshTokenInterface,
    Storage\JwtBearerInterface,
    Storage\ScopeInterface,
    Storage\PublicKeyInterface,
    OpenID\Storage\UserClaimsInterface,
    OpenID\Storage\AuthorizationCodeInterface
{
}