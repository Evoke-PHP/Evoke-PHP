<?php
/**
 * Menu Control View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\Model\Data\TreeIface,
    Evoke\View\ViewIface,
    LogicException,
    RecursiveIteratorIterator;

/**
 * Menu Control View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Menu implements ViewIface
{
    /**
     * Protected properties.
     *
     * @var TreeIface $tree Tree data.
     */
    protected $tree;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view of the menu.
     *
     * @return mixed[] The view tree.
     */
    public function get()
    {
        if (!isset($this->tree))
        {
            throw new LogicException('needs tree to be set.');
        }

        $menuClass = 'Menu ' . $this->tree->get();
        $menuItems = array();

        if ($this->tree->hasChildren())
        {
            $menuItems = $this->getMenu($this->tree);
        }
        else
        {
            $menuClass .= ' Empty';
        }

        return array('ul', array('class' => $menuClass), $menuItems);
    }

    /**
     * Set the menu data.
     *
     * @param TreeIface Menu data.
     */
    public function set(TreeIface $tree)
    {
        $this->tree = $tree;
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Get the menu.
     *
     * @param TreeIface The tree that specified the menu.
     * @return mixed[] HTML5 menu.
     */
    protected function getMenu(TreeIface $tree)
    {
        $childrenPosition = 2;
        $currentDepth = 0;
        $iterator = new RecursiveIteratorIterator(
            $tree, RecursiveIteratorIterator::SELF_FIRST);
        $menu = array();
        $menuPtr =& $menu;

        foreach ($iterator as $node)
        {
            $newDepth = $iterator->getDepth();
            $depthChange = $newDepth - $currentDepth;
            $currentDepth = $newDepth;
            $menuPtr =& $menu;

            // Set the pointer to the correct depth of the tree.
            for ($i = 0; $i < $currentDepth; $i++)
            {
                // Go to the last list item elements children.
                end($menuPtr);
                $endKey = key($menuPtr);
                $menuPtr =& $menuPtr[$endKey][$childrenPosition];

                // Build the sub level if it hasn't been built already.
                if ($depthChange > 0 && $i == $currentDepth - 1)
                {
                    $menuPtr[] = array('ul', array(), array());
                }

                // Go to the last unordered list elements children. This is
                // always in position 1 just after the li.
                $menuPtr =& $menuPtr[1][$childrenPosition];
            }

            $menuItem = $node->get();
            $menuPtr[] = array(
                'li',
                array('class' => 'Menu_Item Level_' . $currentDepth),
                array(array('a',
                            array('href' => $menuItem['Href']),
                            $menuItem['Text'])
                    ));
        }

        return $menu;
    }
}
// EOF
