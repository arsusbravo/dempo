<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\WebsiteText;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'activeNav' => 'home',
            'texts' => WebsiteText::where('group', 'home')->pluck('value', 'key'),
            'featuredMenus' => Menu::with('category')
                ->where('is_featured', true)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function menu()
    {
        return view('pages.menu', [
            'activeNav' => 'menu',
            'menus' => Menu::query()
                ->with('category')
                ->join('menu_categories', 'menu_categories.id', '=', 'menus.menu_category_id')
                ->where('menus.is_active', true)
                ->orderBy('menu_categories.sort_order')
                ->orderBy('menus.sort_order')
                ->select('menus.*')
                ->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'activeNav' => 'about',
            'texts' => WebsiteText::where('group', 'about')->pluck('value', 'key'),
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'activeNav' => 'contact',
            'texts' => WebsiteText::where('group', 'contact')->pluck('value', 'key'),
        ]);
    }
}
