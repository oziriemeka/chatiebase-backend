<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'group'];

    public function privileges()
    {
        return $this->belongsToMany(
            Privilege::class,
            'privilege_permissions', // Correct pivot table name
            'permission_id', // Foreign key on privilege_permissions table
            'privilege_id'  // Foreign key on privilege_permissions table
        );
    }
}
