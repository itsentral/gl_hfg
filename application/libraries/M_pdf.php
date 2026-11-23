<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_pdf
{

    public function __construct()
    {
        if (function_exists('log_message')) {
            log_message('debug', 'mPDF class is loaded.');
        }
    }

    public function load($config = array())
    {
        return $this->createMpdf('A5', $config);
    }

    public function load_a4($config = array())
    {
        return $this->createMpdf('A4', $config);
    }

    public function load_c($config = array())
    {
        return $this->createMpdf('A5', $config);
    }

    private function createMpdf($format, $config)
    {
        $autoload = FCPATH . 'vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new RuntimeException('Dependency mPDF belum terpasang. Jalankan composer install.');
        }

        require_once $autoload;

        if (!is_array($config)) {
            $config = array();
        }

        $defaultConfig = array(
            'mode' => 'utf-8',
            'format' => $format,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 6,
            'margin_footer' => 3,
            'tempDir' => APPPATH . 'cache'
        );

        return new \Mpdf\Mpdf(array_merge($defaultConfig, $config));
    }
}
