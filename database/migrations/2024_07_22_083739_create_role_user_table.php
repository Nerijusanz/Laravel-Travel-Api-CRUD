<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropForeign(['role_id']);
        $table->dropForeign(['user_id']);
        Schema::dropIfExists('role_user');
    }
};
