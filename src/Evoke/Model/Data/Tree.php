<?php
/**
 * Tree
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Tree
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class Tree implements TreeIface
{
    /**
     * The children of the current node.
     *
     * @var Tree[]
     */
    protected $children = [];

    /**
     * The position of the iterator.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The value of the current node.
     *
     * @var mixed
     */
    protected $value;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a node to the tree.
     *
     * @param TreeIface $node The node to add as a child.
     */
    public function add(TreeIface $node)
    {
        $this->children[] = $node;
    }

    /**
     * Get the current item we are iterating over.
     *
     * @return TreeIface The current node that we are iterating over.
     */
    public function current()
    {
        return $this->children[$this->position];
    }

    /**
     * Get the value of the current node.
     *
     * @return mixed The value of the current node.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Get the children of the node.
     *
     * @return TreeIface[]
     */
    public function getChildren()
    {
        return $this->children[$this->position];
    }

    /**
     * Return whether the node has any children.
     *
     * @return bool Whether the node has any children.
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Return the key for the current node.
     *
     * @return mixed The key for the current node.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Increment to the next child node.
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Rewind the iterator.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Return whether the current node is valid.
     *
     * @return bool Whether the current node is valid.
     */
    public function valid()
    {
        return $this->position < count($this->children);
    }

    /**
     * Set the value of the node.
     *
     * @param mixed $value Value for the node.
     */
    public function set($value)
    {
        $this->value = $value;
    }
}
// EOF