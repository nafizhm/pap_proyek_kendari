<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApplicationBrandingTest extends TestCase
{
    private string $mediaDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        // This test never writes to the shared PAP database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('konfigurasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
        });
        Schema::create('konfigurasi_media', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_data');
            $table->string('nama_file');
            $table->boolean('stt_aktif');
        });

        $this->mediaDirectory = storage_path('framework/testing-branding-'.bin2hex(random_bytes(6)));
        mkdir($this->mediaDirectory);
        config(['branding.media_path' => $this->mediaDirectory]);
        file_put_contents($this->mediaDirectory.'/logo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aWQAAAABJRU5ErkJggg=='));
        DB::table('konfigurasi')->insert(['nama_perusahaan' => 'Perusahaan dari Database']);
        DB::table('konfigurasi_media')->insert([
            'jenis_data' => 'Logo Aplikasi', 'nama_file' => 'logo.png', 'stt_aktif' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        if (isset($this->mediaDirectory)) {
            unlink($this->mediaDirectory.'/logo.png');
            rmdir($this->mediaDirectory);
        }

        parent::tearDown();
    }

    public function test_login_reflects_company_name_changes_from_database(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Perusahaan dari Database')->assertDontSee('Nazura');

        DB::table('konfigurasi')->update(['nama_perusahaan' => 'Nama Perusahaan Terbaru']);
        $this->get(route('login'))->assertOk()->assertSee('Nama Perusahaan Terbaru')->assertDontSee('Perusahaan dari Database');
    }

    public function test_configured_media_is_served_from_shared_upload_directory(): void
    {
        $branding = app(\App\Services\ApplicationBranding::class)->identity();
        $this->assertStringContainsString('/branding/logo?v=', $branding['logo']);
        $this->assertSame($branding['logo'], $branding['favicon']);
        $this->get($branding['logo'])->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');

        DB::table('konfigurasi_media')->update(['stt_aktif' => 0]);
        $this->get(route('branding.media', ['type' => 'logo']))->assertNotFound();
    }

    public function test_media_cannot_serve_paths_outside_the_configured_directory(): void
    {
        foreach (['../logo.png', '..\\logo.png', 'file.php'] as $filename) {
            DB::table('konfigurasi_media')->update(['nama_file' => $filename]);
            $this->get(route('branding.media', ['type' => 'logo']))->assertNotFound();
        }
    }
}
