<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * A customer has exactly one conversation.
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function messages()
    {
        return $this->hasManyThrough(ConversationMessage::class, Conversation::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latest('created_at');
    }

}
