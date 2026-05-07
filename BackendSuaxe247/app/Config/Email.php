<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public $fromEmail  = 'thanhtung220504@gmail.com';
    public $fromName   = 'Sửa xe 247';
    public $protocol   = 'smtp';
    public $SMTPHost   = 'smtp.gmail.com';
    public $SMTPUser;
    public $SMTPPass;
    public $SMTPPort   = 587;
    public $SMTPCrypto = 'tls';
    public $mailType   = 'html';
    public $charset    = 'utf-8';
    public $wordWrap   = true;
    
    public function __construct()
    {
        parent::__construct();
        //old
        #SMTP_USER=thanhtung220504@gmail.com
        #SMTP_PASS=jbbb uzao huxg nleh
        
        // Đọc từ .env
        $this->SMTPUser = $_ENV['SMTP_USER'] ?? '22a1001d0358@students.hou.edu.vn';
        $this->SMTPPass = $_ENV['SMTP_PASS'] ?? 'qvoa lynn avqr feju';
    }
}