<?php
namespace App\Events;

use App\Models\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewCreated implements ShouldBroadcast
{
    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function broadcastOn()
    {
        return new Channel('reviews');
    }

    public function broadcastAs()
    {
        return 'review.created';
    }
}
