<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = json_decode(file_get_contents(database_path('project-schema.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($tables as $table => $definition) {
            if (Schema::hasTable($table)) {
                continue;
            }
            DB::unprepared($definition['sql']);
            foreach (array_chunk($definition['rows'], 100) as $rows) {
                DB::table($table)->insert($rows);
            }
        }

        // Grant initial access only to existing administrators, never source user IDs.
        $admins = DB::table('users')->where('id_role', 1)->where('status', 'AKTIF')->pluck('id');
        foreach ($admins as $userId) {
            foreach (DB::table('menu_proyek')->get() as $menu) {
                if (!DB::table('hak_akses_proyek')->where('id_user', $userId)->where('id_menu', $menu->id)->exists()) {
                    DB::table('hak_akses_proyek')->insert([
                        'id_user' => $userId, 'id_menu' => $menu->id,
                        'lihat' => $menu->lihat, 'tambah' => $menu->tambah,
                        'edit' => $menu->edit, 'hapus' => $menu->hapus,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Rollback otomatis dinonaktifkan: tabel proyek dapat berisi data operasional. Gunakan backup dan tinjauan manual.');
    }
};
