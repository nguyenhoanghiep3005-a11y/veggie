<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTroQuyen extends Model
{
    protected $table = 'vai_tro_quyen';

    protected $primaryKey = 'ma_vai_tro_quyen';

    protected $fillable = ['ma_vai_tro', 'ma_quyen'];

    // Lấy vai trò của dòng phân quyền.
    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'ma_vai_tro');
    }

    // Lấy quyền của dòng phân quyền.
    public function quyen()
    {
        return $this->belongsTo(Quyen::class, 'ma_quyen');
    }
}