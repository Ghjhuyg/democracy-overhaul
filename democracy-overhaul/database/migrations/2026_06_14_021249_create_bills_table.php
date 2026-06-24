<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // кто предложил
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['rejected', 'accepted', 'standby', 'open'])->default('standby');
            $table->timestamp('voting_start_at')->nullable();   // дата начала голосования
            $table->timestamp('voting_end_at')->nullable();     // дата конца
            $table->timestamps();                               // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};