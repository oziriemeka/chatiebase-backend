<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model
{
    public function organization()
    {
        return $this->belongsTo(OrganizationSettings::class, 'organization_id', 'id');
    }
}
