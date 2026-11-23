<?php
$mode_export = isset($mode_export) ? $mode_export : '';
if ($mode_export === '') {
	$this->load->view('header');
}

$nama_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$bulan_terpilih = (int) $data_bulan_post;
$tahun_terpilih = (int) $data_tahun_post;

if ($bulan_terpilih < 1 || $bulan_terpilih > 12) {
	$bulan_terpilih = isset($bln_aktif) ? (int) $bln_aktif : (int) date('n');
}
if ($tahun_terpilih < 1) {
	$tahun_terpilih = isset($thn_aktif) ? (int) $thn_aktif : (int) date('Y');
}

$semua_akun = array();
$sumber_akun = array(
	$data_nokir_pdptn, $data_nokir_hpp, $data_nokir_biaya52,
	$data_nokir_biaya61, $data_nokir_biaya, $data_nokir_pdptn2,
	$data_nokir_biaya3
);
foreach ($sumber_akun as $daftar_akun) {
	if (is_array($daftar_akun) || is_object($daftar_akun)) {
		foreach ($daftar_akun as $akun) {
			$semua_akun[] = $akun;
		}
	}
}

$nilai_akun = function ($akun) {
	return (((float) $akun->saldoawal) + ((float) $akun->debet - (float) $akun->kredit)) * (float) $akun->faktor;
};
$nilai_periode = function ($akun) {
	$previous = (float) $akun->saldoawal * (float) $akun->faktor;
	$current = ((float) $akun->debet - (float) $akun->kredit) * (float) $akun->faktor;
	return array($previous, $current, $previous + $current);
};
$akun_kelompok = function ($prefix) use ($semua_akun) {
	$hasil = array();
	foreach ($semua_akun as $akun) {
		if (strpos($akun->no_perkiraan, $prefix) === 0) {
			$hasil[] = $akun;
		}
	}
	return $hasil;
};
$total_kelompok = function ($prefix) use ($akun_kelompok, $nilai_akun) {
	$total = 0;
	foreach ($akun_kelompok($prefix) as $akun) {
		$total += $nilai_akun($akun);
	}
	return $total;
};
$format_nilai = function ($nilai) {
	return number_format($nilai, 2, ',', '.');
};
$total_periode = function ($prefix) use ($akun_kelompok, $nilai_periode) {
	$total = array(0, 0, 0);
	foreach ($akun_kelompok($prefix) as $akun) {
		$nilai = $nilai_periode($akun);
		for ($i = 0; $i < 3; $i++) {
			$total[$i] += $nilai[$i];
		}
	}
	return $total;
};
$operasi_periode = function ($nilai_awal, $nilai_lain, $operator = '-') {
	$hasil = array();
	for ($i = 0; $i < 3; $i++) {
		$hasil[$i] = $operator === '+' ? $nilai_awal[$i] + $nilai_lain[$i] : $nilai_awal[$i] - $nilai_lain[$i];
	}
	return $hasil;
};

$kode_cabang = $this->session->userdata('nomor_cabang');
$cabang = $this->db->query(
	'SELECT namacabang FROM pastibisa_tb_cabang WHERE nocab = ? LIMIT 1',
	array($kode_cabang)
)->row();
$nama_cabang = $cabang ? $cabang->namacabang : '';

$kelompok_hpp = array(
	array('5101', 'COST OF GOODS PRODUCT', 'SUB TOTAL COST OF GOOD PRODUCT'),
	array('5103', 'COST OF GOODS MANUFACTURE', 'SUB TOTAL COST OF GOODS MANUFACTURE'),
	array('5104', 'COST OF GOODS SOLD', 'SUB TOTAL COST OF GOODS SOLD'),
	array('5105', 'BIAYA PENGIRIMAN PC', 'SUB TOTAL BIAYA PENGIRIMAN PC'),
	array('5106', 'FAVORABLE/UNFAVORABLE', 'SUB TOTAL FAVORABLE/UNFAVORABLE')
);
$kelompok_produksi = array(
	array('5201', 'LABOUR COST', 'SUB TOTAL LABOUR COST'),
	array('5202', 'BAHAN PENDUKUNG KHUSUS', 'SUB TOTAL BAHAN PENDUKUNG KHUSUS'),
	array('5203', 'CONSUMABLE', 'SUB TOTAL CONSUMABLE'),
	array('5204', 'BIAYA KOORDINASI', 'SUB TOTAL BIAYA KOORDINASI'),
	array('5205', 'BIAYA SCRAP', 'SUB TOTAL BIAYA SCRAP'),
	array('5206', 'FACTORY OVERHEAD', 'SUB TOTAL FACTORY OVERHEAD'),
	array('5207', 'BIAYA PENGIRIMAN', 'SUB TOTAL BIAYA PENGIRIMAN'),
	array('5208', 'LABOUR COST CONTROL', 'SUB TOTAL LABOUR COST CONTROL'),
	array('5209', 'BAHAN PENDUKUNG KHUSUS CONTROL', 'SUB TOTAL BAHAN PENDUKUNG KHUSUS CONTROL'),
	array('5210', 'CONSUMABLE CONTROL', 'SUB TOTAL CONSUMABLE CONTROL'),
	array('5211', 'BIAYA KOORDINASI CONTROL', 'SUB TOTAL BIAYA KOORDINASI CONTROL'),
	array('5212', 'BIAYA SCRAP CONTROL', 'SUB TOTAL BIAYA SCRAP CONTROL'),
	array('5213', 'FACTORY OVERHEAD CONTROL', 'SUB TOTAL FACTORY OVERHEAD CONTROL'),
	array('5214', 'BIAYA PENGIRIMAN CONTROL', 'SUB TOTAL BIAYA PENGIRIMAN CONTROL')
);

$total_pendapatan = $total_kelompok('41');
$total_5101 = $total_kelompok('5101');
$total_5103 = $total_kelompok('5103');
$total_5104 = $total_kelompok('5104');
$total_5105 = $total_kelompok('5105');
$total_5106 = $total_kelompok('5106');
$gross_profit_pc = $total_pendapatan - $total_5101 - $total_5103 - $total_5104 - $total_5105;
$gross_profit_sebelum_biaya_produksi = $gross_profit_pc - $total_5106;
$total_hpp = $total_kelompok('51') + $total_kelompok('52');
$gross_profit = $total_pendapatan - $total_hpp;
$total_operasional = $total_kelompok('61') + $total_kelompok('62');
$operating_profit = $gross_profit - $total_operasional;
$total_pendapatan_lain = $total_kelompok('71');
$total_biaya_lain = $total_kelompok('72');
$net_profit = $operating_profit + $total_pendapatan_lain - $total_biaya_lain;

$periode_pendapatan = $total_periode('41');
$periode_cogm = $operasi_periode($total_periode('5101'), $total_periode('5103'), '+');
$periode_gross_profit_pc = $periode_pendapatan;
foreach (array('5101', '5103', '5104', '5105') as $prefix) {
	$periode_gross_profit_pc = $operasi_periode($periode_gross_profit_pc, $total_periode($prefix));
}
$periode_gross_profit_sebelum_produksi = $operasi_periode($periode_gross_profit_pc, $total_periode('5106'));
$periode_total_hpp = $operasi_periode($total_periode('51'), $total_periode('52'), '+');
$periode_gross_profit = $operasi_periode($periode_pendapatan, $periode_total_hpp);
$periode_total_operasional = $operasi_periode($total_periode('61'), $total_periode('62'), '+');
$periode_operating_profit = $operasi_periode($periode_gross_profit, $periode_total_operasional);
$periode_net_profit = $operasi_periode($operasi_periode($periode_operating_profit, $total_periode('71'), '+'), $total_periode('72'));

$sel_periode = function ($nilai) use ($format_nilai) {
	foreach ($nilai as $angka) {
		echo '<td class="nilai">' . $format_nilai($angka) . '</td>';
	}
};
$baris_ringkasan = function ($judul, $nilai_ytd, $nilai_periode) use ($mode_export, $format_nilai, $sel_periode) {
	echo '<tr class="baris-total"><td></td><td>' . html_escape($judul) . '</td>';
	if ($mode_export === 'excel') {
		$sel_periode($nilai_periode);
	} else {
		echo '<td class="nilai">' . $format_nilai($nilai_ytd) . '</td>';
	}
	echo '</tr>';
};
$tampilkan_kelompok = function ($prefix, $judul_kelompok, $judul_subtotal) use ($akun_kelompok, $nilai_akun, $nilai_periode, $format_nilai, $mode_export, $sel_periode) {
	$daftar = $akun_kelompok($prefix);
	echo '<tr class="baris-kelompok"><td></td><td>' . html_escape($judul_kelompok) . '</td><td></td>' . ($mode_export === 'excel' ? '<td></td><td></td>' : '') . '</tr>';
	foreach ($daftar as $akun) {
		echo '<tr><td>' . html_escape($akun->no_perkiraan) . '</td><td class="nama-akun">' . html_escape($akun->nama) . '</td>';
		if ($mode_export === 'excel') {
			$sel_periode($nilai_periode($akun));
		} else {
			echo '<td class="nilai">' . $format_nilai($nilai_akun($akun)) . '</td>';
		}
		echo '</tr>';
	}
	$total = 0;
	$total_export = array(0, 0, 0);
	foreach ($daftar as $akun) {
		$total += $nilai_akun($akun);
		$nilai = $nilai_periode($akun);
		for ($i = 0; $i < 3; $i++) {
			$total_export[$i] += $nilai[$i];
		}
	}
	echo '<tr class="baris-subtotal"><td></td><td>' . html_escape($judul_subtotal) . '</td>';
	if ($mode_export === 'excel') {
		$sel_periode($total_export);
	} else {
		echo '<td class="nilai">' . $format_nilai($total) . '</td>';
	}
	echo '</tr>';
};
?>

<style>
	.labarugi-filter { background: #d3eefa; padding: 15px; }
	.labarugi-filter .form-group { margin-right: 12px; }
	.btn-labarugi { background: #286090; color: #fff; }
	.btn-labarugi-excel { background: #02723b; color: #fff; }
	.btn-labarugi-pdf { background: #de0b0b; color: #fff; }
	.labarugi-sheet { width: 100%; margin: 0; background: #fff; padding: 24px 18px 40px; box-sizing: border-box; }
	.labarugi-title { text-align: center; font-weight: bold; font-size: 16px; line-height: 1.5; margin-bottom: 24px; }
	.table-labarugi { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
	.table-labarugi td { border: 0; padding: 3px 8px; vertical-align: top; }
	.table-labarugi td:first-child { width: 145px; white-space: nowrap; }
	.table-labarugi td:nth-child(2) { width: auto; }
	.table-labarugi td:last-child { width: 185px; }
	.table-labarugi .nama-akun { padding-left: 28px; }
	.table-labarugi .nilai { text-align: right; white-space: nowrap; }
	.table-labarugi .baris-kelompok td { font-weight: bold; padding-top: 15px; }
	.table-labarugi .baris-subtotal td { font-weight: bold; padding-top: 6px; border-top: 1px solid #222; }
	.table-labarugi .baris-total td { font-weight: bold; padding-top: 8px; padding-bottom: 8px; border-top: 1px solid #222; }
	.labarugi-export { padding: 0; }
	.labarugi-excel .table-labarugi td,
	.labarugi-excel .table-labarugi th { padding: 4px 7px; }
	.labarugi-excel .header-excel th { border: 1px solid #000; text-align: center; }
	@media print {
		.content-header, .main-footer, .labarugi-filter { display: none !important; }
		.labarugi-sheet { max-width: none; margin: 0; padding: 0; }
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
		<form class="labarugi-filter form-inline" method="post" action="<?= base_url('index.php/report/tampilkan_labarugi') ?>" autocomplete="off">
			<div class="form-group">
				<label for="bulan_labarugi">Bulan</label>
				<select id="bulan_labarugi" name="bulan_labarugi" class="form-control">
					<?php for ($i = 1; $i <= 12; $i++) { ?>
						<option value="<?= $i ?>" <?= $i === $bulan_terpilih ? 'selected' : '' ?>><?= $nama_bulan[$i] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label for="tahun_labarugi">Tahun</label>
				<select id="tahun_labarugi" name="tahun_labarugi" class="form-control">
					<?php for ($i = (int) date('Y') - 8; $i <= (int) date('Y') + 2; $i++) { ?>
						<option value="<?= $i ?>" <?= $i === $tahun_terpilih ? 'selected' : '' ?>><?= $i ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label for="level">Level</label>
				<select id="level" name="level" class="form-control">
					<option value="3" <?= (int) $level === 3 ? 'selected' : '' ?>>3</option>
					<option value="5" <?= (int) $level === 5 ? 'selected' : '' ?>>5</option>
				</select>
			</div>
			<button type="submit" name="tampilkan" value="Tampilkan" class="btn btn-labarugi">Tampilkan</button>
			<button type="submit" name="tampilkan" value="View Excel" class="btn btn-labarugi-excel">View Excel</button>
			<button type="submit" name="tampilkan" value="View Pdf" class="btn btn-labarugi-pdf">View Pdf</button>
		</form>
	</div>

	<div class="box">
		<?php } ?>
		<div class="labarugi-sheet <?= $mode_export !== '' ? 'labarugi-export' : '' ?> <?= $mode_export === 'excel' ? 'labarugi-excel' : '' ?>">
			<div class="labarugi-title">
				INCOME STATEMENT LEVEL <?= (int) $level ?><br>
				Periode : <?= $nama_bulan[$bulan_terpilih] ?> <?= $tahun_terpilih ?><br>
				<?= html_escape($nama_cabang) ?>
			</div>
			<div class="table-responsive">
				<table class="table-labarugi">
					<?php if ($mode_export === 'excel') { ?>
					<thead><tr class="header-excel">
						<th>No. Perkiraan</th><th>Nama Perkiraan</th><th>Previous (Year-To-Date)</th><th>Current Month</th><th>Year-To-Date</th>
					</tr></thead>
					<?php } ?>
					<tbody>
						<?php $tampilkan_kelompok('41', 'PENDAPATAN', 'SUB TOTAL PENDAPATAN'); ?>

						<?php foreach ($kelompok_hpp as $kelompok) { ?>
							<?php $tampilkan_kelompok($kelompok[0], $kelompok[1], $kelompok[2]); ?>
							<?php if ($kelompok[0] === '5103') { ?>
								<?php $baris_ringkasan('SUB TOTAL COGM', $total_5101 + $total_5103, $periode_cogm); ?>
							<?php } elseif ($kelompok[0] === '5105') { ?>
								<?php $baris_ringkasan('GROSS PROFIT PC', $gross_profit_pc, $periode_gross_profit_pc); ?>
							<?php } elseif ($kelompok[0] === '5106') { ?>
								<?php $baris_ringkasan('GROSS PROFIT -', $gross_profit_sebelum_biaya_produksi, $periode_gross_profit_sebelum_produksi); ?>
							<?php } ?>
						<?php } ?>

						<?php foreach ($kelompok_produksi as $kelompok) { $tampilkan_kelompok($kelompok[0], $kelompok[1], $kelompok[2]); } ?>
						<?php $baris_ringkasan('TOTAL COST OF GOODS SOLD', $total_hpp, $periode_total_hpp); ?>
						<?php $baris_ringkasan('GROSS PROFIT', $gross_profit, $periode_gross_profit); ?>

						<?php $tampilkan_kelompok('61', 'BIAYA PEMASARAN', 'SUB TOTAL BIAYA PEMASARAN'); ?>
						<?php $tampilkan_kelompok('62', 'BEBAN UMUM & ADMINISTRASI', 'SUB TOTAL BEBAN UMUM & ADMINISTRASI'); ?>
						<?php $baris_ringkasan('TOTAL BIAYA OPERATIONAL', $total_operasional, $periode_total_operasional); ?>
						<?php $baris_ringkasan('OPERATING PROFIT', $operating_profit, $periode_operating_profit); ?>

						<?php $tampilkan_kelompok('71', 'PENDAPATAN LAIN-LAIN', 'SUB TOTAL PENDAPATAN LAIN-LAIN'); ?>
						<?php $tampilkan_kelompok('72', 'BIAYA LAIN-LAIN', 'SUB TOTAL BIAYA LAIN-LAIN'); ?>
						<?php $baris_ringkasan('NET PROFIT', $net_profit, $periode_net_profit); ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php if ($mode_export === '') { ?>
	</div>
</section>

<?php
	$this->load->view('footer');
}
?>
