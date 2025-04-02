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
        Schema::create('domain_statistics_changes', function (Blueprint $table) {
            $table->id('change_id'); // Первичный ключ
            $table->unsignedBigInteger('domain_id'); // Внешний ключ на domains.domain_id
            $table->bigInteger('throughputs_change'); // Разница в throughputs
            $table->decimal('total_earned_change', 12, 4); // Разница в total_earned
            $table->integer('total_running_nodes_change'); // Разница в total_running_nodes
            $table->timestamps(); // created_at и updated_at

            // Внешний ключ
            $table->foreign('domain_id')->references('domain_id')->on('domains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_statistics_changes');
    }
};
