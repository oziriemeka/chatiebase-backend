<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationMessage extends Model
{
    /**
     * A conversation message belongs to one conversation.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function attachment()
    {
        return $this->hasOne(ConversationAttachments::class, 'message_id', 'id');
    }
}
