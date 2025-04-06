<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "attachment_id" => $this->attachment_id,
            "message_id" => $this->message_id,
            "content" => $this->content,
            "extension" => $this->extension,
            "size" => $this->size
        ];
    }
}
