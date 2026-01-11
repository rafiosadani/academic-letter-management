<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Base extends Component
{
    public $id;
    public $size;
    public $position;
    public $backdrop;
    public $closeButton;
    public $title;

    public $sizeClass;
    public $positionClass;
    public $backdropClass;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = 'modal',
        $size = 'md',
        $position = 'center',
        $backdrop = 'blur',
        $closeButton = true,
        $title = null
    ) {
        // Set properti yang diterima
        $this->id = $id;
        $this->size = $size;
        $this->position = $position;
        $this->backdrop = $backdrop;
        $this->closeButton = $closeButton;
        $this->title = $title;

        // Panggil metode untuk menghitung class CSS
        $this->setSizeClass();
        $this->setPositionClass();
        $this->setBackdropClass();
    }

    protected function setSizeClass()
    {
        $sizes = [
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
            'xl' => 'max-w-xl',
            '2xl' => 'max-w-2xl',
            'full' => 'max-w-full',
        ];

        $this->sizeClass = $sizes[$this->size] ?? $sizes['md'];
    }

    protected function setPositionClass()
    {
        $positions = [
            'top' => 'items-start pt-20',
            'center' => 'items-center',
            'bottom' => 'items-end pb-20',
        ];

        $this->positionClass = $positions[$this->position] ?? $positions['center'];
    }

    protected function setBackdropClass()
    {
        $backdrops = [
            'blur' => 'bg-slate-900/60 backdrop-blur-sm',
            'dark' => 'bg-slate-900/80',
            'none' => 'bg-slate-900/40',
        ];

        $this->backdropClass = $backdrops[$this->backdrop] ?? $backdrops['blur'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.base');
    }
}
