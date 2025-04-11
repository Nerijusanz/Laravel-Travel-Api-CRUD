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
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('travel_id')->constrained('travels')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->integer('price');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name','deleted_at']);
            $table->index(['user_id','travel_id']);

        });

    }


    public function down(): void
    {
        Schema::dropIfExists('tours');
    }

};
