<?php

namespace App\Http\Resources;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ContactListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'conversation_id' => $this->conversation->conversation_id,
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'avatar' => asset("storage/customer/avatar/". $this->avatar),
            'last_message' => $this->latestMessage ? Str::limit($this->latestMessage->message, 100) : "Start conversation",
            'last_message_time' =>
                UtilityHelper::getUserDateTimeFormat(
                $this->latestMessage->created_at,
                auth()->user()->organizationSettings->timezone
            )->diffForHumans()
        ];
    }
}
