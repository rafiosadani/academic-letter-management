<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $id;
    public string $type;
    public string $title;
    public string $message;
    public string $buttonText;
    public ?string $icon;
    public array $config;
    public bool $showButton;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id = 'alert-modal',
        string $type = 'success',
        string $title = 'Success',
        string $message = '',
        string $buttonText = 'OK',
        ?string $icon = null,
        bool $showButton = false
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->buttonText = $buttonText;
        $this->icon = $icon;
        $this->showButton = $showButton;

        $configs = [
            'success' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-24 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'buttonClass' => 'bg-success hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90',
            ],
            'error' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-24 shrink-0 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'buttonClass' => 'bg-error hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90',
            ],
            'warning' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-24 shrink-0 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                'buttonClass' => 'bg-warning hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90',
            ],
            'info' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-24 shrink-0 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'buttonClass' => 'bg-info hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90',
            ],
        ];

        $this->config = $configs[$type] ?? $configs['success'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.alert');
    }

    public function getDisplayIcon(): string
    {
        return $this->icon ?? $this->config['icon'];
    }
}
