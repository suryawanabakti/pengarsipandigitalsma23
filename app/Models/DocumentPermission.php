<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPermission extends Model
{
    protected $fillable = [
        'document_id',
        'role_id',
        'can_view',
        'can_upload',
        'can_edit',
        'can_delete',
        'can_download'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
