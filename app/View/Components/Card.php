<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $title;
    public $value;
    public $color;
    public $subtitle;
    public $extra;

    public function __construct($title = '', $value = '', $color = 'text-gray-900 !important', $subtitle = '', $extra = '')
    {
        $this->title = $title;
        $this->value = $value;
        $this->color = $color;
        $this->subtitle = $subtitle;
        $this->extra = $extra;
    }

    public function render()
    {
        return view('components.card');
    }
}
