<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Add the new enum value first, migrate existing rows, then remove old enum value.
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','pendaftaran','plp','nakes','dokter') NOT NULL");
            DB::table('users')->where('role', 'plp')->update(['role' => 'nakes']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','pendaftaran','nakes','dokter') NOT NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::table('users')->where('role', 'plp')->update(['role' => 'nakes']);
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin','admin','pendaftaran','nakes','dokter'))");
            return;
        }

        DB::table('users')->where('role', 'plp')->update(['role' => 'nakes']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','pendaftaran','plp','nakes','dokter') NOT NULL");
            DB::table('users')->where('role', 'nakes')->update(['role' => 'plp']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','pendaftaran','plp','dokter') NOT NULL");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::table('users')->where('role', 'nakes')->update(['role' => 'plp']);
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin','admin','pendaftaran','plp','dokter'))");
            return;
        }

        DB::table('users')->where('role', 'nakes')->update(['role' => 'plp']);
    }
};
