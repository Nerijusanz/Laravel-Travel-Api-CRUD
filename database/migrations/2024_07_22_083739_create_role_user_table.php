<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private string $tableName = 'role_user';

    private string $columnUserId = 'user_id';

    private string $columnRoleId = 'role_id';


    public function up(): void
    {

        Schema::create($this->tableName, function (Blueprint $table) {

            $table->foreignId($this->columnUserId)->constrained()->cascadeOnDelete();
            $table->foreignId($this->columnRoleId)->constrained()->cascadeOnDelete();

            $table->index($this->columnUserId, $this->columnIndex($this->tableName,$this->columnUserId) );
            $table->index($this->columnRoleId, $this->columnIndex($this->tableName,$this->columnRoleId) );

        });

    }


    public function down(): void
    {

        Schema::table($this->tableName, function (Blueprint $table) {

            $table->dropForeign([$this->columnUserId]);
            $table->dropForeign([$this->columnRoleId]);
            $table->dropIndex( $this->columnIndex($this->tableName,$this->columnUserId) );
            $table->dropIndex( $this->columnIndex($this->tableName,$this->columnRoleId) );

        });

        Schema::dropIfExists($this->tableName);

    }


    private function columnIndex(string $table, string $column): string
    {
        return $table . '_' . $column . '_idx';
    }

};
