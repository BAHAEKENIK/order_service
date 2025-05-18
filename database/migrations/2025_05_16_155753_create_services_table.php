<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('Provider ID')->constrained('users')->onDelete('cascade'); // Le prestataire qui offre ce service
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('address')->nullable(); // Adresse spécifique du service si différente de celle du prestataire
            $table->string('city')->nullable(); // Ville où le service est offert
            $table->decimal('base_price', 10, 2)->nullable(); // Prix de base du service
            $table->string('image_path')->nullable(); // Image illustrative du service
            $table->string('status')->default('available'); // 'available', 'unavailable'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
