<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddTeamMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'status' => ucfirst($this->status),
            'privilege' => $this->privilege,
            'member_since' => Carbon::parse($this->created_at)->format("d M, Y")
        ];
    }
}
