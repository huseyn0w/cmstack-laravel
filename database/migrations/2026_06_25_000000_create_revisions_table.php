<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Morph to the translation model (PostTranslation / PageTranslation):
            // the editable content lives there, and its row id is stable across
            // edits, so all revisions for one post+locale share a revisionable_id.
            $table->string('revisionable_type');
            $table->unsignedBigInteger('revisionable_id');
            // The editor who triggered the snapshot (nullable for system writes).
            $table->unsignedBigInteger('user_id')->nullable();
            // Immutable JSON snapshot of the translation's pre-edit attributes.
            $table->json('data');
            $table->timestamp('created_at')->nullable();

            $table->index(['revisionable_type', 'revisionable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
