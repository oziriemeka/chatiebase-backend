<?php

namespace App\Http\Resources;

use App\Models\ApplicationChatSound;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserApplicationSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "theme_color" => $this->theme_color,
            "enable_browser_notification" => $this->enable_browser_notification === 1,
            "language" => $this->language,
            "new_chat_sound" => $this->getSoundData($this->new_chat_sound),
            "existing_chat_sound" => $this->getSoundData($this->existing_chat_sound),
            "new_website_visitor_sound" => $this->getSoundData($this->existing_chat_sound),
            "enable_sound_for_new_visitor" => $this->enable_sound_for_new_visitor === 1
         ];
    }

    private function getSoundData($id)
    {
        $chatSound = ApplicationChatSound::where('id', $id)->first();
        return new ChatSoundResource($chatSound);
    }
}
