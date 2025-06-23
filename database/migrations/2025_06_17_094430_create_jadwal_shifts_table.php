<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalShiftsTable extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')
                  ->constrained('shift')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Add foreign key for user
            $table->foreignId('users_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->date('tanggal');
            $table->tinyInteger('status')->default(1)->comment('0=Cancelled, 1=Active, 2=Completed');
            $table->timestamps();

            // 1 pegawai tidak bisa memiliki shift yang sama di tanggal yang sama
            $table->unique(['users_id', 'shift_id', 'tanggal'], 'unique_user_shift_date');

            // Indexes
            $table->index('tanggal');
            $table->index('status');
            $table->index(['users_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');
    }
}
