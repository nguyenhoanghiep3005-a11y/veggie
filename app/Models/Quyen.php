<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    protected $table = 'quyen';

    protected $primaryKey = 'ma_quyen';

    protected $fillable = ['ten'];

    // Lấy các vai trò đang có quyền này.
    public function vaiTros()
    {
        return $this->belongsToMany(
            VaiTro::class,
            'vai_tro_quyen',
            'ma_quyen',
            'ma_vai_tro'
        );
    }
}