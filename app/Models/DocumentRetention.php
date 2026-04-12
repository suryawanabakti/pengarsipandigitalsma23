<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRetention extends Model
{
    protected $fillable = [
        'document_id',
        'retention_status',
        'expiry_date'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
