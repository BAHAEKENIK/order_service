<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_provider_details_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique(); 
            $table->text('professional_description')->nullable();
            $table->json('certificates')->nullable();
            $table->decimal('average_rating', 2, 1)->nullable()->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_details');
    }
};
