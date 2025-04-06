<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->message_id,
            "message" =>  $this->is_deleted === 0 ? $this->message : "message deleted",
            "action_type" =>  $this->action_type,
            "attachment" => $this->attachement ? new AttachmentResource($this->attachement) : null,
            "parent_id" => $this->parent_id,
            "is_deleted" => $this->is_deleted === 1,
            "sender" => $this->getSenderInfo(),
            "created_at" =>  Carbon::parse($this->created_at)->format('M-d-y H:i:s')
        ];
    }

    private function getSenderInfo()
    {
        if($this->sender === "operator"){
            return [
                'id' => $this->operator->user_id,
                'sender' => $this->sender,
                'name' => $this->operator->name,
                'avatar' => $this->operator->avatar
            ];
        } else {
            return [
                'id' => $this->customer->customer_id,
                'sender' => $this->sender,
                'name' => $this->customer->name,
                'avatar' => asset('storage/customer/avatar/' . $this->customer->avatar)
            ];
        }
    }
}
