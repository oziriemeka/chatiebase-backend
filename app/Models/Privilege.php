<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'organization_id'];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'privilege_permissions', // Correct pivot table name
            'privilege_id', // Foreign key on privilege_permissions table
            'permission_id'  // Foreign key on privilege_permissions table
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_privilege');
    }
}
