<?php

namespace App\Helpers;

use App\Helpers\MenuItem;
use Illuminate\Database\Eloquent\Collection;

/*
    The menu class needs to accomplish the following.
    * Needs to be open, and easy to manipulate insert items at any index, remove items, reorder items
    * Menu's can have their own models and tables to be manage as data driven
    * Menu's should also have the ability to generate based on content structure
    *

    Sample:

    $menu = new Menu;
    $menu->append($menu->button('test', [
        'action' => 'Controller@index'
    ]));
 */

class Menu extends Collection
{
    function __construct($text = null)
    {
        parent::__construct();
    }

    /**
     * Create an anchor menu item
     * @param  string $text             Link text
     * @param  array  $properties       List of properties of the link
     * @return \Baytek\Menu\MenuItem    MenuItem Object
     */
    public static function anchor($text, array $properties = [])
    {
        return new MenuItem($text, $properties);
    }

    /**
     * Create an button menu item
     * @param  string $text             Button text
     * @param  array  $properties       List of properties of the link
     * @return \Baytek\Menu\MenuItem    MenuItem Object
     */
    public static function button($text, array $properties = [])
    {
        return new MenuItem($text, $properties);
    }

    /**
     * Render the collection of Menu Items to an HTML list
     * @param  string $wrapItems HTML node name to wrap the menu items
     * @param  string $wrapMenu  HTML node name to wrap the menu
     * @return string            HTML markup list
     */
    public function toNav($wrapItems = 'li', $wrapMenu = 'ol')
    {
        $menu = collect($this)->map(function($link) use ($wrapItems)
        {
            return "<$wrapItems>$link</s$wrapItems>";
        })->implode('');

        return "<$wrapMenu>$menu</$wrapMenu>";
    }

    /**
     * To string magic method that simply runs the toNav method
     * @return string HTML markup list
     */
    public function __toString()
    {
        return $this->toNav();
    }
}