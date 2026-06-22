<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channels;
    
    public $reload = true;

    /**
     * Create a new event instance.
     * 
     * @param array $channels Array of channel names (e.g. ['admins.online', 'company.1'])
     */
    public function __construct(array $channels)
    {
        $this->channels = $channels;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $broadcastChannels = [];
        foreach ($this->channels as $channel) {
            if ($channel === 'admins.online') {
                $broadcastChannels[] = new PresenceChannel($channel);
            } else {
                $broadcastChannels[] = new PrivateChannel($channel);
            }
        }
        return $broadcastChannels;
    }
}
