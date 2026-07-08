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
// PENTING: $data_detail HARUS sudah di-ORDER BY urutan ASC dari controller/model,
// supaya urutan render baris di halaman edit sama persis dengan urutan tersimpan.
$Existing_Detail = array();
if (!empty($data_detail)) {
	foreach ($data_detail as $row) {
		$Existing_Detail[] = array(
			'nama_menu'         => isset($row->menu) ? $row->menu : '',
			'nama_field'        => isset($row->field) ? $row->field : '',
			'field_no_reff'     => isset($row->field_no_reff) ? $row->field_no_reff : '',
			'field_nominal_kurs'  => isset($row->field_nominal_kurs) ? $row->field_nominal_kurs : '',
			'sumber_coa'        => isset($row->sumber_coa) ? $row->sumber_coa : 'tetap',
			'noperkiraan'       => isset($row->no_perkiraan) ? $row->no_perkiraan : '',

			'field_coa_dinamis' => isset($row->field_coa_dinamis) ? $row->field_coa_dinamis : '',
			'keterangan'        => isset($row->keterangan) ? $row->keterangan : '',
			'posisi'            => isset($row->posisi) ? $row->posisi : 'D',
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

	.drag-handle {
		cursor: move;
		vertical-align: middle !important;
		color: #999;
		font-size: 16px;
	}

	.drag-handle:hover {
		color: #333;
	}

	tr.sortable-ghost {
		background: #f0f8ff !important;
		opacity: 0.6;
	}

	tr.sortable-chosen {
		background: #fffbe6 !important;
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
							<p class="text-muted" style="margin-bottom:0;">
								<i class="fa fa-info-circle"></i> Geser ikon <i class="fa fa-arrows"></i> di kolom paling kiri untuk mengubah urutan baris.
							</p>
						</div>

						<div class="table-responsive">
							<table class="table table-bordered table-striped" style="min-width:1450px;">
								<thead>
									<tr class="bg-blue">
										<th class="text-center" width="30">&nbsp;</th>
										<th class="text-center">Nama Tabel</th>
										<th class="text-center">Nominal IDR</th>
										<th class="text-center">Nominal Kurs</th>
										<th class="text-center">Field No. Reff</th>
										<th class="text-center">Sumber COA</th>
										<th class="text-center">No. Perkiraan</th>
										<th class="text-center" style="min-width: 250px;">Keterangan</th>
										<th class="text-center">Posisi</th>
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

<!-- SortableJS untuk drag & drop urutan baris detail -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
	var data_coa = <?php echo json_encode($Arr_Coa); ?>;
	var data_menu = <?php echo json_encode($Arr_Menu); ?>;
	var existing_details = <?php echo json_encode($Existing_Detail); ?>;
	var max_fields = 15;
	var sortableDetail = null;

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

		// 0. Drag handle (kolom urutan) - HARUS jadi kolom pertama
		Template += '<td class="text-center drag-handle"><i class="fa fa-arrows"></i></td>';

		// 1. Nama Tabel
		Template += '<td><select name="detail[' + rowNum + '][nama_menu]" id="nama_menu_' + rowNum + '" class="form-control input-sm nm_menu select2-me"><option value="">- Nama Tabel -</option>';
		$.each(data_menu, function(key, nilai) {
			var sel = (key === existingData.nama_menu) ? ' selected' : '';
			Template += '<option value="' + key + '"' + sel + '>' + nilai + '</option>';
		});
		Template += '</select></td>';

		// 2. Nama Kolom (diisi via ajax kalau ada nama_menu)
		Template += '<td><select name="detail[' + rowNum + '][nama_field]" id="nama_field_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

		// 3. Nominal Kurs
		Template += '<td><select name="detail[' + rowNum + '][field_nominal_kurs]" id="field_nominal_kurs_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

		// 4. Field No. Reff
		Template += '<td><select name="detail[' + rowNum + '][field_no_reff]" id="field_no_reff_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select></td>';

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
			// Dropdown pakai key composite "kode^nama", tapi DB cuma simpan kode polos -> cocokkan bagian depannya saja
			var nokirOnly = key.split('^')[0];
			var sel = (nokirOnly === existingData.noperkiraan) ? ' selected' : '';
			Template += '<option value="' + key + '"' + sel + '>' + nilai + '</option>';
		});
		Template += '</select></div>';

		Template += '<div class="kolom_coa_dinamis" style="' + tampilDinamis + '">';
		Template += '<select name="detail[' + rowNum + '][field_coa_dinamis]" id="field_coa_dinamis_' + rowNum + '" class="form-control input-sm select2-me"><option value="">- Daftar Kosong -</option></select>';
		Template += '</div>';
		Template += '</div></td>';

		// 7. Keterangan
		var ketVal = existingData.keterangan ? existingData.keterangan.replace(/"/g, '&quot;') : '';
		Template += '<td><input type="text" class="form-control input-sm" id="keterangan' + rowNum + '" name="detail[' + rowNum + '][keterangan]" placeholder="- Keterangan -" value="' + ketVal + '"></td>';

		// 8. Posisi
		Template += '<td><select name="detail[' + rowNum + '][posisi]" id="posisi' + rowNum + '" class="form-control input-sm select2-me">';
		Template += '<option value="D"' + (existingData.posisi === 'D' ? ' selected' : '') + '>Debet</option>';
		Template += '<option value="K"' + (existingData.posisi === 'K' ? ' selected' : '') + '>Kredit</option>';
		Template += '<option value="otomatis"' + (existingData.posisi === 'otomatis' ? ' selected' : '') + '>Otomatis (ikuti tanda nilai)</option>';
		Template += '</select></td>';

		// 10. Opsi: baris pertama = tombol Tambah, baris lain = tombol Delete
		var opsiHtml = (rowNum === 1) ?
			'<button type="button" class="btn btn-sm btn-primary" id="add_field_button">Tambah</button>' :
			'<button type="button" class="btn btn-sm btn-danger" onClick="return DelRow(' + rowNum + ');">Delete <i class="fa fa-trash-o"></i></button>';
		Template += '<td width="10%" class="text-center">' + opsiHtml + '</td>';

		Template += '</tr>';
		return Template;
	}

	function initSortable() {
		if (sortableDetail) {
			sortableDetail.destroy();
		}
		sortableDetail = Sortable.create(document.getElementById('list_detail'), {
			handle: '.drag-handle',
			animation: 150,
			forceFallback: true, // supaya select2 dropdown tidak mengganggu proses drag
			ghostClass: 'sortable-ghost',
			chosenClass: 'sortable-chosen'
		});
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
		initSortable();

		// Untuk setiap baris lama yang sudah punya Nama Tabel, load dropdown dependent-nya
		// (Nama Kolom, Field No.Reff, Field No.Request, dan Field COA Dinamis kalau perlu)
		$.each(existing_details, function(idx, row) {
			var rowNum = idx + 1;

			if (row.nama_menu) {
				ajaxGetKolomEdit(row.nama_menu, [{
						selector: '#nama_field_' + rowNum,
						value: row.nama_field
					},
					{
						selector: '#field_no_reff_' + rowNum,
						value: row.field_no_reff
					},
					{
						selector: '#field_nominal_kurs_' + rowNum,
						value: row.field_nominal_kurs
					},
					{
						selector: '#field_coa_dinamis_' + rowNum,
						value: row.field_coa_dinamis
					}
				]);
			}
		});

		// Nama Tabel utama -> isi 4 dropdown kolom sekaligus (Nama Kolom, Field No. Reff, Field No. Request, Field COA Dinamis)
		$(document).on('change', '.nm_menu', function() {
			var loop = $(this).attr('id').split('_')[2];
			var nama_tabel = $(this).val();

			ajaxGetKolom(nama_tabel, '#nama_field_' + loop);
			ajaxGetKolom(nama_tabel, '#field_no_reff_' + loop);
			ajaxGetKolom(nama_tabel, '#field_nominal_kurs_' + loop);
			ajaxGetKolom(nama_tabel, '#field_coa_dinamis_' + loop);
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
					var field_coa = $('#field_coa_dinamis_' + loop).val();
					if (!field_coa) intC++;
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

			// ===== Susun ulang nilai "urutan" sesuai posisi baris SAAT INI di layar =====
			// Ini WAJIB dilakukan di sini (bukan sebelumnya), karena baris bisa saja
			// sudah dipindah-pindah via drag & drop sebelum tombol SIMPAN ditekan.
			$('#list_detail tr').each(function(idx) {
				var loop = $(this).attr('id').split('_')[1];

				// Hapus hidden input urutan lama (kalau sudah pernah ditambahkan) supaya tidak dobel
				$(this).find('input[name="detail[' + loop + '][urutan]"]').remove();

				$(this).append(
					'<input type="hidden" name="detail[' + loop + '][urutan]" value="' + (idx + 1) + '">'
				);
			});

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