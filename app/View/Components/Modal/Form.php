<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public string $id;
    public string $title;
    public string $size;
    public string $method;
    public string $action;
    public string $cancelText;
    public string $submitText;
    public string $transition;
    public string $htmlMethod;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id = 'form-modal',
        string $title = 'Form',
        string $size = 'lg',
        string $method = 'POST',            // POST, GET, PUT, PATCH, DELETE
        string $action = '#',
        string $cancelText = 'Cancel',
        string $submitText = 'Submit',
        string $transition = 'modal-scale'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
        $this->method = strtoupper($method);
        $this->action = $action;
        $this->cancelText = $cancelText;
        $this->submitText = $submitText;
        $this->transition = $transition;

        // Tentukan method HTML: Jika bukan GET, gunakan POST (untuk spoofing method)
        $this->htmlMethod = ($this->method === 'GET') ? 'GET' : 'POST';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.form');
    }
}
