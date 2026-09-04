<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class WebLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $back = null,
        public bool $search = true,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.web');
    }
}
