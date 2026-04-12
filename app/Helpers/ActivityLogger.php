<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log a user activity.
     *
     * @param string $activity The type of activity (e.g., login, view, download, etc.)
     * @param string $description Detailed description of the action
     * @param int|null $documentId Associated document ID if any
     * @return void
     */
    public static function log($activity, $description, $documentId = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'document_id' => $documentId,
            'activity' => $activity,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
