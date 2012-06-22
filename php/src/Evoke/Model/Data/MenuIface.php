<?php
namespace Evoke\Model\Data;

/**
 * MenuIface
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface MenuIface extends DataIface
{
	/**
	 * Get the menu as a tree.
	 */
	public function getMenu();
}
// EOF
