<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KichHoatTaiKhoanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maKichHoat;
    public $nguoiDung;

    // Khoi tao thong tin email kich hoat tai khoan.
    public function __construct($maKichHoat, $nguoiDung)
    {
        $this->maKichHoat = $maKichHoat;
        $this->nguoiDung = $nguoiDung;
    }

    // Tao email kich hoat tai khoan.
    public function build()
    {
        return $this->subject('Kích hoạt tài khoản')
            ->view('clients.emails.kich-hoat');
    }
}