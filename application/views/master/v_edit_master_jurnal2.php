<?php
$this->load->view('header');

$Arr_Coa  = array();
$Arr_Menu = array();

if ($data_perkiraan) {
	foreach ($data_perkiraan as $key => $vals) {
		$kode_Coa = $vals->no_perkiraan . '^' . $vals->nama;
		$Arr_Coa[$kode_Coa] = $vals->no_perkiraan . '  ' . $vals->nama;
	}
}

if ($data_menu) {
	foreach ($data_menu as $key => $vals) {
		$kode_Menu = $vals->nama_table;
		$Arr_Menu[$kode_Menu] = $vals->nama_menu;
	}
}

// Susun data detail existing jadi array asosiatif sederhana untuk dikirim ke JS.
// SESUAIKAN nama properti ($row->...) di bawah ini dengan nama kolom asli di tabel detail kamu.
$Existing_Detail = array();
if (!empty($data_detail)) {
	foreach ($data_detail as $row) {
		$Existing_Detail[] = array(
			'nama_menu'         => isset($row->menu) ? $row->menu : '',
			'nama_field'        => isset($row->field) ? $row->field : '',
			'field_no_reff'     => isset($row->field_no_reff) ? $row->field_no_reff : '',
			'field_no_request'  => isset($row->field_no_request) ? $row->field_no_request : '',
			'sumber_coa'        => isset($row->sumber_coa) ? $row->sumber_coa : 'tetap',
			'noperkiraan'       => isset($row->no_perkiraan) ? $row->no_perkiraan : '',
			'table_coa_dinamis' => isset($row->table_coa_dinamis) ? $row->table_coa_dinamis : '',
			'field_coa_dinamis' => isset($row->field_coa_dinamis) ? $row->field_coa_dinamis : '',
			'keterangan'        => isset($row->keterangan) ? $row->keterangan : '',
			'posisi'            => isset($row->posisi) ? $row->posisi : 'D',
			'proses'            => isset($row->cara_insert) ? $row->cara_insert : 'otomatis',
		);
	}
}
?>

<style>
	.select2-container--default .select2-selection--single {
		height: 34px;
		border: 1px solid #d2d6de;
		border-radius: 0px;
		background: none;
		box-shadow: none;
		color: #444;
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 32px;
		padding-left: 8px;
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 32px;
	}

	.table-responsive .select2-container {
		min-width: 140px !important;
	}

	.select2-container {
		z-index: 9999;
	}
</style>

<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/jquery.timepicker.css">
<section class="content-header">
	<h1><?= $judul ?></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active"><?= $judul ?></li>
	</ol>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-xs-12">
			<form method="post" action="<?= base_url() ?>index.php/master/proses_edit_jurnal_header" id="form-proses-bro">
				<input type="hidden" name="kode_master_jurnal" value="<?= $kode_master_jurnal ?>">

				<div class="box box-primary">
					<div class="box-header">
						<h3 class="box-title">Jurnal Header</h3>
					</div>
					<div class="box-body">
						<div class="form-group row">
							<label class="control-label col-sm-2">Kode Master Jurnal</label>
							<div class="col-sm-4">
								<span class="badge bg-maroon"><?= $kode_master_jurnal ?></span>
							</div>
							<label class="control-label col-sm-2">Tipe Jurnal</label>
							<div class="col-sm-4">
								<select name="tipe" class="form-control select2-me" id="tipe" required>
									<option value="">-- Pilih Tipe Jurnal --</option>
									<option value="BUM" <?= ($tipe == 'BUM') ? 'selected' : '' ?>>BUM</option>
									<option value="BUK" <?= ($tipe == 'BUK') ? 'selected' : '' ?>>BUK</option>
									<option value="JV" <?= ($tipe == 'JV') ? 'selected' : '' ?>>JV</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-sm-2">Jenis Pembelian</label>
							<div class="col-sm-4">
								<select name="jenis_jurnal" class="form-control select2-me" id="jenis_jurnal" required>
									<option value="">-- Pilih Jenis Pembelian --</option>
									<option value="produksi" <?= ($jenis_jurnal == 'produksi') ? 'selected' : '' ?>>Produksi</option>
									<option value="nonstok" <?= ($jenis_jurnal == 'nonstok') ? 'selected' : '' ?>>Non Stok</option>
									<option value="stok" <?= ($jenis_jurnal == 'stok') ? 'selected' : '' ?>>Stok</option>
									<option value="aset" <?= ($jenis_jurnal == 'aset') ? 'selected' : '' ?>>Aset</option>
								</select>
							</div>
							<label class="control-label col-sm-2">Eksekusi Saat</label>
							<div class="col-sm-4">
								<select name="eksekusi" class="form-control select2-me" id="eksekusi" required>
									<option value="">-- Pilih Proses Jurnal --</option>
									<option value="penerimaan" <?= ($eksekusi == 'penerimaan') ? 'selected' : '' ?>>Penerimaan Barang</option>
									<option value="aproval" <?= ($eksekusi == 'aproval') ? 'selected' : '' ?>>Approval Persetujuan Pembayaran</option>
									<option value="pembayaran" <?= ($eksekusi == 'pembayaran') ? 'selected' : '' ?>>Pembayaran</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-sm-2">Nama Jurnal</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="nama_jurnal" name="nama_jurnal" value="<?= $nama_jurnal ?>" required>
							</div>
							<label class="control-label col-sm-2">Jenis Transaksi</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="jenis_transaksi" name="jenis_transaksi" value="<?= isset($jenis_transaksi) ? $jenis_transaksi : '' ?>" placeholder="- Isi Jenis Transaksi -">
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-sm-2">Keterangan</label>
							<div class="col-sm-10">
								<textarea cols="75" rows="2" class="form-control input-sm" name="keterangan_header" id="keterangan_header"><?= $keterangan_header ?></textarea>
							</div>
						</div>
					</div>

					<div class="box-body">
						<div class="box-header">
							<h3 class="box-title">Jurnal Detail</h3>
						</div>

						<div class="table-responsive">
							<table class="table table-bordered table-striped" style="min-width:1400px;">
								<thead>
									<tr class="bg-blue">
										<th class="text-center">Nama Tabel</th>
										<th class="text-center">Nama Kolom</th>
										<th class="text-center">Field No. Reff</th>
										<th class="text-center">Field No. Request</th>
										<th class="text-center">Sumber COA</th>
										<th class="text-center">No. Perkiraan</th>
										<th class="text-center">Keterangan</th>
										<th class="text-center">Posisi</th>
										<th class="text-center">Insert</th>
										<th class="text-center">Opsi</th>
									</tr>
								</thead>
								<tbody id="list_detail">
									<!-- Baris diisi via JavaScript (buildDetailRow) saat halaman dimuat -->
								</tbody>
							</table>
						</div>
						<!-- /table-responsive -->

					</div>
					<div class="box-footer">
						<?php
						echo form_button(array('type' => 'button', 'class' => 'btn btn-md btn-success', 'value' => 'save', 'content' => 'SIMPAN', 'id' => 'simpan-bro')) . ' ';
						?>
						<a href="<?= base_url() ?>index.php/master/jurnal_header" class="btn btn-danger">KEMBALI</a>
					</div>
				</div>
		</div>
	</div>
</section>

<?php $this->load->view('footer'); ?>

<link rel="stylesheet" href="<?= base_url() ?>plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?= base_url() ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/bootstrap-clockpicker.min.css">
<script src="<?= base_url() ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= base_url() ?>dist/js/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>dist/jquery.timepicker.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	var data_coa          = <?php echo json_encode($Arr_Coa); ?>;
	var data_menu         = <?php echo json_encode($Arr_Menu); ?>;
	var existing_details  = <?php echo json_encode($Existing_Detail); ?>;
	var max_fields         = 15;

	function initSelect2(context) {
		var target = context ? $(context).find('.select2-me') : $('.select2-me');

		target.each(function() {
			var $el = $(this);
			if ($el.data('chosen')) {
				$el.chosen('destroy');
			}
			$el.next('.chosen-container').remove();
			$el.removeClass('chosen-select').css('display', '');
		});

		target.select2({
			dropdownParent: $('body'),
			width: '100%'
		});
	}

	// Dipakai saat user mengganti Nama Tabel secara manual (baris baru / diganti)
	function ajaxGetKolom(nama_tabel, target_selector) {
		$.ajax({
			url: base_url + 'index.php/master/get_kolom/' + nama_tabel,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data) {
				$(target_selector).html(data.option).trigger("change");
			},
			error: function() {
				swal({
					title: "Error Message !",
					text: 'Connection Time Out. Please try again..',
					type: "warning",
					timer: 3000,
					showCancelButton: false,
					showConfirmButton: false,
					allowOutsideClick: false
				});
			}
		});
	}

	// Dipakai KHUSUS saat load awal (edit mode): isi dropdown dependent SEKALIGUS pilih value lama
	function ajaxGetKolomEdit(nama_tabel, targets) {
		$.ajax({
			url: base_url + 'index.php/master/get_kolom/' + nama_tabel,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data) {
				$.each(targets, function(i, target) {
					var $sel = $(target.selector);
					$sel.html(data.option);
					if (target.value) {
						$sel.val(target.value);
					}
					$sel.trigger('change');
				});
			},
			error: function() {
				swal({
					title: "Error Message !",
					text: 'Connection Time Out. Please try again..',
					type: "warning",
					timer: 3000,
					showCancelButton: false,
					showConfirmButton: false,
					allowOutsideClick: false
				});
			}
		});
	}

	// Satu fungsi untuk membangun baris detail, dipakai baik untuk data lama (edit)
	// maupun baris kosong baru (tombol Tambah) -> supaya strukturnya selalu sama persis.
	function buildDetailRow(rowNum, existingData) {
		existingData = existingData || {};

		var Template = '<tr id="tr_' + rowNum + '">';

		// 1. Nama Tabel
		Template += '<td><select name="detail[' + rowNum + '][nama_menu]" id="nama_menu_' + rowNum + '" class="form-control input-sm nm_menu select2-me"><option value="">- Nama Tabel -</option>';
		$.each(data_menu, function(key, nilai) {
			var sel = (key === existingData.nama_menu) ? ' selected' : '';
			Template += '<option value="' + key + '"' + sel + '>' + nilai + '</option>';
		});
		Template += '</select></td>';

		// 2. Nama Kolom (diisi via ajax kalau ada nama_menu)
		Template += '<td><select name="detail[' + rowNum + '][nama_field]" id="nama_field_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

		// 3. Field No. Reff
		Template += '<td><select name="detail[' + rowNum + '][field_no_reff]" id="field_no_reff_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

		// 4. Field No. Request
		Template += '<td><select name="detail[' + rowNum + '][field_no_request]" id="field_no_request_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

		// 5. Sumber COA
		var tetapSel = (existingData.sumber_coa !== 'dinamis') ? ' selected' : '';
		var dinamisSel = (existingData.sumber_coa === 'dinamis') ? ' selected' : '';
		Template += '<td><select name="detail[' + rowNum + '][sumber_coa]" id="sumber_coa_' + rowNum + '" class="form-control input-sm sumber_coa select2-me">';
		Template += '<option value="tetap"' + tetapSel + '>Tetap (Pilih Manual)</option>';
		Template += '<option value="dinamis"' + dinamisSel + '>Dari Form Input HFG</option>';
		Template += '</select></td>';

		// 6. No. Perkiraan (1 kolom, toggle di dalamnya)
		var tampilTetap = (existingData.sumber_coa === 'dinamis') ? 'display:none;' : '';
		var tampilDinamis = (existingData.sumber_coa === 'dinamis') ? '' : 'display:none;';

		Template += '<td><div class="coa-wrap-' + rowNum + '">';
		Template += '<div class="kolom_coa_tetap" style="' + tampilTetap + '">';
		Template += '<select name="detail[' + rowNum + '][noperkiraan]" id="noperkiraan' + rowNum + '" class="form-control input-sm select2-me"><option value="">- No Perkiraan -</option>';
		$.each(data_coa, function(key, nilai) {
			var sel = (key === existingData.noperkiraan) ? ' selected' : '';
			Template += '<option value="' + key + '"' + sel + '>' + nilai + '</option>';
		});
		Template += '</select></div>';

		Template += '<div class="kolom_coa_dinamis" style="' + tampilDinamis + '">';
		Template += '<select name="detail[' + rowNum + '][table_coa_dinamis]" id="table_coa_dinamis_' + rowNum + '" class="form-control input-sm tabel_coa select2-me"><option value="">- Nama Tabel -</option>';
		$.each(data_menu, function(key, nilai) {
			var sel = (key === existingData.table_coa_dinamis) ? ' selected' : '';
			Template += '<option value="' + key + '"' + sel + '>' + nilai + '</option>';
		});
		Template += '</select>';
		Template += '<select name="detail[' + rowNum + '][field_coa_dinamis]" id="field_coa_dinamis_' + rowNum + '" class="form-control input-sm select2-me" style="margin-top:4px;"><option value="">- Daftar Kosong -</option></select>';
		Template += '</div>';
		Template += '</div></td>';

		// 7. Keterangan
		var ketVal = existingData.keterangan ? existingData.keterangan.replace(/"/g, '&quot;') : '';
		Template += '<td><input type="text" class="form-control input-sm" id="keterangan' + rowNum + '" name="detail[' + rowNum + '][keterangan]" placeholder="- Keterangan -" value="' + ketVal + '"></td>';

		// 8. Posisi
		Template += '<td><select name="detail[' + rowNum + '][posisi]" id="posisi' + rowNum + '" class="form-control input-sm select2-me">';
		Template += '<option value="D"' + (existingData.posisi === 'D' ? ' selected' : '') + '>Debet</option>';
		Template += '<option value="K"' + (existingData.posisi === 'K' ? ' selected' : '') + '>Kredit</option>';
		Template += '</select></td>';

		// 9. Insert / Proses
		Template += '<td><select name="detail[' + rowNum + '][proses]" id="proses' + rowNum + '" class="form-control input-sm select2-me">';
		Template += '<option value="otomatis"' + (existingData.proses !== 'input' ? ' selected' : '') + '>Otomatis</option>';
		Template += '<option value="input"' + (existingData.proses === 'input' ? ' selected' : '') + '>Input</option>';
		Template += '</select></td>';

		// 10. Opsi: baris pertama = tombol Tambah, baris lain = tombol Delete
		var opsiHtml = (rowNum === 1) ?
			'<button type="button" class="btn btn-sm btn-primary" id="add_field_button">Tambah</button>' :
			'<button type="button" class="btn btn-sm btn-danger" onClick="return DelRow(' + rowNum + ');">Delete <i class="fa fa-trash-o"></i></button>';
		Template += '<td width="10%" class="text-center">' + opsiHtml + '</td>';

		Template += '</tr>';
		return Template;
	}

	$(document).ready(function() {

		// ==== RENDER BARIS AWAL (data lama kalau ada, atau 1 baris kosong kalau data baru) ====
		if (existing_details.length > 0) {
			$.each(existing_details, function(idx, row) {
				$('#list_detail').append(buildDetailRow(idx + 1, row));
			});
		} else {
			$('#list_detail').append(buildDetailRow(1, {}));
		}

		initSelect2();

		// Untuk setiap baris lama yang sudah punya Nama Tabel, load dropdown dependent-nya
		// (Nama Kolom, Field No.Reff, Field No.Request, dan Field COA Dinamis kalau perlu)
		$.each(existing_details, function(idx, row) {
			var rowNum = idx + 1;

			if (row.nama_menu) {
				ajaxGetKolomEdit(row.nama_menu, [
					{ selector: '#nama_field_' + rowNum, value: row.nama_field },
					{ selector: '#field_no_reff_' + rowNum, value: row.field_no_reff },
					{ selector: '#field_no_request_' + rowNum, value: row.field_no_request }
				]);
			}

			if (row.sumber_coa === 'dinamis' && row.table_coa_dinamis) {
				ajaxGetKolomEdit(row.table_coa_dinamis, [
					{ selector: '#field_coa_dinamis_' + rowNum, value: row.field_coa_dinamis }
				]);
			}
		});

		// Nama Tabel utama -> isi 3 dropdown kolom sekaligus (Nama Kolom, Field No. Reff, Field No. Request)
		$(document).on('change', '.nm_menu', function() {
			var loop = $(this).attr('id').split('_')[2];
			var nama_tabel = $(this).val();

			ajaxGetKolom(nama_tabel, '#nama_field_' + loop);
			ajaxGetKolom(nama_tabel, '#field_no_reff_' + loop);
			ajaxGetKolom(nama_tabel, '#field_no_request_' + loop);
		});

		// Toggle tampilan No. Perkiraan (Tetap vs Dinamis)
		$(document).on('change', '.sumber_coa', function() {
			var val = $(this).val();
			var row = $(this).closest('tr');

			if (val === 'dinamis') {
				row.find('.kolom_coa_tetap').hide();
				row.find('.kolom_coa_dinamis').show();
			} else {
				row.find('.kolom_coa_tetap').show();
				row.find('.kolom_coa_dinamis').hide();
			}
		});

		// Tabel COA Dinamis -> Field COA Dinamis
		$(document).on('change', '.tabel_coa', function() {
			var loop = $(this).attr('id').split('_')[3];
			ajaxGetKolom($(this).val(), '#field_coa_dinamis_' + loop);
		});

		$('#simpan-bro').click(function(e) {
			e.preventDefault();
			$('#simpan-bro, #btn-back').prop('disabled', true);
			loading_spinner();

			var tipe = $('#tipe').val();
			var nama_jurnal = $('#nama_jurnal').val();
			var keterangan_header = $('#keterangan_header').val();

			if (!tipe) {
				close_spinner();
				alert('Tipe belum dipilih, mohon pilih tipe jurnal terlebih dahulu..');
				$('#simpan-bro, #btn-back').prop('disabled', false);
				return false;
			}
			if (!nama_jurnal) {
				close_spinner();
				alert('Nama Jurnal belum diinput, mohon isi Nama Jurnal terlebih dahulu..');
				$('#simpan-bro, #btn-back').prop('disabled', false);
				return false;
			}
			if (!keterangan_header) {
				close_spinner();
				alert('Keterangan belum diinput, mohon isi Keterangan terlebih dahulu..');
				$('#simpan-bro, #btn-back').prop('disabled', false);
				return false;
			}

			var intC = 0;
			var intD = 0;

			$('#list_detail').find('tr').each(function() {
				var loop = $(this).attr('id').split('_')[1];
				var sumber = $('#sumber_coa_' + loop).val();
				var descr = $('#keterangan' + loop).val();

				if (sumber === 'dinamis') {
					var tabel_coa = $('#table_coa_dinamis_' + loop).val();
					var field_coa = $('#field_coa_dinamis_' + loop).val();
					if (!tabel_coa || !field_coa) intC++;
				} else {
					var kode_coa = $('#noperkiraan' + loop).val();
					if (!kode_coa) intC++;
				}

				if (!descr || descr === '-') intD++;
			});

			if (intC > 0) {
				close_spinner();
				alert('No Perkiraan Belum dipilih/dilengkapi. Mohon lengkapi sumber No. Perkiraan terlebih dahulu');
				$('#simpan-bro, #btn-back').prop('disabled', false);
				return false;
			}
			if (intD > 0) {
				close_spinner();
				alert('Keterangan detail Belum diinput. Mohon input keterangan terlebih dahulu');
				$('#simpan-bro, #btn-back').prop('disabled', false);
				return false;
			}

			$('#form-proses-bro').submit();
		});

		$(document).on('click', '#add_field_button', function() {
			var total_row = parseInt($('#list_detail').find('tr').length);
			if (total_row >= max_fields) return;

			var last_row = $('#list_detail tr:last').attr('id');
			var awal = parseInt(last_row.split('_')[1]) + 1;

			$('#list_detail').append(buildDetailRow(awal, {}));
			initSelect2($('#tr_' + awal));
		});

		$('#datepicker').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});

	function DelRow(id) {
		$('#list_detail #tr_' + id).remove();
	}
</script>