<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrower_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained('borrowers')->onDelete('cascade');
            $table->foreignId('checklist_id')->constrained('checklists')->onDelete('cascade');
            $table->string('checklist_item_name'); // Name of the specific checklist item
            $table->enum('status', ['Document Pending', 'Document Clear'])->default('Document Pending');
            $table->string('file_path')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('status_updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure unique combination of borrower, checklist and item
            $table->unique(['borrower_id', 'checklist_id', 'checklist_item_name'], 'bc_borrower_checklist_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrower_checklists');
    }
};
