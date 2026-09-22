<link rel="stylesheet" href="<?= base_url("assets/pdf/style.css"); ?>">
<div id="space"></div>
<!-- <table class="gridtable" width="100%"> -->
<div class="form-group row">
	<label class="control-label col-sm-2">Nomor BUM</label>
	<div class="col-sm-4 text-left">
		<?php
			echo $rows_header[0]->nomor;
		?>
	</div>
	<label class="control-label col-sm-2">Tgl BUM</label>
	<div class="col-sm-4 text-left">
		<?php
			echo date('d-m-Y',strtotime($rows_header[0]->tgl));
		?>
	</div>
</div>
<div class="form-group row">
	<label class="control-label col-sm-2">Keterangan</label>
	<div class="col-sm-4 text-left">
		<?php
			echo $rows_header[0]->terima_dari;
		?>
	</div>
	<label class="control-label col-sm-2">Total BUM</label>
	<div class="col-sm-4 text-left">
		<?php
			echo number_format($rows_header[0]->jml);
		?>
	</div>
</div>
<table id="my-grid" class="table table-striped table-bordered table-hover" width="100%">
    <thead>
        <tr bgcolor='#9acfea'>
            <th width="10">#</th>
            <th>Keterangan</th>
            <th>Reff</th>
            <th>No. Perkiraan</th>
			<th>Nama Perkiraan</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Kredit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sum_debet = 0;
        $sum_kredit = 0;
        if($detail->num_rows() > 0)
        {
            $no=1;
            foreach($detail->result() as $d){
                $sum_debet  += $d->debet;
                $sum_kredit += $d->kredit;

                echo "
                <tr>
                    <td>".$no.".</td>
                    <td>".$d->keterangan."</td>
                    <td>".$d->no_reff."</td>
                    <td>".$d->no_perkiraan."</td>
					<td>".$d->nama."</td>
                    <td align='right'>".number_format($d->debet, 0, ',', '.')."</td>
                    <td align='right'>".number_format($d->kredit, 0, ',', '.')."</td>
                </tr>
                ";
                $no++;
            }
        }
        ?>
    </tbody>
    <tfoot>
        <tr bgcolor='#DCDCDC'>
            <td colspan="5" align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= number_format($sum_debet, 0, ',', '.') ?></b></td>
            <td align="right"><b><?= number_format($sum_kredit, 0, ',', '.') ?></b></td>
        </tr>
    </tfoot>
</table>