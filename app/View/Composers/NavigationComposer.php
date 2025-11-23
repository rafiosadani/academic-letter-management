<?php

namespace App\View\Composers;
use App\View\Navigation\Menu;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $mainMenus = Menu::main();
        $currentMenu = $this->detectCurrentMenu($mainMenus);
        $view->with([
            'mainMenus' => $mainMenus,
            'hasPanel' => $currentMenu['hasPanel'] ?? false,
            'panelTitle' => $currentMenu['panelTitle'] ?? null,
            'currentPanelMenus' => $currentMenu['submenu'] ?? [],
        ]);
    }

    private function detectCurrentMenu(array $menus): ?array
    {
        foreach ($menus as $menu) {
            $activePatterns = (array) ($menu['active'] ?? []);
            foreach ($activePatterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    return $menu;
                }
            }
        }
        return null;
    }
}
