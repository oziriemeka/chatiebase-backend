<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupedConversationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $groupedMessages = [];

        // The collection is expected to be grouped by minute keys.
        foreach ($this as $minute => $messages) {
            $groupedMessages[] = [
                'minute'   => $minute,
                'messages' => ConversationMessageResource::collection($messages)
            ];
        }

        return $groupedMessages;
    }
}
