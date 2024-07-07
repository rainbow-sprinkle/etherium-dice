<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\DiceGame;

class DiceGameCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $diceGame;

    public function __construct(DiceGame $diceGame)
    {
        $this->diceGame = $diceGame;
    }

    public function broadcastOn()
    {
        return new Channel('dice-games');
    }
}