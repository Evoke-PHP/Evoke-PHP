<?php
/**
 * Join
 *
 * @package Model\Data\Join
 */
namespace Evoke\Model\Data\Join;

use DomainException;
use LogicException;

/**
 * Join
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data\Join
 */
abstract class Join implements JoinIface
{
    /**
     * Array of join identifiers to join objects from the current join.  The
     * joins form a tree structure which describes the hierarchy of data
     * represented by the flat structure.
     *
     * @var JoinIface[]
     */
    protected $joins;

    /**
     * The join keys from the joins array.
     *
     * @var string[]
     */
    protected $joinKeys;

    /**
     * Whether we can refer to joins using a case-insensitive alpha numeric
     * match in addition to the exact join passed upon adding the join. This
     * allows us to match joins between different formats such as
     * Pascal_Case, lowerCamelCase, UpperCamelCase, snake_case. It could
     * also be used to match ST_uP-iD_&*#(C)(*aSe.  These joins would have
     * to be matched exactly if this boolean is not set true.
     *
     * @var bool
     */
    protected $useAlphaNumMatch;

    /**
     * Construct the Join object.
     *
     * @param bool $useAlphaNumMatch
     * Whether we can refer to joins using a case-insensitive alpha numeric
     * match.
     */
    public function __construct($useAlphaNumMatch)
    {
        $this->joinKeys         = [];
        $this->joins            = [];
        $this->useAlphaNumMatch = $useAlphaNumMatch;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a join for the data.
     *
     * @param string    $joinID The canonical join ID.
     * @param JoinIface $join   The join to add.
     * @throws LogicException If the join to be added is ambiguous.
     */
    public function addJoin($joinID, JoinIface $join)
    {
        $usableJoinID = $this->useAlphaNumMatch ?
            $this->toAlphaNumLower($joinID) :
            $joinID;

        if (in_array($usableJoinID, $this->joinKeys)) {
            throw new LogicException('Ambiguous join: ' . $joinID);
        }

        $this->joinKeys[]     = $usableJoinID;
        $this->joins[$joinID] = $join;
    }

    /**
     * Get the join ID for the specified join or throw an exception if it can't
     * be found uniquely.
     *
     * The join can be matched in two ways:
     *
     * - An exact match: `Join_Name`
     * - A lowerCamelCase match: `joinName`
     *
     * The Join ID will be returned as the exact match.
     *
     * @param string $join Join to get the ID for.
     * @return string The matched join.
     * @throws DomainException If the join cannot be found.
     */
    public function getJoinID($join)
    {
        if (isset($this->joins[$join])) {
            return $join;
        } elseif ($this->useAlphaNumMatch) {
            $alphaNumJoin      = $this->toAlphaNumLower($join);
            $canonicalJoinKeys = array_keys($this->joins);

            foreach ($canonicalJoinKeys as $joinKey) {
                if ($alphaNumJoin === $this->toAlphaNumLower($joinKey)) {
                    return $joinKey;
                }
            }
        }

        throw new DomainException('Join not found');
    }

    /**
     * Get the joins from the join object. The join objects generally form tree
     * structures, so these are the joins from the current node in the tree.
     *
     * @return JoinIface[] The joins from the object identified by their joinID.
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /*******************/
    /* Private Methods */
    /*******************/

    /**
     * Convert a string to alpha numeric in lower-case.
     *
     * @param string $input Input string.
     * @return string The input string converted to lower alpha numeric.
     */
    private function toAlphaNumLower($input)
    {
        return strtolower(preg_replace('~[^[:alnum:]]~', '', $input));
    }
}
// EOF
