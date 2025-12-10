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

    public function currentActorId()
    {        
        #$this->user->setFarmMode(false);

        if ($this->user->isInFarm() && $this->user->getFarmMode())
        {
            return $this->user->farms()->first()->id;
        }

        return $this->user->id;
    }

}
