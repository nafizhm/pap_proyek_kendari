<?php

namespace App\Services;

use App\Models\Konfigurasi;
use Illuminate\Support\Facades\DB;

class ApplicationBranding
{
    private const MEDIA_TYPES = [
        'logo' => 'Logo Aplikasi',
        'favicon' => 'fav icon',
    ];

    public function identity(): array
    {
        $media = $this->media();
        $logo = $this->mediaUrl('logo', $media['logo']);

        return [
            'name' => Konfigurasi::value('nama_perusahaan') ?: 'Pengelolaan Proyek',
            'logo' => $logo ?? asset('assets/img/logo-pap.png'),
            'favicon' => $this->mediaUrl('favicon', $media['favicon']) ?? $logo ?? asset('assets/img/logo-pap.png'),
        ];
    }

    public function mediaFile(string $type): ?string
    {
        if (! isset(self::MEDIA_TYPES[$type])) {
            return null;
        }

        return $this->resolveFile($this->media()[$type]);
    }

    private function media(): array
    {
        $records = DB::table('konfigurasi_media')
            ->where('stt_aktif', 1)
            ->whereIn('jenis_data', array_values(self::MEDIA_TYPES))
            ->orderBy('id')
            ->get();

        $media = [];
        foreach (self::MEDIA_TYPES as $type => $label) {
            $media[$type] = $records->firstWhere('jenis_data', $label)?->nama_file;
        }

        return $media;
    }

    private function mediaUrl(string $type, ?string $filename): ?string
    {
        $path = $this->resolveFile($filename);
        if (! $path) {
            return null;
        }

        return route('branding.media', ['type' => $type, 'v' => sha1($filename.'|'.filemtime($path))]);
    }

    private function resolveFile(?string $filename): ?string
    {
        // Only the configured image filename may be served, never an arbitrary path.
        if (! $filename || preg_match('/[\\\\\/]/', $filename)
            || ! in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'webp', 'gif', 'ico'], true)) {
            return null;
        }

        foreach (array_unique([config('branding.media_path'), public_path('config_media')]) as $directory) {
            if (! $directory) {
                continue;
            }

            $root = realpath($directory);
            $path = realpath($directory.DIRECTORY_SEPARATOR.$filename);
            if ($root && $path && dirname($path) === $root && is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
