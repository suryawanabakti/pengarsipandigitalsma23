<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'unit_id',
        'can_view',
        'can_upload',
        'can_edit',
        'can_delete',
        'can_download'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_upload' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_download' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
