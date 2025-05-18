<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_admin_reply_to_contact_us_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_us_messages', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('admin_notes');
            $table->timestamp('replied_at')->nullable()->after('admin_reply');
        });
    }

    public function down(): void
    {
        Schema::table('contact_us_messages', function (Blueprint $table) {
            $table->dropColumn(['admin_reply', 'replied_at']);
        });
    }
};
