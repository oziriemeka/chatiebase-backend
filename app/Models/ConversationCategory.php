<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'organization_id'];

    /**
     * Relationship to organization.
     */
    public function organization()
    {
        return $this->belongsTo(OrganizationSettings::class);
    }

    /**
     * Scope for default categories.
     */
    public function scopeDefault($query)
    {
        return $query->whereNull('organization_id');
    }

    /**
     * Scope for organization-specific categories.
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
