<?php

namespace Wcom\Jwt\Domain;

/**
 * Header token
 * @author Gemma Black <gblackuk@gmail.com>
 */
class HeaderToken extends AccessToken
{
    /**
     * Defines the access token
     *
     * @param string $accessToken
     * @return HeaderToken
     */
    public static function define($accessToken)
    {
        if (!is_string($accessToken)) {
            throw new DomainException('Header token should be a string');
        }

        if (!self::isJWTStructure($accessToken)) {
            throw new DomainException('Header token is not a valid JWT');
        }

        return new static($accessToken);
    }
}