<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE `thanh_toan` MODIFY `trang_thai` ENUM('cho_thanh_toan','chua_thanh_toan','da_thanh_toan','that_bai','da_hoan_tien') NOT NULL DEFAULT 'chua_thanh_toan'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE `thanh_toan` MODIFY `trang_thai` ENUM('cho_thanh_toan','da_thanh_toan','that_bai','da_hoan_tien') NOT NULL DEFAULT 'cho_thanh_toan'"
        );
    }
};