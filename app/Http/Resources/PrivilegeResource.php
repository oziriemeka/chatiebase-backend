<?php

namespace App\Http\Resources;

use App\Models\PrivilegePermission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivilegeResource extends JsonResource
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
            'can_delete' => $this->can_delete,
            'permissions' => $this->getSelectedPermission()
        ];
    }

    public function getSelectedPermission(){
        return $this->permissions()->pluck('permission_id');
    }
}
