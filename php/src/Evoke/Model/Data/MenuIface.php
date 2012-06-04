<?php
namespace Evoke\Model\Data;

interface MenuIface extends DataIface
{
	/// Get the menu as a tree.
	public function getMenu();
}
// EOF
