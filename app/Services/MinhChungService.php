<?php

namespace App\Services;

use Exception;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class MinhChungService
{
    // Tai nhieu tep minh chung va tra ve thong tin tung tep.
    public function taiNhieuTepLen(
        $cacTep,
        $thuMuc,
        &$duongDanDaLuu = []
    ) {
        $minhChungs = [];

        foreach ($cacTep as $tep) {
            if ($tep instanceof UploadedFile) {
                $minhChungs[] = $this->taiTepLen(
                    $tep,
                    $thuMuc,
                    $duongDanDaLuu
                );
            }
        }

        return $minhChungs;
    }

    // Tai mot tep len Cloudinary hoac luu local neu chua cau hinh.
    public function taiTepLen(
        UploadedFile $tep,
        $thuMuc,
        &$duongDanDaLuu = []
    ) {
        if ($this->daCauHinhCloudinary()) {
            $minhChung = $this->taiLenCloudinary($tep, $thuMuc);

            if ($minhChung) {
                return $minhChung;
            }
        }

        return $this->luuTepLocal($tep, $thuMuc, $duongDanDaLuu);
    }

    // Lay duong dan day du cua mot tep minh chung moi hoac cu.
    public function layDuongDan($minhChung)
    {
        $oDia = $this->layGiaTri(
            $minhChung,
            'o_dia',
            'disk',
            'public'
        );
        $duongDan = $this->layGiaTri(
            $minhChung,
            'duong_dan',
            'path',
            ''
        );

        if (
            $oDia == 'cloudinary'
            || str_starts_with($duongDan, 'http://')
            || str_starts_with($duongDan, 'https://')
        ) {
            return $duongDan;
        }

        return asset('storage/'.$duongDan);
    }

    // Kiem tra cac thong tin Cloudinary bat buoc da duoc cau hinh.
    private function daCauHinhCloudinary()
    {
        return config('cloudinary.enabled')
            && config('cloudinary.cloud_name')
            && config('cloudinary.api_key')
            && config('cloudinary.api_secret');
    }

    // Tai tep truc tiep len API Cloudinary.
    private function taiLenCloudinary($tep, $thuMuc)
    {
        try {
            $loaiMime = (string) $tep->getMimeType();
            $loaiTep = 'hinh_anh';
            $loaiTaiNguyen = 'image';

            if (str_starts_with($loaiMime, 'video/')) {
                $loaiTep = 'video';
                $loaiTaiNguyen = 'video';
            }

            $thoiGian = time();
            $thamSos = [
                'folder' => $thuMuc,
                'timestamp' => $thoiGian,
            ];
            $chuKy = $this->taoChuKy($thamSos);
            $duongDanApi = 'https://api.cloudinary.com/v1_1/'
                .config('cloudinary.cloud_name')
                .'/'.$loaiTaiNguyen.'/upload';

            $response = Http::timeout(30)
                ->attach(
                    'file',
                    file_get_contents($tep->getRealPath()),
                    $tep->getClientOriginalName()
                )->post($duongDanApi, [
                    'api_key' => config('cloudinary.api_key'),
                    'folder' => $thuMuc,
                    'timestamp' => $thoiGian,
                    'signature' => $chuKy,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $duongDan = '';
            $maCongKhai = null;

            if (isset($data['secure_url'])) {
                $duongDan = $data['secure_url'];
            }

            if (isset($data['public_id'])) {
                $maCongKhai = $data['public_id'];
            }

            return [
                'o_dia' => 'cloudinary',
                'duong_dan' => $duongDan,
                'ten_goc' => $tep->getClientOriginalName(),
                'loai_mime' => $loaiMime,
                'loai_tep' => $loaiTep,
                'kich_thuoc' => (int) $tep->getSize(),
                'ma_cong_khai' => $maCongKhai,
            ];
        } catch (Exception $exception) {
            return null;
        }
    }

    // Luu tep vao storage public khi khong dung Cloudinary.
    private function luuTepLocal(
        $tep,
        $thuMuc,
        &$duongDanDaLuu
    ) {
        $duongDan = $tep->store($thuMuc, 'public');
        $duongDanDaLuu[] = $duongDan;
        $loaiMime = (string) $tep->getMimeType();
        $loaiTep = 'hinh_anh';

        if (str_starts_with($loaiMime, 'video/')) {
            $loaiTep = 'video';
        }

        return [
            'o_dia' => 'public',
            'duong_dan' => $duongDan,
            'ten_goc' => $tep->getClientOriginalName(),
            'loai_mime' => $loaiMime,
            'loai_tep' => $loaiTep,
            'kich_thuoc' => (int) $tep->getSize(),
        ];
    }
    // Tao chu ky Cloudinary tu cac tham so upload.
    private function taoChuKy($thamSos)
    {
        ksort($thamSos);
        $cacPhan = [];

        foreach ($thamSos as $ten => $giaTri) {
            $cacPhan[] = $ten.'='.$giaTri;
        }

        return sha1(
            implode('&', $cacPhan)
            .config('cloudinary.api_secret')
        );
    }

    // Lay gia tri theo ten moi hoac ten cu.
    private function layGiaTri(
        $data,
        $tenMoi,
        $tenCu,
        $macDinh
    ) {
        if (is_array($data) && isset($data[$tenMoi])) {
            return $data[$tenMoi];
        }

        if (is_array($data) && isset($data[$tenCu])) {
            return $data[$tenCu];
        }

        if (is_object($data) && isset($data->$tenMoi)) {
            return $data->$tenMoi;
        }

        if (is_object($data) && isset($data->$tenCu)) {
            return $data->$tenCu;
        }

        return $macDinh;
    }
}
