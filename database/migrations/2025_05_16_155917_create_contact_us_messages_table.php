<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_contact_us_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_us_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nom de la personne (peut être non connectée)
            $table->string('email'); // Email de la personne
            $table->string('subject')->nullable();
            $table->text('message');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Si un utilisateur connecté envoie
            $table->string('status')->default('new'); // 'new', 'read', 'replied', 'archived'
            $table->text('admin_notes')->nullable(); // Notes internes pour l'admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_us_messages');
    }
};
