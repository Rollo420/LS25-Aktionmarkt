<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class FarmService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function currentActor()
    {        
        $this->user->setFarmMode(true);

        if ($this->user->isInFarm() && $this->user->getFarmMode())
        {
            return $this->user->farms()->first();
        }

        return $this->user;
    }

}
