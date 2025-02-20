<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    public function GetWebsiteIconAttribute($value) {
        return asset('storage/widget/chatbox/'.$value);
    }


    public function WidgetContactInformation() {
        return $this->hasOne(WidgetContactInformation::class, 'widget_id', 'id');
    }

    public function WidgetAdvanceCustomization() {
        return $this->hasOne(WidgetAdvanceCustomization::class, 'widget_id', 'id');
    }

    public function WidgetAddon() {
        return $this->hasOne(WidgetAddon::class, 'widget_id', 'id');
    }

    public function Organization() {
        return $this->belongsTo(UserOrganization::class, 'organization_id', 'id');
    }
}
