<?php

namespace App\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $route = '#',
        public string $label = '',
        public bool   $active = false,
        public bool   $hasPanel = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $activeClasses = 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light';
        $defaultClasses = 'outline-hidden transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25';

        $classes = $this->active ? $activeClasses : $defaultClasses;

        return view('components.sidebar.nav-item', [
            'classes' => $classes
        ]);
    }
}
