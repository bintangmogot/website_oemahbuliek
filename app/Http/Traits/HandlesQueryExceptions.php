<?php

namespace App\Http\Traits;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

trait HandlesQueryExceptions
{
    /**
     * Menangani error QueryException saat menghapus data.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $redirectRoute
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function safeDelete($model, string $redirectRoute)
    {
        try {
            $model->delete();
            return null; // Mengembalikan null jika berhasil dihapus
        } catch (QueryException $e) {
            // Kode '23000' adalah untuk integrity constraint violation (foreign key)
            if ($e->getCode() === '23000') {
                $errorMessage = 'Data tidak dapat dihapus karena masih digunakan oleh data lain.';
                
                return redirect()->route($redirectRoute)
                    ->with('error', $errorMessage);
            }

            // Untuk error database lainnya, catat dan tampilkan pesan generik
            Log::error("Database Error: " . $e->getMessage());
            return redirect()->route($redirectRoute)
                ->with('error', 'Terjadi kesalahan pada database.');
        }
    }
}