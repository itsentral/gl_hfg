<style>
	table,
	th,
	td {
		border: 0px;
		/* border-collapse: collapse; */
	}
</style>
<?php
// error_reporting(E_ALL & ~E_NOTICE);

// Bikin lookup label supaya nama tabel/kolom/coa tampil manusiawi, bukan cuma kode mentah
$Lookup_Menu  = array(); // nama_table => nama_menu
$Lookup_Field = array(); // nama_field => label
$Lookup_Coa   = array(); // no_perkiraan => "no_perkiraan  nama"

if ($data_menu) {
	foreach ($data_menu as $vals) {
		$Lookup_Menu[$vals->nama_table] = $vals->nama_menu;
	}
}

if ($data_field) {
	foreach ($data_field as $vals) {
		$Lookup_Field[$vals->nama_field] = $vals->label;
	}
}

if ($data_perkiraan) {
	foreach ($data_perkiraan as $vals) {
		$Lookup_Coa[$vals->no_perkiraan] = $vals->no_perkiraan . '  ' . $vals->nama;
	}
}

// label untuk field statis (posisi, sumber_coa, proses)
$Label_Posisi = array('D' => 'Debet', 'K' => 'Kredit');
$Label_Sumber_Coa = array('tetap' => 'Tetap (Pilih Manual)', 'dinamis' => 'Dari Form Input HFG');
$Label_Proses = array('otomatis' => 'Otomatis', 'input' => 'Input');

$kode_master_jurnal = $nama_jurnal = $keterangan_header = $tipe_jurnal = '';
$jenis_transaksi = '';

if ($data_header) {
	foreach ($data_header as $row_header) {
		$kode_master_jurnal = $row_header->kode_master_jurnal;
		$nama_jurnal        = $row_header->nama_jurnal;
		$keterangan_header  = $row_header->keterangan_header;
		$tipe_jurnal        = $row_header->tipe;
		$jenis_transaksi    = isset($row_header->jenis_transaksi) ? $row_header->jenis_transaksi : '';
	}
}
?>
<div class="modal fade bd-example-modal-xl" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" style="width:80%;" role="document">
		<div class="modal-content">
			<div class="modal-body" style="max-height: 90vh; overflow-y: auto;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><b>Master Jurnal Detail</b></h4>
				</div>

				<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px 40px; width: 90%; margin-top: 20px; margin-left: 20px;">

					<div style="display: flex;">
						<span style="width: 150px; font-weight: bold;">Kode Master Jurnal</span>
						<span style="margin-right: 10px;">:</span>
						<div style="flex: 1;"><?= $kode_master_jurnal ?></div>
					</div>
					<div style="display: flex;">
						<span style="width: 150px; font-weight: bold;">Tipe Jurnal</span>
						<span style="margin-right: 10px;">:</span>
						<div style="flex: 1;"><?= $tipe_jurnal ?></div>
					</div>

					<div style="display: flex;">
						<span style="width: 150px; font-weight: bold;">Nama Jurnal</span>
						<span style="margin-right: 10px;">:</span>
						<div style="flex: 1;"><?= $nama_jurnal ?></div>
					</div>
					<div style="display: flex;">
						<span style="width: 150px; font-weight: bold;">Keterangan</span>
						<span style="margin-right: 10px;">:</span>
						<div style="flex: 1;"><?= $keterangan_header ?></div>
					</div>


					<div style="display: flex;">
						<span style="width: 150px; font-weight: bold;">Jenis Transaksi</span>
						<span style="margin-right: 10px;">:</span>
						<div style="flex: 1;"><?= $jenis_transaksi != '' ? $jenis_transaksi : '-' ?></div>
					</div>
					<div></div>

				</div>
				<br>
				<table class="table table-bordered table-striped">
					<thead>
						<tr class="bg-blue">
							<th class="text-center">No</th>
							<th class="text-center">Nama Tabel</th>
							<th class="text-center">Nama Kolom</th>
							<th class="text-center">Nominal Kurs</th>
							<th class="text-center">Field No. Reff</th>
							<th class="text-center">Sumber COA</th>
							<th class="text-center">No. Perkiraan / COA Dinamis</th>
							<th class="text-center" style="min-width: 250px;">Keterangan</th>
							<th class="text-center">Posisi</th>
						</tr>
					</thead>
					<tbody id="list_detail">
						<?php
						$no = 0;
						if ($data_detail) {
							foreach ($data_detail as $row_jurnal) {
								$no++;

								$Menu              = isset($row_jurnal->menu) ? $row_jurnal->menu : '';
								$Field             = isset($row_jurnal->field) ? $row_jurnal->field : '';
								$Field_No_Reff     = isset($row_jurnal->field_no_reff) ? $row_jurnal->field_no_reff : '';
								$Field_Nominal_Kurs  = isset($row_jurnal->field_nominal_kurs) ? $row_jurnal->field_nominal_kurs : '';
								$Sumber_Coa        = isset($row_jurnal->sumber_coa) ? $row_jurnal->sumber_coa : 'tetap';
								$No_Coa            = isset($row_jurnal->no_perkiraan) ? $row_jurnal->no_perkiraan : '';

								$Field_Coa_Dinamis = isset($row_jurnal->field_coa_dinamis) ? $row_jurnal->field_coa_dinamis : '';
								$Keterangan        = isset($row_jurnal->keterangan) ? $row_jurnal->keterangan : '';
								$Posisi            = isset($row_jurnal->posisi) ? $row_jurnal->posisi : '';

								// Label nama tabel & kolom (fallback ke kode mentah kalau tidak ketemu di lookup)
								$Label_Menu  = isset($Lookup_Menu[$Menu]) ? $Lookup_Menu[$Menu] : $Menu;
								$Label_Field = isset($Lookup_Field[$Field]) ? $Lookup_Field[$Field] : $Field;

								// Kolom "No. Perkiraan / COA Dinamis" tergantung sumber_coa
								if ($Sumber_Coa == 'dinamis') {
									$Label_Coa_Field = isset($Lookup_Field[$Field_Coa_Dinamis]) ? $Lookup_Field[$Field_Coa_Dinamis] : $Field_Coa_Dinamis;
									$Tampil_Coa = $Label_Coa_Field;
								} else {
									$Tampil_Coa = isset($Lookup_Coa[$No_Coa]) ? $Lookup_Coa[$No_Coa] : $No_Coa;
								}

								$Label_Posisi_Tampil = isset($Label_Posisi[$Posisi]) ? $Label_Posisi[$Posisi] : $Posisi;
								$Label_Sumber_Tampil = isset($Label_Sumber_Coa[$Sumber_Coa]) ? $Label_Sumber_Coa[$Sumber_Coa] : $Sumber_Coa;
						?>
								<tr id="tr_<?= $no ?>">
									<td class="text-center"><?= $no ?></td>
									<td><?= $Label_Menu != '' ? $Label_Menu : '-' ?></td>
									<td><?= $Label_Field != '' ? $Label_Field : '-' ?></td>
									<td><?= $Field_Nominal_Kurs != '' ? (isset($Lookup_Field[$Field_Nominal_Kurs]) ? $Lookup_Field[$Field_Nominal_Kurs] : $Field_Nominal_Kurs) : '-' ?></td>
									<td><?= $Field_No_Reff != '' ? (isset($Lookup_Field[$Field_No_Reff]) ? $Lookup_Field[$Field_No_Reff] : $Field_No_Reff) : '-' ?></td>
									<td><?= $Label_Sumber_Tampil ?></td>
									<td><?= $Tampil_Coa != '' ? $Tampil_Coa : '-' ?></td>
									<td><?= $Keterangan != '' ? $Keterangan : '-' ?></td>
									<td><?= $Label_Posisi_Tampil ?></td>
								</tr>
						<?php
							}
						} else {
							echo "<tr><td colspan='10' class='text-center'>Tidak ada detail jurnal.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>