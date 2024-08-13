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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("actor_id")
                ->constrained(table: "users", indexName: "activities_actor_id")
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string("token"); // untuk membedakan kegiatan transaksi
            $table->string("message");
            $table->enum("type", ["Create", "Update", "Delete"]);
            $table->boolean("is_read")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
