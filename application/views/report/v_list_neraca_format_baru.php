<?php
$mode_export = isset($mode_export) ? $mode_export : '';
if ($mode_export === '') {
	$this->load->view('header');
}

$nm_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$bulan_terpilih = isset($data_bulan_post) ? (int) $data_bulan_post : 0;
$tahun_terpilih = isset($data_tahun_post) ? (int) $data_tahun_post : 0;
$level_terpilih = isset($level) ? (int) $level : 5;

if ($bulan_terpilih < 1 || $bulan_terpilih > 12) {
	$bulan_terpilih = isset($bln_aktif) ? (int) $bln_aktif : (int) date('n');
}
if ($tahun_terpilih < 1) {
	$tahun_terpilih = isset($thn_aktif) ? (int) $thn_aktif : (int) date('Y');
}
if ($level_terpilih < 1) {
	$level_terpilih = 5;
}

$nocab = $this->session->userdata('nomor_cabang');
$cek_cabang = $this->db->query("SELECT nocab, namacabang FROM pastibisa_tb_cabang WHERE nocab = ?", array($nocab))->row();
$nama_cabang = $cek_cabang ? $cek_cabang->namacabang : '';

// Helper perhitungan nilai akun
$hitung_nilai_aktiva = function ($row) {
	$faktor = (float) $row->faktor;
	$saldoawal = (float) $row->saldoawal;
	$debet = (float) $row->debet;
	$kredit = (float) $row->kredit;
	return $saldoawal + ($debet - $kredit) * $faktor;
};

$hitung_nilai_pasiva = function ($row) {
	$faktor = (float) $row->faktor;
	$saldoawal = (float) $row->saldoawal;
	$debet = (float) $row->debet;
	$kredit = (float) $row->kredit;
	return ($saldoawal + $debet - $kredit) * $faktor;
};

$format_nilai = function ($nilai) {
	if ($nilai === null || $nilai === '') {
		return '';
	}
	return number_format((float) $nilai, 0, ',', '.');
};

// SISI KIRI (AKTIVA)
$items_kiri = array();

// 1. AKTIVA LANCAR (1100-00-00)
$subtotal_harta_lancar = 0;
$items_kiri[] = array('type' => 'group_header', 'code' => '1100-00-00', 'desc' => 'AKTIVA LANCAR', 'amount' => null);
if (!empty($data_HartaLancar) && (is_array($data_HartaLancar) || is_object($data_HartaLancar))) {
	foreach ($data_HartaLancar as $row) {
		$nilai = $hitung_nilai_aktiva($row);
		$subtotal_harta_lancar += $nilai;
		$items_kiri[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kiri[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total AKTIVA LANCAR', 'amount' => $subtotal_harta_lancar);
$items_kiri[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);

// 2. HARTA TIDAK LANCAR (1200-00-00) - Tampilkan jika ada akunnya
$subtotal_tdk_harta_lancar = 0;
if (!empty($data_tdkHartaLancar) && (is_array($data_tdkHartaLancar) || is_object($data_tdkHartaLancar))) {
	$items_kiri[] = array('type' => 'group_header', 'code' => '1200-00-00', 'desc' => 'HARTA TIDAK LANCAR', 'amount' => null);
	foreach ($data_tdkHartaLancar as $row) {
		$nilai = $hitung_nilai_aktiva($row);
		$subtotal_tdk_harta_lancar += $nilai;
		$items_kiri[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
	$items_kiri[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total HARTA TIDAK LANCAR', 'amount' => $subtotal_tdk_harta_lancar);
	$items_kiri[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);
}

// 3. AKTIVA TETAP (1300-00-00)
$subtotal_aktiva_tetap = 0;
$items_kiri[] = array('type' => 'group_header', 'code' => '1300-00-00', 'desc' => 'AKTIVA TETAP', 'amount' => null);
if (!empty($data_AktivaTetap) && (is_array($data_AktivaTetap) || is_object($data_AktivaTetap))) {
	foreach ($data_AktivaTetap as $row) {
		$nilai = $hitung_nilai_aktiva($row);
		$subtotal_aktiva_tetap += $nilai;
		$items_kiri[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kiri[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total AKTIVA TETAP', 'amount' => $subtotal_aktiva_tetap);
$items_kiri[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);

// 4. AKTIVA LAIN-LAIN (1400-00-00)
$subtotal_aktiva_lain = 0;
$items_kiri[] = array('type' => 'group_header', 'code' => '1400-00-00', 'desc' => 'AKTIVA LAIN-LAIN', 'amount' => null);
if (!empty($data_AktivaLain) && (is_array($data_AktivaLain) || is_object($data_AktivaLain))) {
	foreach ($data_AktivaLain as $row) {
		$nilai = $hitung_nilai_aktiva($row);
		$subtotal_aktiva_lain += $nilai;
		$items_kiri[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kiri[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total AKTIVA LAIN-LAIN', 'amount' => $subtotal_aktiva_lain);

$total_assets = $subtotal_harta_lancar + $subtotal_tdk_harta_lancar + $subtotal_aktiva_tetap + $subtotal_aktiva_lain;

// SISI KANAN (PASIVA)
$items_kanan = array();

// 1. PASIVA LANCAR (2100-00-00)
$subtotal_pasiva_lancar = 0;
$items_kanan[] = array('type' => 'group_header', 'code' => '2100-00-00', 'desc' => 'PASIVA LANCAR', 'amount' => null);
if (!empty($data_Hutang) && (is_array($data_Hutang) || is_object($data_Hutang))) {
	foreach ($data_Hutang as $row) {
		$nilai = $hitung_nilai_pasiva($row);
		$subtotal_pasiva_lancar += $nilai;
		$items_kanan[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kanan[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total PASSIVA LANCAR', 'amount' => $subtotal_pasiva_lancar);
$items_kanan[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);

// 2. HUTANG JANGKA PANJANG (2200-00-00)
$subtotal_hutang_jk_pjg = 0;
$items_kanan[] = array('type' => 'group_header', 'code' => '2200-00-00', 'desc' => 'HUTANG JANGKA PANJANG', 'amount' => null);
if (!empty($data_HutangJkPjg) && (is_array($data_HutangJkPjg) || is_object($data_HutangJkPjg))) {
	foreach ($data_HutangJkPjg as $row) {
		$nilai = $hitung_nilai_pasiva($row);
		$subtotal_hutang_jk_pjg += $nilai;
		$items_kanan[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kanan[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total HUTANG JANGKA PANJANG', 'amount' => $subtotal_hutang_jk_pjg);
$items_kanan[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);

// 3. MODAL (3100-00-00)
$subtotal_modal = 0;
$items_kanan[] = array('type' => 'group_header', 'code' => '3100-00-00', 'desc' => 'MODAL', 'amount' => null);
if (!empty($data_Modal) && (is_array($data_Modal) || is_object($data_Modal))) {
	foreach ($data_Modal as $row) {
		$nilai = $hitung_nilai_pasiva($row);
		$subtotal_modal += $nilai;
		$items_kanan[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kanan[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total MODAL DISETOR', 'amount' => $subtotal_modal);
$items_kanan[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);

// 4. LABA DITAHAN (3901-00-00)
$subtotal_laba = 0;
$items_kanan[] = array('type' => 'group_header', 'code' => '3901-00-00', 'desc' => 'LABA DITAHAN', 'amount' => null);
if (!empty($data_Laba) && (is_array($data_Laba) || is_object($data_Laba))) {
	foreach ($data_Laba as $row) {
		$nilai = $hitung_nilai_pasiva($row);
		$subtotal_laba += $nilai;
		$items_kanan[] = array('type' => 'account', 'code' => $row->no_perkiraan, 'desc' => $row->nama, 'amount' => $nilai);
	}
}
$items_kanan[] = array('type' => 'subtotal', 'code' => '', 'desc' => 'Sub Total LABA DITAHAN', 'amount' => $subtotal_laba);

$total_passiva = $subtotal_pasiva_lancar + $subtotal_hutang_jk_pjg + $subtotal_modal + $subtotal_laba;

// Menyelaraskan baris kiri dan kanan
$max_rows = max(count($items_kiri), count($items_kanan));
while (count($items_kiri) < $max_rows) {
	$items_kiri[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);
}
while (count($items_kanan) < $max_rows) {
	$items_kanan[] = array('type' => 'empty', 'code' => '', 'desc' => '', 'amount' => null);
}
?>

<style type="text/css">
	.str {
		mso-number-format: \@;
	}
	.num {
		mso-number-format: "#\,##0";
		text-align: right;
	}
	.neraca-filter {
		background-color: #d3eefa;
		padding: 15px;
	}
	.neraca-filter .form-group {
		margin-right: 12px;
	}
	.warnaTombol {
		background-color: #286090;
		color: white;
	}
	.warnaTombolExcel {
		background-color: #02723b;
		color: white;
	}
	.warnaTombolPdf {
		background-color: #de0b0b;
		color: white;
	}
	.neraca-container {
		width: 100%;
		background: #ffffff;
		padding: <?= $mode_export === '' ? '20px' : '0' ?>;
		box-sizing: border-box;
		font-family: Arial, Helvetica, sans-serif;
	}
	.neraca-title-row th {
		text-align: center;
		font-weight: bold;
		font-size: 15px;
		line-height: 1.6;
		padding-bottom: 14px;
		border: none;
	}
	.table-neraca {
		width: 100%;
		border-collapse: collapse;
		font-size: <?= $mode_export === 'pdf' ? '9.5px' : '11px' ?>;
		font-family: Arial, Helvetica, sans-serif;
	}
	.table-neraca td, .table-neraca th {
		padding: 4px 6px;
		vertical-align: middle;
	}
	.col-code {
		width: 12%;
		white-space: nowrap;
	}
	.col-desc {
		width: 25%;
	}
	.col-amount {
		width: 11%;
		text-align: right;
		white-space: nowrap;
	}
	.col-sep {
		width: 4%;
		border: none;
	}
	.header-kolom th {
		background-color: #0070C0;
		color: #ffffff;
		text-align: center;
		font-weight: bold;
		border: 1px solid #005a9e;
	}
	.header-kolom th.col-sep {
		background-color: transparent;
		border: none;
	}
	.row-group-header td {
		font-weight: bold;
		padding-top: 10px;
	}
	.row-subtotal td {
		font-weight: bold;
		border-top: 1px solid #333333;
		padding-top: 5px;
	}
	.row-total td {
		font-weight: bold;
		font-size: <?= $mode_export === 'pdf' ? '10.5px' : '12px' ?>;
		border-top: 1px solid #333333;
		border-bottom: 3px double #333333;
		padding-top: 8px;
		padding-bottom: 8px;
	}
	.amount-val {
		text-align: right;
	}
	@media print {
		.content-header, .main-footer, .neraca-filter {
			display: none !important;
		}
		.neraca-container {
			padding: 0;
		}
	}
</style>

<?php if ($mode_export === '') { ?>
<section class="content-header">
	<h1><?= html_escape($judul) ?></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active"><?= html_escape($judul) ?></li>
	</ol>
</section>

<section class="content">
	<div class="box box-primary">
		<div class="neraca-filter">
			<form class="form-inline" method="post" action="<?= base_url('index.php/report/tampilkan_neraca') ?>" autocomplete="off">
				<div class="form-group">
					<label for="bulan_neraca">Bulan</label>
					<select id="bulan_neraca" name="bulan_neraca" class="form-control">
						<?php for ($i = 1; $i <= 12; $i++) { ?>
							<option value="<?= $i ?>" <?= $i === $bulan_terpilih ? 'selected' : '' ?>><?= $nm_bulan[$i] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="tahun_neraca">Tahun</label>
					<select id="tahun_neraca" name="tahun_neraca" class="form-control">
						<?php for ($i = (int) date('Y') - 8; $i <= (int) date('Y') + 2; $i++) { ?>
							<option value="<?= $i ?>" <?= $i === $tahun_terpilih ? 'selected' : '' ?>><?= $i ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="level">Level</label>
					<select id="level" name="level" class="form-control">
						<option value="3" <?= $level_terpilih === 3 ? 'selected' : '' ?>>3</option>
						<option value="4" <?= $level_terpilih === 4 ? 'selected' : '' ?>>4</option>
						<option value="5" <?= $level_terpilih === 5 ? 'selected' : '' ?>>5</option>
					</select>
				</div>
				<button type="submit" name="tampilkan" value="Tampilkan" onclick="return check()" class="btn warnaTombol">Tampilkan</button>
				<button type="submit" name="tampilkan" value="View Excel" onclick="return check()" class="btn warnaTombolExcel">View Excel</button>
				<button type="submit" name="tampilkan" value="View Pdf" onclick="return check()" class="btn warnaTombolPdf">View Pdf</button>
			</form>
		</div>
	</div>

	<div class="box">
<?php } ?>

	<div class="neraca-container">
		<div class="table-responsive">
			<table class="table-neraca" border="0" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="neraca-title-row">
						<th colspan="7">
							BALANCE SHEET LEVEL <?= $level_terpilih ?><br>
							Periode : <?= $nm_bulan[$bulan_terpilih] ?> <?= $tahun_terpilih ?><br>
							<?= html_escape($nama_cabang) ?>
						</th>
					</tr>
					<tr class="header-kolom">
						<th class="col-code">Code</th>
						<th class="col-desc">Desciption</th>
						<th class="col-amount">Amount</th>
						<th class="col-sep"></th>
						<th class="col-code">Code</th>
						<th class="col-desc">Desciption</th>
						<th class="col-amount">Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i = 0; $i < $max_rows; $i++) {
						$kiri = $items_kiri[$i];
						$kanan = $items_kanan[$i];

						// Tentukan class styling baris
						$row_class = '';
						if ($kiri['type'] === 'group_header' || $kanan['type'] === 'group_header') {
							$row_class = 'row-group-header';
						} elseif ($kiri['type'] === 'subtotal' || $kanan['type'] === 'subtotal') {
							$row_class = 'row-subtotal';
						}
					?>
						<tr class="<?= $row_class ?>">
							<!-- SISI KIRI (AKTIVA) -->
							<td class="col-code str"><?= $kiri['code'] !== '' ? html_escape($kiri['code']) : '' ?></td>
							<td class="col-desc"><?= $kiri['desc'] !== '' ? ($kiri['type'] === 'group_header' || $kiri['type'] === 'subtotal' ? '<b>' . html_escape($kiri['desc']) . '</b>' : html_escape($kiri['desc'])) : '' ?></td>
							<td class="col-amount amount-val num"><?= $kiri['amount'] !== null ? ($kiri['type'] === 'subtotal' ? '<b>' . $format_nilai($kiri['amount']) . '</b>' : $format_nilai($kiri['amount'])) : '' ?></td>

							<!-- PEMISAH -->
							<td class="col-sep"></td>

							<!-- SISI KANAN (PASIVA) -->
							<td class="col-code str"><?= $kanan['code'] !== '' ? html_escape($kanan['code']) : '' ?></td>
							<td class="col-desc"><?= $kanan['desc'] !== '' ? ($kanan['type'] === 'group_header' || $kanan['type'] === 'subtotal' ? '<b>' . html_escape($kanan['desc']) . '</b>' : html_escape($kanan['desc'])) : '' ?></td>
							<td class="col-amount amount-val num"><?= $kanan['amount'] !== null ? ($kanan['type'] === 'subtotal' ? '<b>' . $format_nilai($kanan['amount']) . '</b>' : $format_nilai($kanan['amount'])) : '' ?></td>
						</tr>
					<?php } ?>

					<!-- BARIS TOTAL ASSETS & TOTAL PASSIVA -->
					<tr class="row-total">
						<td class="col-code"></td>
						<td class="col-desc" style="text-align: center;"><b>TOTAL ASSETS</b></td>
						<td class="col-amount amount-val num"><b><?= $format_nilai($total_assets) ?></b></td>
						<td class="col-sep"></td>
						<td class="col-code"></td>
						<td class="col-desc" style="text-align: center;"><b>TOTAL PASSIVA</b></td>
						<td class="col-amount amount-val num"><b><?= $format_nilai($total_passiva) ?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

<?php if ($mode_export === '') { ?>
	</div>
</section>

<script>
	function check() {
		if ($("#bulan_neraca").val() == "0") {
			alert("Silahkan Pilih Bulan");
			return false;
		} else if ($("#tahun_neraca").val() == "0") {
			alert("Silahkan Pilih Tahun");
			return false;
		}
	}
</script>

<?php
	$this->load->view('footer');
}
?>
