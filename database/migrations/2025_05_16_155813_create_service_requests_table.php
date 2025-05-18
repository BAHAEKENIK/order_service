<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_service_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null'); // Service spécifique demandé (optionnel)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Catégorie de la demande

            $table->text('description'); // Description du besoin par le client
            $table->string('address'); // Adresse où le service est requis
            $table->string('city'); // Ville où le service est requis
            $table->decimal('proposed_budget', 10, 2)->nullable(); // Budget proposé par le client
            $table->dateTime('desired_date_time')->nullable(); // Date et heure souhaitées
            $table->string('status')->default('pending'); // 'pending', 'accepted', 'refused', 'in_progress', 'completed', 'cancelled_by_client', 'cancelled_by_provider'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
