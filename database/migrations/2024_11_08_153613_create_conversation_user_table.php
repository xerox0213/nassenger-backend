<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'conversation_id']);
            $table->boolean('is_admin');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('last_deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
    }
};
