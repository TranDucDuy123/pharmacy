<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nha_cung_cap', function (Blueprint $table) {
            $table->id();
            $table->string('ma_ncc', 50)->unique()->comment('VD: NCC001');
            $table->string('ten_ncc');
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('ma_so_thue', 50)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->boolean('trang_thai')->default(1)->comment('1: Đang hợp tác, 0: Ngừng');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nha_cung_cap');
    }
};