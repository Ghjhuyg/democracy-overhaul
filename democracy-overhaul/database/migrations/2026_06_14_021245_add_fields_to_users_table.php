<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Добавляем поля к существующей таблице users
            $table->string('full_name')->after('name')->nullable();
            $table->string('github_id')->unique()->nullable()->after('email');
            // Роль: 'voter' (голосующий), 'proposer' (предлагающий), 'both' (оба)
            $table->enum('role', ['voter', 'proposer', 'both'])->default('voter')->after('github_id');
            // Можно оставить поле 'name' как логин, 'full_name' для ФИО
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'github_id', 'role']);
        });
    }
};