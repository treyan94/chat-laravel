<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_room_user', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_room_id');
            $table->foreignId('user_id');

            $table->unique(['chat_room_id', 'user_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_user');
    }
};
