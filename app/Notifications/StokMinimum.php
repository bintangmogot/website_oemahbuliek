<?php

namespace App\Notifications;

use App\Models\BahanBaku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StokMinimum extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $bahanBaku;

    public function __construct(BahanBaku $bahanBaku)
    {
        $this->bahanBaku = $bahanBaku;
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Format notifikasi untuk disimpan di database.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            // Data untuk tampilan dinamis di halaman notifikasi
            'icon'    => 'fas fa-box-open text-warning', // Font Awesome icon class
            'title'   => 'Peringatan Stok Minimum',
            'body'    => "Stok untuk {$this->bahanBaku->nama} telah mencapai batas kritis. Sisa {$this->bahanBaku->stok_terkini} {$this->bahanBaku->satuan_label}.",
            'url'     => route('bahan-baku.show', $this->bahanBaku->id),
            
            // Data spesifik untuk referensi jika dibutuhkan
            'bahan_baku_id' => $this->bahanBaku->id,
        ];
    }
    
    /**
     * Format notifikasi untuk di-broadcast (dikirim via Pusher).
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => '🚨 Stok Minimum Tercapai!',
            'body' => "Stok {$this->bahanBaku->nama} hanya tersisa {$this->bahanBaku->stok_terkini} {$this->bahanBaku->satuan_label}.",
            'url' => route('bahan-baku.show', $this->bahanBaku->id),
        ]);
    }
}