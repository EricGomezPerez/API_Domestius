<?php

namespace App\Events;

use App\Models\Usuari;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usuari;
    public $message;

   
    public function __construct(Usuari $usuari, $message)
    {
        $this->usuari = $usuari;
        $this->message = $message;
    }

  
    public function broadcastOn(): Channel
    {
        return new Channel('chat');
    }

   
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}