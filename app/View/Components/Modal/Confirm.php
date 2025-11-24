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
    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = 'confirm-modal',
        $title = 'Confirmation',
        $message = 'Are you sure?',
        $cancelText = 'Cancel',
        $confirmText = 'Confirm',
        $confirmType = 'primary',       // primary, danger, success, warning
        $transition = 'shift-up'        // shift-up, shift-down, scale
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->cancelText = $cancelText;
        $this->confirmText = $confirmText;
        $this->confirmType = $confirmType;
        $this->transition = $transition;

        $this->setConfirmClass();
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

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.confirm');
    }
}
