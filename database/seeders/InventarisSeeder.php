<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        RiwayatStok::truncate();
        BahanBaku::truncate();

        // Aktifkan kembali foreign key check
        Schema::enableForeignKeyConstraints();
        $this->command->info('Tabel riwayat_stok dan bahan_baku telah dikosongkan.');
        // ---------------------------------------------------------

        $admin = User::where('role', 'admin')->first();
        $pegawai = User::where('role', 'pegawai')->first();

        if (!$admin || !$pegawai) {
            $this->command->error('Pastikan ada setidaknya satu Admin dan satu Pegawai di database sebelum menjalankan seeder ini.');
            return;
        }

        $bahanBakus = BahanBaku::factory(15)->create();
        $this->command->info('15 Bahan Baku berhasil dibuat.');

        foreach ($bahanBakus as $bahan) {
            $this->command->info("Membuat riwayat untuk: {$bahan->nama}");
            
            DB::transaction(function () use ($bahan, $admin, $pegawai) {
                $stokSaatIni = 0;
                $hargaBeliTerakhir = 0;

                // PERUBAHAN: Kuantitas dan harga awal dibuat lebih bulat
                $kuantitasMasuk = rand(50, 100) * 100; // Stok awal antara 5.000 - 10.000 (kelipatan 100)
                $hargaBeliTerakhir = rand(10, 30) * 1000; // Harga awal antara 10.000 - 30.000 (kelipatan 1000)
                
                RiwayatStok::factory()->create([
                    'bahan_baku_id' => $bahan->id, 'user_id' => $admin->id,
                    'tipe_mutasi' => 'masuk', 'kuantitas' => $kuantitasMasuk,
                    'harga_satuan' => $hargaBeliTerakhir, 'status' => 'approved',
                ]);
                $stokSaatIni += $kuantitasMasuk;

                for ($i = 0; $i < rand(10, 20); $i++) {
                    $user = [$admin, $pegawai][rand(0, 1)];
                    $tipe = ['masuk', 'produksi', 'rusak'][rand(0, 2)];

                    if ($user->role === 'admin') {
                        if ($tipe === 'masuk') {
                            // PERUBAHAN: Kuantitas dan fluktuasi harga lebih bulat
                            $kuantitas = rand(10, 50) * 100; // Kuantitas masuk 1.000 - 5.000 (kelipatan 100)
                            $hargaBeliTerakhir += rand(-5, 5) * 100; // Fluktuasi harga kelipatan 100
                            
                            RiwayatStok::factory()->create([
                                'bahan_baku_id' => $bahan->id, 'user_id' => $user->id,
                                'tipe_mutasi' => 'masuk', 'kuantitas' => $kuantitas,
                                'harga_satuan' => $hargaBeliTerakhir, 'status' => 'approved',
                            ]);
                            $stokSaatIni += $kuantitas;
                        } else {
                            if ($stokSaatIni > 500) {
                                // PERUBAHAN: Kuantitas keluar lebih bulat
                                $kuantitas = - (rand(1, 5) * 100); // Kuantitas keluar 100 - 500 (kelipatan 100)
                                RiwayatStok::factory()->create([
                                    'bahan_baku_id' => $bahan->id, 'user_id' => $user->id,
                                    'tipe_mutasi' => $tipe, 'kuantitas' => $kuantitas,
                                    'harga_satuan' => null, 'status' => 'approved',
                                ]);
                                $stokSaatIni += $kuantitas;
                            }
                        }
                    } else { // Jika Pegawai, transaksi menjadi pending/rejected
                        $kuantitas = ($tipe === 'masuk') ? rand(10, 50) * 100 : -(rand(1, 5) * 100);
                        
                        RiwayatStok::factory()->create([
                            'bahan_baku_id' => $bahan->id, 'user_id' => $user->id,
                            'tipe_mutasi' => $tipe, 'kuantitas' => $kuantitas,
                            'harga_satuan' => ($tipe === 'masuk') ? $hargaBeliTerakhir + rand(-5, 5) * 100 : null,
                            'status' => ['pending', 'rejected'][rand(0, 1)],
                        ]);
                    }
                }

                $bahan->stok_terkini = $stokSaatIni;
                $bahan->save();
            });
        }
    }
}