<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Laporan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		$this->load->model('Jurnal_model');
		$this->load->model('Order_model');
		//$this->load->model('custom_model');
		//$this->load->model('master_model');
		//$this->load->model('komisi_model');
		$this->load->helper('menu');
		$this->load->model('Invoice_model');
		$this->load->model('Laporan_model');
	}
	
	function labarugi()
	{
		$data['judul']			= "Income Statement";
		$cek_periode_aktif		= $this->Laporan_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['bln_aktif']	= substr($tgl_periode_aktif, 0, 2);
				$data['thn_aktif']	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$this->load->view("laporan/v_labarugi", $data);
	}
	
	function tampilkan_labarugi()
	{
		$data['judul']			= "Income Statement";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('laporan/excel_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} elseif ($this->input->post('tampilkan') == "View Pdf") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('laporan/print_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {

			$var_bulan					= $this->input->post('bulan_labarugi');
			$var_tahun					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			$data['level']				= $this->input->post('level');

			$data['data_bulan']		= $this->Laporan_model->get_bulan_coa();
			$data['data_tahun']		= $this->Laporan_model->get_tahun_coa();
			
			
			$data['data_nokir_pdptn3']	= $this->Laporan_model->get_nokir_pdptn_lvl3($var_bulan, $var_tahun, $level);
			// $data['data_nokir_pdptn']	= $this->Laporan_model->get_nokir_pdptn($var_bulan, $var_tahun, $level, $coapend);
			$data['data_nokir_pdptn2']	= $this->Laporan_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp3']		= $this->Laporan_model->get_nokir_hpp_lvl3($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_5101']		= $this->Laporan_model->get_nokir_hpp_5101($var_bulan, $var_tahun, $level);
			$data['data_nokir_5102']		= $this->Laporan_model->get_nokir_hpp_5102($var_bulan, $var_tahun, $level);
			$data['data_nokir_5103']		= $this->Laporan_model->get_nokir_hpp_5103($var_bulan, $var_tahun, $level);
			$data['data_nokir_5104']		= $this->Laporan_model->get_nokir_hpp_5104($var_bulan, $var_tahun, $level);
			$data['data_nokir_5105']		= $this->Laporan_model->get_nokir_hpp_5105($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_biaya523'] = $this->Laporan_model->get_nokir_biaya52_lvl3($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Laporan_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Laporan_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Laporan_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Laporan_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Laporan_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Laporan_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Laporan_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Laporan_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Laporan_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Laporan_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Laporan_model->get_nokir_fee($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$this->load->view("laporan/v_list_labarugi", $data);
			//redirect('laporan/print_labarugi');
		}
	}

	function excel_labarugi()
	{
		$data['judul']			= "Income Statement";

		$data['data_bulan']		= $this->Laporan_model->get_bulan_coa();
		$data['data_tahun']		= $this->Laporan_model->get_tahun_coa();

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);
		$level = $this->uri->segment(5);
		$data['level']				= $level;

		$data['data_nokir_pdptn3']	= $this->Laporan_model->get_nokir_pdptn_lvl3($var_bulan, $var_tahun, $level);
			// $data['data_nokir_pdptn']	= $this->Laporan_model->get_nokir_pdptn($var_bulan, $var_tahun, $level, $coapend);
			$data['data_nokir_pdptn2']	= $this->Laporan_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp3']		= $this->Laporan_model->get_nokir_hpp_lvl3($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_5101']		= $this->Laporan_model->get_nokir_hpp_5101($var_bulan, $var_tahun, $level);
			$data['data_nokir_5102']		= $this->Laporan_model->get_nokir_hpp_5102($var_bulan, $var_tahun, $level);
			$data['data_nokir_5103']		= $this->Laporan_model->get_nokir_hpp_5103($var_bulan, $var_tahun, $level);
			$data['data_nokir_5104']		= $this->Laporan_model->get_nokir_hpp_5104($var_bulan, $var_tahun, $level);
			$data['data_nokir_5105']		= $this->Laporan_model->get_nokir_hpp_5105($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_biaya523'] = $this->Laporan_model->get_nokir_biaya52_lvl3($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Laporan_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Laporan_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Laporan_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Laporan_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Laporan_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Laporan_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Laporan_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Laporan_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Laporan_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Laporan_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Laporan_model->get_nokir_fee($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

		$this->load->view("laporan/v_labarugi_excel", $data);
		//redirect('laporan/print_labarugi');		
	}

	function print_labarugi()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		$data['level']				= $level;

		$data['data_bulan']		= $this->Laporan_model->get_bulan_coa();
		$data['data_tahun']		= $this->Laporan_model->get_tahun_coa();

		$data['data_nokir_pdptn3']	= $this->Laporan_model->get_nokir_pdptn_lvl3($var_bulan, $var_tahun, $level);
			// $data['data_nokir_pdptn']	= $this->Laporan_model->get_nokir_pdptn($var_bulan, $var_tahun, $level, $coapend);
			$data['data_nokir_pdptn2']	= $this->Laporan_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp3']		= $this->Laporan_model->get_nokir_hpp_lvl3($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_5101']		= $this->Laporan_model->get_nokir_hpp_5101($var_bulan, $var_tahun, $level);
			$data['data_nokir_5102']		= $this->Laporan_model->get_nokir_hpp_5102($var_bulan, $var_tahun, $level);
			$data['data_nokir_5103']		= $this->Laporan_model->get_nokir_hpp_5103($var_bulan, $var_tahun, $level);
			$data['data_nokir_5104']		= $this->Laporan_model->get_nokir_hpp_5104($var_bulan, $var_tahun, $level);
			$data['data_nokir_5105']		= $this->Laporan_model->get_nokir_hpp_5105($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_biaya523'] = $this->Laporan_model->get_nokir_biaya52_lvl3($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Laporan_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Laporan_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Laporan_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Laporan_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Laporan_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Laporan_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Laporan_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Laporan_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Laporan_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Laporan_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Laporan_model->get_nokir_fee($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;
			
			
			$this->load->view("laporan/v_print_labarugi", $data);
			
			
			
		
	}
	
	function print_labarugi_old()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		$data['level']				= $level;

		$data['data_bulan']		= $this->Laporan_model->get_bulan_coa();
		$data['data_tahun']		= $this->Laporan_model->get_tahun_coa();

		$data['data_nokir_pdptn3']	= $this->Laporan_model->get_nokir_pdptn_lvl3($var_bulan, $var_tahun, $level);
			// $data['data_nokir_pdptn']	= $this->Laporan_model->get_nokir_pdptn($var_bulan, $var_tahun, $level, $coapend);
			$data['data_nokir_pdptn2']	= $this->Laporan_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp3']		= $this->Laporan_model->get_nokir_hpp_lvl3($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_5101']		= $this->Laporan_model->get_nokir_hpp_5101($var_bulan, $var_tahun, $level);
			$data['data_nokir_5102']		= $this->Laporan_model->get_nokir_hpp_5102($var_bulan, $var_tahun, $level);
			$data['data_nokir_5103']		= $this->Laporan_model->get_nokir_hpp_5103($var_bulan, $var_tahun, $level);
			$data['data_nokir_5104']		= $this->Laporan_model->get_nokir_hpp_5104($var_bulan, $var_tahun, $level);
			$data['data_nokir_5105']		= $this->Laporan_model->get_nokir_hpp_5105($var_bulan, $var_tahun, $level);
			
			$data['data_nokir_biaya523'] = $this->Laporan_model->get_nokir_biaya52_lvl3($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Laporan_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Laporan_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Laporan_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Laporan_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Laporan_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Laporan_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Laporan_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Laporan_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Laporan_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Laporan_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Laporan_model->get_nokir_fee($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;
			
			
		$html			= $this->load->view('laporan/v_print_labarugi', $data, true);
		$pdfFilePath	= "Laporan_Laba_Rugi_" . $var_bulan . "_" . $var_tahun . ".pdf";
		$this->load->library('m_pdf');
		$pdf			= $this->m_pdf->load();
		//$mpdf = new mPDF('c', 'A4-L'); 
		$pdf->AddPage(
			'P', // L = Landscape, P = Potrait
			'',
			'',
			'',
			5, // margin_left
			0, // margin right
			0, // margin top
			0, // margin bottom
			0, // margin header
			0  // margin footer
		); 
		$pdf->WriteHTML($html);
		$pdf->Output($pdfFilePath, "I");
		//redirect('laporan/tampilkan_labarugi');
	}

	function labarugi_event()
	{
		$data['judul']				= "Laporan Laba Rugi Per Event";
		$data['data_project']		= $this->Laporan_model->ambil_data_project();
		$this->load->view("laporan/v_labarugi_event", $data);
	}

	function tampilkan_labarugi_event()
	{
		$data['judul']			= "Laporan Laba Rugi Per Event";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_project					= $this->input->post('project');
			$var_project_ = str_replace("|", "_", $var_project);

			redirect('laporan/excel_labarugi_event/' . $var_project_);
		} else {

			$var_project					= $this->input->post('project');

			$data['data_project']		= $this->Laporan_model->ambil_data_project();
			$data['data_project2']		= $this->Laporan_model->ambil_data_project2($var_project);

			$data['data_nokir_pdptn']	= $this->Laporan_model->get_nokir_pdptn_event($var_project);
			$data['data_nokir_pdptn2']	= $this->Laporan_model->get_nokir_pdptn2_event($var_project);
			$data['data_nokir_hpp']		= $this->Laporan_model->get_nokir_hpp_event($var_project);
			$data['data_nokir_biaya']	= $this->Laporan_model->get_nokir_biaya_event($var_project);
			$data['data_nokir_biaya2']	= $this->Laporan_model->get_nokir_biaya2_event($var_project);
			$data['data_nokir_biaya3']	= $this->Laporan_model->get_nokir_biaya3_event($var_project);

			$this->load->view("laporan/v_list_labarugi_event", $data);
			//redirect('laporan/print_labarugi');
		}
	}
	
	function tes(){
		for($n=12;$n<=15;)
		{
			do
			
			{ 
				if($n==13)
				
					$x=9;
					
					echo"nilai".$n."<br>";
			}				
			while($n==12);
		
		}
					
		
	}
}