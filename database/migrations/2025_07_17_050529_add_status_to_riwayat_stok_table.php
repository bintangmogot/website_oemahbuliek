<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            // Tambahkan kolom status setelah 'keterangan'
            $table->string('status')->default('pending')->after('keterangan');
        });
    }
    public function down(): void {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};