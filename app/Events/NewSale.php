<?php

namespace App\Events;

use App\Models\Desk;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSale implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sale;
    private $desk;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public $deskId)
    {
        $this->desk = Desk::query()->find($deskId);
        $this->sale = $this->desk->sale() ?
            collect($this->desk->sale())->put('total', $this->desk->sale()?->details()->get()->sum(fn ($detail) => $detail->quantity * $detail->price))->all()
            : null;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['SaleChannel.'.$this->desk->organization_id];
    }
}
