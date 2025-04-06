<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "timezone" => $this->timezone,
            "website" => $this->website,
            "country" =>  $this->country
        ];
    }
}
