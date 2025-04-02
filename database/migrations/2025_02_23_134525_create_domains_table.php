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
        Schema::create('domains', function (Blueprint $table) {
            $table->id('domain_id'); // Первичный ключ с именем domain_id
            $table->string('fqdn')->unique(); // Полное доменное имя
            $table->string('display_name'); // Отображаемое имя
            $table->string('avatar_url')->nullable(); // URL аватара
            $table->string('gdn'); // GDN (Gaia Domain Name)

            $table->unsignedBigInteger('throughputs')->default(0);
            $table->decimal('total_earned', 12, 4)->default(0)->unsigned();
            $table->unsignedInteger('total_running_nodes')->default(0);
            $table->unsignedBigInteger('throughputs_change'); // Разница в throughputs
            $table->decimal('total_earned_change', 12, 4)->default(0)->unsigned(); // Разница в total_earned
            $table->integer('total_running_nodes_change')->default(0); // Разница в total_running_nodes

            $table->string('domain_name'); // Имя домена
            $table->text('system_prompt')->nullable(); // Системный промпт
            $table->text('description')->nullable(); // Описание
            $table->string('hosting_type'); // Тип хостинга
            $table->string('approval_method'); // Метод одобрения
            $table->string('llm_requirements'); // Требования к LLM
            $table->string('server_configuration'); // Конфигурация сервера
            $table->string('domain_tier'); // Уровень домена
            $table->integer('initial_stake_tokens'); // Начальные стейк-токены
            $table->string('owner_id'); // ID владельца
            $table->string('owner_wallet_address'); // Адрес кошелька владельца
            $table->string('status')->nullable(); // Статус
            $table->string('region')->nullable(); // Регион
            $table->timestamp('created'); // Создана
            $table->timestamp('updated'); // обновлена
            $table->timestamps(); // created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
