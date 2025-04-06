<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * A conversation belongs to one customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latest();
    }
}
