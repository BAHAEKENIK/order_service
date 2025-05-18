<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->onDelete('cascade'); // Conversation liée à une demande
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Qui a envoyé
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // Qui doit recevoir
            $table->text('content');
            $table->timestamp('read_at')->nullable(); // Quand le destinataire a lu le message
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
