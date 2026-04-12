<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_view')->default(true)->after('role_id');
            $table->boolean('can_upload')->default(false)->after('can_view');
            $table->boolean('can_edit')->default(false)->after('can_upload');
            $table->boolean('can_delete')->default(false)->after('can_edit');
            $table->boolean('can_download')->default(true)->after('can_delete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_view', 'can_upload', 'can_edit', 'can_delete', 'can_download']);
        });
    }
};
