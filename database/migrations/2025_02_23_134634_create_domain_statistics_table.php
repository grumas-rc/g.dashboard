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
        Schema::create('domain_statistics', function (Blueprint $table) {
            $table->id('domain_statistic_id');
            $table->unsignedBigInteger('domain_id');
            $table->unsignedInteger('total_running_nodes')->default(0);
            $table->unsignedBigInteger('throughputs')->default(0);
            $table->decimal('total_earned', 12, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_statistics');
    }
};
