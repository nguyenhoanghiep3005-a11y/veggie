<?php

namespace Database\Seeders;

use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    // Them hoac cap nhat san pham mau theo tung danh muc.
    public function run()
    {
        foreach ($this->layDanhSachSanPhamMau() as $duongDanDanhMuc => $sanPhams) {
            $danhMuc = DanhMuc::where('duong_dan', $duongDanDanhMuc)->first();

            if (! $danhMuc) {
                continue;
            }

            foreach ($sanPhams as $sanPham) {
                foreach ($sanPham['bien_thes'] as $donVi => $gia) {
                    $tenDayDu = $sanPham['ten'].' '.$donVi;
                    $duongDan = Str::slug($tenDayDu);

                    SanPham::updateOrCreate(
                        ['duong_dan' => $duongDan],
                        [
                            'ten' => $tenDayDu,
                            'ma_danh_muc' => $danhMuc->ma_danh_muc,
                            'mo_ta' => $this->taoMoTa($sanPham),
                            'gia' => $gia,
                            'don_vi' => $donVi,
                            'danh_gia_trung_binh' => 5,
                        ]
                    );
                }
            }
        }
    }

    // Tra ve danh sach san pham mau can tao.
    private function layDanhSachSanPhamMau()
    {
        return [
            'thuc-pham-kho' => [
                [
                    'ten' => 'Nam dong co',
                    'nguon_goc' => 'Lam Dong, Viet Nam',
                    'bao_quan' => 'De noi kho rao, thoang mat, day kin sau khi mo bao bi.',
                    'bien_thes' => ['100g' => 95000, '250g' => 220000, '500g' => 420000],
                ],
                [
                    'ten' => 'Moc nhi kho',
                    'nguon_goc' => 'Hoa Binh, Viet Nam',
                    'bao_quan' => 'Tranh am, tranh anh nang truc tiep.',
                    'bien_thes' => ['100g' => 68000, '250g' => 150000, '500g' => 285000],
                ],
                [
                    'ten' => 'Mien dong',
                    'nguon_goc' => 'Bac Kan, Viet Nam',
                    'bao_quan' => 'Bao quan noi kho, buoc kin sau khi dung.',
                    'bien_thes' => ['500g' => 52000, '1kg' => 98000, '2kg' => 188000],
                ],
            ],
            'gia-vi' => [
                [
                    'ten' => 'Tieu den xay',
                    'nguon_goc' => 'Dak Lak, Viet Nam',
                    'bao_quan' => 'Dong kin nap sau khi su dung.',
                    'bien_thes' => ['100g' => 52000, '250g' => 115000, '500g' => 220000],
                ],
                [
                    'ten' => 'Bot nghe nguyen chat',
                    'nguon_goc' => 'Nghe An, Viet Nam',
                    'bao_quan' => 'Bao quan trong hu kin, tranh am.',
                    'bien_thes' => ['100g' => 60000, '250g' => 135000, '500g' => 260000],
                ],
            ],
            'gao' => [
                [
                    'ten' => 'Gao ST25',
                    'nguon_goc' => 'Soc Trang, Viet Nam',
                    'bao_quan' => 'De noi kho thoang, tranh con trung.',
                    'bien_thes' => ['2kg' => 82000, '5kg' => 185000, '10kg' => 360000],
                ],
                [
                    'ten' => 'Gao lut huyet rong',
                    'nguon_goc' => 'Long An, Viet Nam',
                    'bao_quan' => 'Bao quan kin, tranh noi am thap.',
                    'bien_thes' => ['1kg' => 62000, '2kg' => 120000, '5kg' => 285000],
                ],
            ],
            'hat-dinh-duong' => [
                [
                    'ten' => 'Hat dieu rang muoi',
                    'nguon_goc' => 'Binh Phuoc, Viet Nam',
                    'bao_quan' => 'Dong kin sau khi mo tui de giu do gion.',
                    'bien_thes' => ['250g' => 145000, '500g' => 270000, '1kg' => 520000],
                ],
                [
                    'ten' => 'Hanh nhan rang bo',
                    'nguon_goc' => 'Nhap khau My, rang dong goi tai Viet Nam',
                    'bao_quan' => 'Bao quan kin, tranh nhiet do cao.',
                    'bien_thes' => ['250g' => 165000, '500g' => 310000, '1kg' => 600000],
                ],
            ],
        ];
    }

    // Tao mo ta theo 4 dong de trang chi tiet tu tach thong tin.
    private function taoMoTa($sanPham)
    {
        return $sanPham['ten'].' duoc dong goi theo tung khoi luong, phu hop nhu cau su dung gia dinh.'
            ."\n".$sanPham['bao_quan']
            ."\nVeggie"
            ."\n".$sanPham['nguon_goc'];
    }
}