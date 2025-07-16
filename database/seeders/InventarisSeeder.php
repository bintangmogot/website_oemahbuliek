<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user Admin dan Pegawai untuk dicatat di riwayat
        $admin = User::where('role', 'admin')->first();
        $pegawai = User::where('role', 'pegawai')->first();

        if (!$admin || !$pegawai) {
            $this->command->error('Pastikan ada setidaknya satu Admin dan satu Pegawai di database sebelum menjalankan seeder ini.');
            return;
        }

        // Buat 15 jenis bahan baku menggunakan factory
        $bahanBakus = BahanBaku::factory(15)->create();
        $this->command->info('15 Bahan Baku berhasil dibuat.');

        foreach ($bahanBakus as $bahan) {
            // Untuk setiap bahan baku, kita buat riwayat transaksinya
            $this->command->info("Membuat riwayat untuk: {$bahan->nama}");
            
            // Gunakan DB Transaction untuk memastikan data konsisten
            DB::transaction(function () use ($bahan, $admin, $pegawai) {
                $stokSaatIni = 0;
                $hargaBeliTerakhir = 0;

                // Buat 1 transaksi masuk sebagai data awal
                $kuantitasMasuk = rand(5000, 10000);
                $hargaBeliTerakhir = rand(50, 200) * 100; // Harga antara 5rb - 20rb
                
                RiwayatStok::factory()->create([
                    'bahan_baku_id' => $bahan->id,
                    'user_id' => $admin->id,
                    'tipe_mutasi' => 'masuk',
                    'kuantitas' => $kuantitasMasuk,
                    'harga_satuan' => $hargaBeliTerakhir,
                    'tanggal' => now()->subMonths(6),
                ]);
                $stokSaatIni += $kuantitasMasuk;

                // Buat 5 sampai 15 transaksi acak setelahnya
                for ($i = 0; $i < rand(5, 15); $i++) {
                    $tipe = ['masuk', 'produksi', 'rusak'][rand(0, 2)];
                    $user = [$admin->id, $pegawai->id][rand(0, 1)];

                    if ($tipe === 'masuk') {
                        $kuantitas = rand(1000, 5000);
                        $hargaBeliTerakhir = $hargaBeliTerakhir + rand(-500, 500); // Harga berfluktuasi
                        
                        RiwayatStok::factory()->create([
                            'bahan_baku_id' => $bahan->id,
                            'user_id' => $user,
                            'tipe_mutasi' => 'masuk',
                            'kuantitas' => $kuantitas,
                            'harga_satuan' => $hargaBeliTerakhir,
                        ]);
                        $stokSaatIni += $kuantitas;
                    } else { // Jika produksi atau rusak
                        // Pastikan stok cukup sebelum mengurangi
                        if ($stokSaatIni > 500) {
                            $kuantitas = -rand(100, 500); // Kuantitas keluar dibuat negatif
                            
                            RiwayatStok::factory()->create([
                                'bahan_baku_id' => $bahan->id,
                                'user_id' => $user,
                                'tipe_mutasi' => $tipe,
                                'kuantitas' => $kuantitas,
                                'harga_satuan' => null,
                            ]);
                            $stokSaatIni += $kuantitas;
                        }
                    }
                }

                // Setelah semua riwayat dibuat, update stok terkini di tabel bahan_baku
                $bahan->stok_terkini = $stokSaatIni;
                $bahan->save();
            });
        }
    }
}
