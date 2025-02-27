<?php

namespace App\Http\Resources;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WidgetSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "default_theme_options" => json_decode($this->default_theme_options),
            "theme_text_options" => json_decode($this->theme_text_options),
            "welcome_message_options" => json_decode($this->welcome_message_options),
            "background_image_options" => $this->parseBackgroundImageOptions($this->background_image_options),
            "countries" => $this->getCountries()
        ];
    }

    private function getCountries()
    {
        return CountryResource::collection(Country::get());
    }

    private function parseBackgroundImageOptions($background_image_options): array
    {

        $data = json_decode($background_image_options);
        $items = [];
        foreach ($data as $item){
            $items[] = [
                "id" => $item->id,
                "name" => $item->name,
                "src" => asset("/storage/widget/patterns/" . $item->src),
                "is_default" => $item->is_default
            ];
        }
        return $items;
    }


}
