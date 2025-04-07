<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private string $tableName = 'travels';

    private string $columnUserId = 'user_id';


    public function up(): void
    {

        Schema::create('travels', function (Blueprint $table)
        {

            $table->id();
            $table->foreignId($this->columnUserId)->constrained()->cascadeOnDelete();
            $table->boolean('is_public')->default(false);
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('number_of_days');
            $table->unsignedTinyInteger('number_of_nights');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();


            $table->index($this->columnUserId,$this->columnIndex($this->tableName,$this->columnUserId) );

        });

    }


    public function down(): void
    {

        Schema::table($this->tableName, function (Blueprint $table) {

            $table->dropForeign([$this->columnUserId]);
            $table->dropIndex( $this->columnIndex($this->tableName,$this->columnUserId) );

        });

        Schema::dropIfExists('travels');

    }


    private function columnIndex(string $table, string $column): string
    {
        return $table . '_' . $column . '_idx';
    }

};
