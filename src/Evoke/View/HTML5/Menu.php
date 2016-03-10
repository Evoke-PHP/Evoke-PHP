<?php
/**
 * Menu Control View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\Model\Data\TreeIface;
use Evoke\View\ViewIface;
use LogicException;
use RecursiveIteratorIterator;

/**
 * Menu Control View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Menu implements ViewIface
{
    /**
     * Tree data
     *
     * @var TreeIface.
     */
    protected $tree;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view of the menu.
     *
     * @return mixed[] The view tree.
     * @throws LogicException If the tree has not been set for the view.
     */
    public function get()
    {
        if (!isset($this->tree)) {
            throw new LogicException('needs tree to be set.');
        }

        $menuClass = 'Menu ' . $this->tree->get();
        $menuItems = [];

        if ($this->tree->hasChildren()) {
            $menuItems = $this->getMenu($this->tree);
        } else {
            $menuClass .= ' Empty';
        }

        return ['ul', ['class' => $menuClass], $menuItems];
    }

    /**
     * Set the menu data.
     *
     * @param TreeIface $tree Menu data.
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
     * @param TreeIface $tree The tree that specifies the menu.
     * @return mixed[] HTML5 menu.
     */
    protected function getMenu(TreeIface $tree)
    {
        $childrenPosition = 2;
        $currentDepth     = 0;
        $iterator         = new RecursiveIteratorIterator(
            $tree,
            RecursiveIteratorIterator::SELF_FIRST
        );
        $menu             = [];
        $menuPtr          =& $menu;

        foreach ($iterator as $node) {
            $newDepth     = $iterator->getDepth();
            $depthChange  = $newDepth - $currentDepth;
            $currentDepth = $newDepth;
            $menuPtr      =& $menu;

            // Set the pointer to the correct depth of the tree.
            for ($i = 0; $i < $currentDepth; $i++) {
                // Go to the last list item elements children.
                end($menuPtr);
                $endKey  = key($menuPtr);
                $menuPtr =& $menuPtr[$endKey][$childrenPosition];

                // Build the sub level if it hasn't been built already.
                if ($depthChange > 0 && $i == $currentDepth - 1) {
                    $menuPtr[] = ['ul', [], []];
                }

                // Go to the last unordered list elements children. This is
                // always in position 1 just after the li.
                $menuPtr =& $menuPtr[1][$childrenPosition];
            }

            $menuItem  = $node->get();
            $menuPtr[] = [
                'li',
                ['class' => 'Menu_Item Level_' . $currentDepth],
                [
                    [
                        'a',
                        ['href' => $menuItem['href']],
                        $menuItem['text']
                    ]
                ]
            ];
        }

        return $menu;
    }
}
// EOF
