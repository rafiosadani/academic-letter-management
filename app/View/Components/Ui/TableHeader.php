<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableHeader extends Component
{
    public string $title;
    public ?string $description;
    public bool $showSearch;
    public string $searchPlaceholder;
    public ?string $searchValue;
    public bool $hasDeletedView;
    public bool $isDeletedView;
    public ?string $createRoute;
    public ?string $createText;
    public ?string $indexRoute;
    public ?int $deletedCount;
    public ?string $restoreAllModalId;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title,
        ?string $description = null,
        bool $showSearch = true,
        string $searchPlaceholder = 'Search...',
        ?string $searchValue = null,
        bool $hasDeletedView = true,
        bool $isDeletedView = false,
        ?string $createRoute = null,
        ?string $createText = 'Tambah Data',
        ?string $indexRoute = null,
        ?int $deletedCount = 0,
        ?string $restoreAllModalId = 'restore-all-modal'
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->showSearch = $showSearch;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->searchValue = $searchValue ?? request('search');
        $this->hasDeletedView = $hasDeletedView;
        $this->isDeletedView = $isDeletedView;
        $this->createRoute = $createRoute;
        $this->createText = $createText;
        $this->indexRoute = $indexRoute;
        $this->deletedCount = $deletedCount;
        $this->restoreAllModalId = $restoreAllModalId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.table-header');
    }
}
