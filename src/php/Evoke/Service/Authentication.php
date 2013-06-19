<?php
/**
 * Authentication
 *
 * @package Service
 */
namespace Evoke\Service;

/**
 * Use PHP-PasswordLib from Anthony Ferrara (ircmaxell).  This will need to be
 * autoloaded as per the installation instructions for PHP-PasswordLib (don't
 * worry, its easy).
 */
use PasswordLib\PasswordLib;

/**
 * Authentication
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
class Authentication extends PasswordLib implements AuthenticationIface
{
	/**
	 * Create the password hash, defaulting to Blowfish post PHP 5.3.7.
	 *
	 * @param string $password The password to hash
     * @param string $prefix   The prefix of the hashing function
     *
     * @return string The generated password hash
	 */
	public function createPasswordHash($password, $prefix = '$2y$')
	{
		return parent::createPasswordHash($password, $prefix);
	}
}
// EOF
