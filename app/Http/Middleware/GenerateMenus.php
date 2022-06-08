<?php

namespace App\Http\Middleware;

use App\Models\MenuItem;
use Closure;
use Illuminate\Http\Request;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        \Menu::make('NavBar', function($menu){
            $menuitems = MenuItem::all();
            foreach($menuitems as $menuitem)
            {
                
                if(!is_null($menuitem->parent)){
                   // For example, 'Conferences', a top level menu item with a null parent field
                   $menu->add($menuitem->name, url($menuitem->url));
                }
                else{
                    // Parent is a field in database, for example 'Traverse City 2015' would have the parent 'conferences'
                    $parent = $menuitem->parent;
                    $menu->item($parent)->add($menuitem->name, url($menuitem->url));
    
                }
            }
        });
        return $next($request);
    }
}
