<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;

class SellTransaction extends Transaction
{
    use HasParent;
    // ...spezifische Logik für SellTransaction...
}
