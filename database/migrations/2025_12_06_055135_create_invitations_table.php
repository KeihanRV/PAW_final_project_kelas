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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_name'); // 1. Acara Apa
            $table->date('event_date');   // 2. Tanggal
            $table->time('event_time');   // 2. Waktu
            $table->string('location');   // 4. Tempat
            $table->string('poster_path')->nullable(); // 5. Poster
            $table->text('message_body')->nullable();  // 5. Surat/Pesan Tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
