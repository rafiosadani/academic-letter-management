<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Confirm extends Component
{
    public $id;
    public $title;
    public $message;
    public $cancelText;
    public $confirmText;
    public $confirmType;
    public $transition;

    public $confirmClass;
    public $icon;
    public array $config;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = 'confirm-modal',
        $title = 'Confirmation',
        $message = 'Are you sure?',
        $cancelText = 'Cancel',
        $confirmText = 'Confirm',
        $confirmType = 'primary',           // primary, danger, success, warning
        $transition = 'shift-up',           // shift-up, shift-down, scale
        $icon = null                        // Custom icon override
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->cancelText = $cancelText;
        $this->confirmText = $confirmText;
        $this->confirmType = $confirmType;
        $this->transition = $transition;
        $this->icon = $icon;

        $this->setConfirmConfig();
    }

    protected function setConfirmClass()
    {
        // Confirm button types
        $confirmTypes = [
            'primary' => 'bg-primary hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90',
            'danger' => 'bg-error hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90',
            'success' => 'bg-success hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90',
            'warning' => 'bg-warning hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90',
        ];

        $this->confirmClass = $confirmTypes[$this->confirmType] ?? $confirmTypes['primary'];
    }

    protected function setConfirmConfig()
    {
        // Config untuk setiap tipe konfirmasi
        $configs = [
            'primary' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 shrink-0 text-primary dark:text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'buttonClass' => 'bg-primary hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90',
            ],
            'error' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 shrink-0 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                'buttonClass' => 'bg-error hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90',
            ],
            'success' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'buttonClass' => 'bg-success hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90',
            ],
            'warning' => [
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="inline size-28 shrink-0 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                'buttonClass' => 'bg-warning hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90',
            ],
        ];

        $this->config = $configs[$this->confirmType] ?? $configs['primary'];
        $this->confirmClass = $this->config['buttonClass'];
    }

    /**
     * Get icon untuk ditampilkan
     */
    public function getDisplayIcon(): string
    {
        return $this->icon ?? $this->config['icon'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.confirm');
    }
}
