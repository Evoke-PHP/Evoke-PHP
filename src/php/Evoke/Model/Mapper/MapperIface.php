<?php
/**
 * Mapper Interface
 *
 * @package Model
 */
namespace Evoke\Model\Mapper;

/**
 * Mapper Interface
 *
 * Decouples the CRUD from the storage mechanism.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface MapperIface
implements CreateIface, ReadIface, UpdateIface, DeleteIface
{
}
// EOF