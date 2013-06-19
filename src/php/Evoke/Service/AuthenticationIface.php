<?php
/**
 * Authentication Interface
 *
 * @package Service
 */
namespace Evoke\Service;

/**
 * Authentication Interface
 *
 * This interface takes its inspiration from PHP-PasswordLib, so I have added
 * Anthony Ferrara as an author.
 *
 * @author    Anthony Ferrara
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 The Authors
 * @license   MIT
 * @package   Service
 */
interface AuthenticationIface
{
    /**
     * Create a password hash from the supplied password and generator prefix.
     *
     * @param string The password to hash.
     * @param string The prefix of the hashing function.
     *
     * @return string The password hash.
     */
	public function createPasswordHash($password, $prefix = '$2a$');

	/**
	 * Verify a password against a supplied password hash
	 *
	 * @param string The password to verify.
	 * @param string The valid hash to verify against.
	 *
	 * @return boolean Whether the password is valid.
	 */
	public function verifyPasswordHash($password, $hash);	
}
// EOF
