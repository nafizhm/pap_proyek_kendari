<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model
{
    public static function receiptCompany(?int $locationId = null): array
    {
        $company = null;
        if ($locationId !== null) {
            $company = \Illuminate\Support\Facades\DB::table('perusahaan as p')
                ->join('lokasi_kavling_perusahaan as lp', 'lp.id_perusahaan', '=', 'p.id')
                ->where('lp.id_lokasi', $locationId)->orderBy('lp.id')->select('p.*')->first();
        }
        $config = static::first();

        return [
            'nama_perusahaan' => $company?->nama_perusahaan ?? $config?->nama_perusahaan ?? config('app.name'),
            'alamat' => $company?->alamat_perusahaan ?? $config?->alamat ?? '',
            'telp' => $company?->telp_perusahaan ?? $config?->telp ?? '',
            'kota' => $company?->kota_penandatangan ?? '',
            'nama_penandatangan' => $company?->nama_penandatangan ?? '',
            'jabatan_penandatangan' => $company?->jabatan_penandatangan ?? '',
        ];
    }

    protected $table = 'konfigurasi';
    public $timestamps = false;

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'email',
        'telp',
        'kota_penandatanganan',
        'nama_penandatangan_1',
        'jabatan_penandatangan_1',
    ];
}
