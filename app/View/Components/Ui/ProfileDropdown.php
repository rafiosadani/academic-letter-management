<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileDropdown extends Component
{
    public string $wrapperId;
    public string $refId;
    public string $boxId;
    public array $config;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $location = 'header', // Default 'header'
        public string $size = 'md'          // Default 'md'
    ) {
        $this->wrapperId = $this->location . '-profile-wrapper';
        $this->refId = $this->location . '-profile-ref';
        $this->boxId = $this->location . '-profile-box';

        $sizes = [
            'sm' => [
                'button' => 'size-7',
                'badge' => 'size-2.5',
                'border' => 'border-2',
            ],
            'md' => [
                'button' => 'size-8',
                'badge' => 'size-3',
                'border' => 'border-2',
            ],
            'lg' => [
                'button' => 'size-12',
                'badge' => 'size-3.5',
                'border' => 'border-2',
            ],
        ];

        $this->config = $sizes[$this->size] ?? $sizes['md'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.profile-dropdown');
    }
}
