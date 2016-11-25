<?php
declare(strict_types = 1);
/**
 * TreeBuilder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use InvalidArgumentException;

/**
 * TreeBuilder
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class TreeBuilder
{
    /**
     * The left field name.
     * @var string
     */
    protected $left;

    /**
     * The right field name.
     * @var string
     */
    protected $right;

    /**
     * Construct a TreeBuilder object.
     *
     * @param string $left
     * @param string $right
     */
    public function __construct($left = 'lft', $right = 'rgt')
    {
        $this->left  = $left;
        $this->right = $right;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Build a tree from Modified Pre-order Tree Traversal data.
     *
     * @param string  $treeName The name of the tree.
     * @param mixed[] $mptt     The Modified Pre-order Tree Traversal data.
     * @return Tree The tree.
     * @throws InvalidArgumentException If the MPTT data is bad.
     */
    public function build($treeName, Array $mptt)
    {
        $mpttItems = count($mptt);

        if ($mpttItems < 1) {
            throw new InvalidArgumentException('needs MPTT entries to build tree.');
        }

        if (!isset($mptt[0][$this->left], $mptt[0][$this->right])
        ) {
            throw new InvalidArgumentException(
                'needs MPTT root with ' . $this->left . ' and ' . $this->right . ' fields.'
            );
        }

        $rootNode         = new Tree;
        $level            = 0;
        $treePtrs         = [];
        $children         = [];
        $children[$level] =
            ($mptt[0][$this->right] - $mptt[0][$this->left] - 1) / 2;

        unset($mptt[0][$this->left], $mptt[0][$this->right]);
        $rootNode->set($treeName);

        $treePtrs[$level++] =& $rootNode;

        for ($item = 1; $item < $mpttItems; ++$item) {
            if (!isset($mptt[$item][$this->left], $mptt[$item][$this->right])) {
                throw new InvalidArgumentException(
                    'needs MPTT data at ' . $item . ' with ' . $this->left . ' and ' . $this->right . ' fields.'
                );
            }

            $node       = new Tree;
            $childNodes = ($mptt[$item][$this->right] -
                    $mptt[$item][$this->left] - 1) / 2;
            unset($mptt[$item][$this->left], $mptt[$item][$this->right]);
            $node->set($mptt[$item]);
            $treePtrs[$level - 1]->add($node);

            // We have processed the node, update the child counts, removing a level if it has been fully processed.
            for ($lev = $level - 1; $lev >= 0; --$lev) {
                if (--$children[$lev] === 0) {
                    unset($children[--$level]);
                }
            }

            // If we have children update the tree pointers and level.
            if ($childNodes > 0) {
                $children[$level]   = $childNodes;
                $treePtrs[$level++] =& $node;
            }

            // Unset the local reference to node so that we can use it as a variable again.
            unset($node);
        }

        return $rootNode;
    }
}
// EOF
