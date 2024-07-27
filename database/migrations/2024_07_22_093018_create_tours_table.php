<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_id')->constrained('travels')->cascadeOnDelete();
            $table->string('name');
            $table->integer('price');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id','tours_user_id_idx');
            $table->index('travel_id','tours_travel_id_idx');
        });


    }


    public function down(): void
    {
        $table->dropForeign(['user_id']);
        $table->dropForeign(['travel_id']);

        Schema::dropIfExists('tours');
    }
};
