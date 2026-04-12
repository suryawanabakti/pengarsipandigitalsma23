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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('document_number')->nullable();

            $table->foreignId('category_id')->constrained('document_categories');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('uploaded_by')->constrained('users');

            $table->enum('stage', ['draft', 'final', 'arsip']);
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak']);
            $table->enum('archive_type', ['dinamis', 'statis']);

            $table->date('document_date')->nullable();
            $table->timestamp('publish_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
