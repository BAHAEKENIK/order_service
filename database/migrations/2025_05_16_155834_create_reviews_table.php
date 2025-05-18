<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_reviews_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Une review est liée à une demande de service spécifique
            $table->foreignId('service_request_id')->constrained('service_requests')->onDelete('cascade')->unique(); // Assure une seule review par demande
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade'); // Qui a écrit l'avis
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Qui a reçu l'avis
            $table->unsignedTinyInteger('rating'); // Note de 1 à 5
            $table->text('comment')->nullable();
            $table->boolean('is_moderated')->default(false); // Si l'admin a vérifié/modéré
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
