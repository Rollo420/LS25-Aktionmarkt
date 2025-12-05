<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TransactionList extends Component
{
    public $transactions;

    /**
     * Create a new component instance.
     */
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.transaction-list');
    }
}
