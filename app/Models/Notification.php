<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'sender',
        'recipient',
        'channel_id',
        'status'
    ];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
