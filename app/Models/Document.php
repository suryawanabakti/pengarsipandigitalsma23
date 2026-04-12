<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'file_type',
        'document_number',
        'category_id',
        'unit_id',
        'uploaded_by',
        'stage',
        'status',
        'archive_type',
        'document_date',
        'publish_at'
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'document_tag');
    }

    public function permissions()
    {
        return $this->hasMany(DocumentPermission::class);
    }

    public function retention()
    {
        return $this->hasOne(DocumentRetention::class);
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
