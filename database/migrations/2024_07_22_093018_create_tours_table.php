<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private string $tableName = 'tours';

    private string $tableTravel = 'travels';

    private string $columnUserId = 'user_id';

    private string $columnTravelId = 'travel_id';


    public function up(): void
    {

        Schema::create($this->tableName, function (Blueprint $table) {

            $table->id();
            $table->foreignId($this->columnUserId)->constrained()->cascadeOnDelete();
            $table->foreignId($this->columnTravelId)->constrained($this->tableTravel)->cascadeOnDelete();
            $table->string('name');
            $table->integer('price');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->timestamps();
            $table->softDeletes();


            $table->index($this->columnUserId,$this->columnIndex($this->tableName,$this->columnUserId) );
            $table->index($this->columnTravelId,$this->columnIndex($this->tableName,$this->columnTravelId) );

        });

    }


    public function down(): void
    {

        Schema::table($this->tableName, function (Blueprint $table) {

            $table->dropForeign([$this->columnUserId]);
            $table->dropForeign([$this->columnTravelId]);

            $table->dropIndex($this->columnIndex($this->tableName,$this->columnUserId) );
            $table->dropIndex($this->columnIndex($this->tableName,$this->columnTravelId) );

        });

        Schema::dropIfExists($this->tableName);

    }


    private function columnIndex(string $table, string $column): string
    {
        return $table . '_' . $column . '_idx';
    }

};
