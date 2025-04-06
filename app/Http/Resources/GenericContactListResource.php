<?php

namespace App\Http\Resources;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenericContactListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'avatar' => asset("storage/customer/avatar/". $this->avatar),
            'assigned_by' => $this->assigned_by_id,
            'created_at' => UtilityHelper::getUserDateTimeFormat(
                $this->latestMessage->created_at,
                auth()->user()->organizationSettings->timezone
            )->diffForHumans()
        ];
    }
}
