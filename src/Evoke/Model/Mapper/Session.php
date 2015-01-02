<?php
/**
 * Session Mapper
 *
 * @package Model\Mapper
 */
namespace Evoke\Model\Mapper;

use Evoke\Model\Persistence\SessionIface;

/**
 * Session Mapper
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Mapper
 */
class Session implements MapperIface
{
    /**
     * The Session storage that we are mapping.
     *
     * @var SessionIface
     */
    protected $session;

    /**
     * Construct a Session Mapper.
     *
     * @param SessionIface $session The session that we are mapping.
     */
    public function __construct(SessionIface $session)
    {
        $this->session = $session;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Create data in the session.
     *
     * @param mixed[] $data The data to create in storage as a simple array.
     */
    public function create(Array $data)
    {
        $this->session->setData($data);
    }

    /**
     * Delete data in the session.
     *
     * @param mixed[] $offset The offset of the data to delete from storage.
     */
    public function delete(Array $offset)
    {
        $this->session->deleteAtOffset($offset);
    }

    /**
     * Read data from the session.
     *
     * @param mixed[] $offset The data to match (the offset).
     * @return mixed[]|null Session data or null if the offset does not exist.
     */
    public function read(Array $offset = [])
    {
        return $this->session->getAtOffset($offset);
    }

    /**
     * Update data in the session.
     *
     * @param mixed[] $offset The data to match (the offset).
     * @param mixed[] $data   The data to set.
     */
    public function update(Array $offset, Array $data)
    {
        $this->session->setDataAtOffset($data, $offset);
    }
}
// EOF
