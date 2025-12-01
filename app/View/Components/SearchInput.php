<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchInput extends Component
{
    public string $placeholder;
    public string $id;

    public function __construct(
        string $placeholder = "Suchen...",
        string $id = "searchInput"
    ) {
        $this->placeholder = $placeholder;
        $this->id = $id;
    }

    public function render()
    {
        return view('components.search-input');
    }
}
