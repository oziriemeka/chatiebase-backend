<?php

namespace App\Http\Resources;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WidgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'property_id' => $this->id_alias,
            'website_name' => $this->website_name,
            'website_domain' => $this->website_domain,
            'website_icon' => $this->website_icon,
            'widget_code' => UtilityHelper::getWidgetCode($this->id_alias),
            'contact_information' => [
                'email' => $this->widgetContactInformation->email,
                'phone' => $this->widgetContactInformation->phone,
                'messenger' => $this->widgetContactInformation->messenger,
                'telegram' => $this->widgetContactInformation->telegram,
                'twitter' => $this->widgetContactInformation->twitter,
                'whatsapp' => $this->widgetContactInformation->whatsapp,
                'instagram' => $this->widgetContactInformation->instagram,
            ],
            'advance_customization' => [
                'chat_appearance' => json_decode($this->widgetAdvanceCustomization->chat_appearance),
                'chat_visibility' => json_decode($this->widgetAdvanceCustomization->chat_visibility),
                'chat_behaviour' => json_decode($this->widgetAdvanceCustomization->chat_behaviour),
                'chat_restrictions' => json_decode($this->widgetAdvanceCustomization->chat_restrictions),
                'chat_security' => json_decode($this->widgetAdvanceCustomization->chat_security),
            ],
            'widget_addon' => [
                'enable_emojis' => $this->widgetAddon->enable_emojis === 1,
                'prevent_profanity' => $this->widgetAddon->prevent_profanity === 1,
            ]
        ];
    }
}
