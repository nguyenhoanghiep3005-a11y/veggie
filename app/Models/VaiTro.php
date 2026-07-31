<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vai_tro';

    protected $primaryKey = 'ma_vai_tro';

    protected $fillable = ['ten'];

    // Lấy các quyền được gán cho vai trò.
    public function cacQuyen()
    {
        return $this->belongsToMany(
            Quyen::class,
            'vai_tro_quyen',
            'ma_vai_tro',
            'ma_quyen'
        );
    }
}