<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cetak extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$id_instansi	= $this->input->post('instansi');
		$tahun			= $this->input->post('tahun');
		$id_user		= $this->session->userdata('id_dd_user');
		
		$qprestasi		= $this->_getPrestasi($id_user,$tahun);
		
		if($qprestasi->num_rows() == 0)
		{
			$this->load->view('v_info');
			return;
		}
		
		$prestasi		= $qprestasi->row();
		
		$skp_tahunan    = $this->_getSKPTahunan($prestasi->id_dd_user);	
        $nilai_akhir    = $this->_getNilaiAkhir($prestasi->id_dd_user,$prestasi->tahun)->row();	
		$instansi       = $this->_get_instansi($id_instansi)->row();
		
		$this->load->library('PDFTC', array());
		$this->pdftc->setTitle_Header('Prestasi Kerja');
		$this->pdftc->setPrintHeader(false);
		$this->pdftc->setPrintFooter(false);
		$this->pdftc->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdftc->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$this->pdftc->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->pdftc->SetMargins(10, 35, PDF_MARGIN_RIGHT);
		$this->pdftc->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdftc->SetFooterMargin(PDF_MARGIN_FOOTER);
		$this->pdftc->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		$this->pdftc->AddPage('P', 'A4');
		
		/* Page Border Create*/
		$this->pdftc->SetLineStyle( array( 'width' => 0, 'color' => array(0,0,0)));
		$this->pdftc->Rect(10,10, $this->pdftc->getPageWidth()-20, $this->pdftc->getPageHeight()-20);
		/* Create Image Logo*/
		$this->pdftc->Image(base_url().'assets/img/garuda.png', 0, 30, 30,30, '','','',false,300,'C');
		
		$this->pdftc->SetFont('courier', 'B', 16);
		$this->pdftc->Text(15,75, 'PENILAIAN PRESTASI KERJA',false,false,true,0,0, 'C');
		$this->pdftc->Text(15,80, 'PEGAWAI NEGERI SIPIL',false,false,true,0,0, 'C');
		$this->pdftc->Text(15,85, 'TAHUN '.$prestasi->tahun,false,false,true,0,0, 'C');
			
		$this->pdftc->Text(15,105, 'Jangka Waktu Penilaian',false,false,true,0,0, 'C');
		$this->pdftc->Text(15,110, '01 Januari '.$prestasi->tahun.' s/d 31 Desember '.$prestasi->tahun,false,false,true,0,0, 'C');
		
		$this->pdftc->SetFont('courier', '', 11);
        $this->pdftc->Text(45,145, 'Nama Pegawai',false,false,true,0,0, 'L');
		$this->pdftc->Text(100,145, ': '.$prestasi->nama,false,false,true,0,0, 'L');
		
		$this->pdftc->Text(45,150, 'NIP',false,false,true,0,0, 'L');
		$this->pdftc->Text(100,150, ': '.$prestasi->nip,false,false,true,0,0, 'L');
		
		$this->pdftc->Text(45,155, 'Pangkat/Golongan Ruang',false,false,true,0,0, 'L');
		$this->pdftc->Text(100,155, ': '.$prestasi->Golongan.', '.$prestasi->Pangkat,false,false,true,0,0, 'L');
		
		$this->pdftc->Text(45,160, 'Jabatan',false,false,true,0,0, 'L');
		$this->pdftc->Text(100,160, ': '.$prestasi->jabatan,false,false,true,0,0, 'L');
		
		$this->pdftc->Text(45,165, 'Unit Kerja',false,false,true,0,0, 'L');
		$this->pdftc->Text(100,165, ':',false,false,true,0,0, 'L');
		
		if($prestasi->jenis_jabatan =='2')
		{
			
		    $this->pdftc->MultiCell(75, 5, ucwords(strtolower($prestasi->unitkerja_atasan_2)), 0, 'L', 0, 0, 105, 165, true);
		}
		else
		{
            $this->pdftc->MultiCell(75, 5, ucwords(strtolower($prestasi->unitkerja_atasan_langsung)), 0, 'L', 0, 0, 105, 165, true);
			//$this->pdftc->Text(100,165, ': '.ucwords(strtolower($prestasi->unitkerja_atasan_langsung)),false,false,true,0,0, 'L');
		}
		$this->pdftc->SetFont('courier', 'B', 18);
		//$this->pdftc->Text(15,240, strtoupper($instansi->nama_instansi),false,false,true,0,0, 'C');
		$this->pdftc->MultiCell(150, 5, ucwords(strtoupper($instansi->nama_instansi)), 0, 'C', 0, 0, 32, 240, true);
		//$this->pdftc->Text(15,245, 'TAHUN '.$prestasi->tahun,false,false,true,0,0, 'C');
		
	
			
		/* LOOP FORMULIR SASARAN KERJA */				
		foreach($skp_tahunan->result() as $value)
		{
			$kegiatan		= $this->_getKegiatanTahunan($value->id_dd_user,$value->id_opmt_tahunan_skp);				
			$tbl ='<table cellspacing="0" cellpadding="1" border="1">
			<tr style="font-weight:bold;">
				<td width="30" align="center">NO</td>
				<td colspan="3" width="365">I. PEJABAT PENILAI</td>
				<td width="40" align="center">NO</td>
				<td colspan="3" width="365">II. PEGAWAI NEGERI SIPIL YANG DINILAI</td>
			</tr>
			<tr>
				<td width="30" align="center">1.</td>
				<td width="125">Nama</td><td width="240">: '.$prestasi->nama_atasan_langsung.'</td>
				<td width="40" align="center">1.</td>
				<td width="125">Nama</td><td width="240">: '.$prestasi->nama.' </td>
			</tr>
			<tr>
				<td width="30" align="center">2.</td>
				<td width="125">NIP</td><td width="240">: '.$prestasi->nip_atasan_langsung.'</td>
				<td width="40" align="center">2.</td>
				<td width="125">NIP</td><td width="240">: '.$prestasi->nip.'</td>
			</tr>
			
			<tr>
				<td width="30" align="center">3.</td>
				<td width="125">Pangkat/Gol.Ruang</td><td width="240">: '.$prestasi->golongan_atasan_langsung.', '.$prestasi->pangkat_atasan_langsung.'</td>
				<td width="40" align="center">3.</td>
				<td width="125">Pangkat/Gol.Ruang</td><td width="240">: '.$prestasi->Golongan.', '.$prestasi->Pangkat.'</td>
			</tr>
			
			<tr>
				<td width="30" align="center">4.</td>
				<td width="125">Jabatan</td><td width="240">: '.$prestasi->jabatan_atasan_langsung.'</td>
				<td width="40" align="center">4.</td>
				<td width="125">Jabatan</td><td width="240">: '.$prestasi->jabatan.'</td>
			</tr>
			
			<tr>
				<td width="30" align="center">5.</td>
				<td width="125">Unit Kerja</td><td width="240">: '.$prestasi->unitkerja_atasan_langsung.'</td>
				<td width="40" align="center">5.</td>
				<td width="125">Unit Kerja</td><td width="240">: '.$prestasi->unitkerja_atasan_langsung.'</td>
			</tr>
			
			<tr style="font-weight:bold;" >
				<td width="30" rowspan="2" align="center">NO</td>
				<td width="365" rowspan="2" align="center">III. KEGIATAN TUGAS JABATAN</td>
				<td width="40" rowspan="2" align="center">AK</td>
				<td width="365" align="center">TARGET</td>
			</tr>
			<tr style="font-weight:bold;">
				<td width="100" align="center">KUANT/OUTPUT</td>
				<td width="70" align="center">MUTU</td>
				<td width="100" align="center">WAKTU</td>
				<td width="95" align="center">BIAYA</td>
			</tr>
			<tr bgcolor="#d0e1e1">
				<td width="30"  align="center">1</td>
				<td width="365" align="center">2</td>
				<td width="40" align="center">3</td>
				<td width="100" align="center">4</td>
				<td width="70" align="center">5</td>
				<td width="100" align="center">6</td>
				<td width="95" align="center">7</td>							
			</tr>';
			$no = 1;
			foreach($kegiatan->result() as $val){
				
				$tbl .='<tr>
					<td width="30"  align="center">'.$no.'.</td>';
				
				if(!is_null($val->angka_kredit)){	
					$tbl .='<td width="365" align="left"> '.$val->kegiatan_tahunan.'('.$val->angka_kredit.'/'.$value->satuan_kuantitas.')</td>
					<td width="40" align="center"> '.($val->angka_kredit*$val->target_kuantitas).'</td>';
				}else{
				    $tbl .='<td width="365" align="left"> '.$val->kegiatan_tahunan.'</td>
					<td width="40" align="center">-</td>';
				
				}	
				$tbl .='<td width="100" align="center">'.$val->target_kuantitas.' '.$value->satuan_kuantitas.'</td>
					<td width="70" align="center">100</td>
					<td width="100" align="center">'.$val->target_waktu.' bulan</td>
					<td width="95" align="center">'.$val->target_biaya.'</td>
				</tr>';
				$no++;
			}		
				$tbl .='</table>';
		   
		    $this->pdftc->AddPage('L', 'A4');
			$this->pdftc->SetFont('courier', 'B', 14);
			$this->pdftc->Setxy(0,12);
			$this->pdftc->Write(0, '   FORMULIR SASARAN KERJA', '', 0, 'C', true, 0, false, false, 0);
			$this->pdftc->Write(0, 'PEGAWAI NEGERI SIPIL', '', 0, 'C', true, 0, false, false, 0);		
			$this->pdftc->SetFont('freesans', '', 10);		

			
			$this->pdftc->SetXY(50, 160);
			$this->pdftc->Cell(30, 0, 'Pejabat Penilai,', 0,0, 'C', 0, '', 0, false, 'B', 'B');
			$this->pdftc->SetXY(50, 177);
			$this->pdftc->Cell(30, 0, $prestasi->nama_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(50, 180);
			$this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
						
			$this->pdftc->SetXY(235, 155);
			$this->pdftc->Cell(30, 0, $prestasi->lokasi_spesimen.', '.$value->format_awal_periode_skp, 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 160);
			$this->pdftc->Cell(30, 0, 'Pegawai Negeri Sipil Yang Dinilai,', 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 177);
			$this->pdftc->Cell(30, 0,$prestasi->nama, 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 180);
			$this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip, 0,0, 'C', 0, '', 0, false, 'B', 'B');				
			$this->pdftc->SetY(25);		
			$this->pdftc->writeHTML($tbl, true, false, false, false, '');		
		
		}	/* END LOOP FORMULIR SASARAN KERJA */	
			
					
		/* LOOP PENGUKURAN */        		
        foreach($skp_tahunan->result() as $val)
		{
			$kegiatan		= $this->_getKegiatanTahunan($val->id_dd_user,$val->id_opmt_tahunan_skp);
			$tugas_tambahan = $this->_getTugasTambahan($val->id_dd_user,$val->awal_periode_skp,$val->akhir_periode_skp);
			
			$this->pdftc->AddPage('L', 'A4');
			$this->pdftc->SetMargins(5,0,5);
			$this->pdftc->SetFont('courier', 'B', 14);
			$this->pdftc->Setxy(0,12);
			$this->pdftc->Write(0, 'PENILAIAN CAPAIAN SASARAN KERJA', '', 0, 'C', true, 0, false, false, 0);
			$this->pdftc->Write(0, 'PEGAWAI NEGERI SIPIL', '', 0, 'C', true, 0, false, false, 0);
			$this->pdftc->SetFont('freesans', '', 10);
			$this->pdftc->Write(0, 'Jangka Waktu Penilaian : '.$val->format_awal_periode_skp.' s/d '.$val->format_akhir_periode_skp, '', 0, 'L', true, 0, false, false, 0);
			$this->pdftc->SetFont('freesans', '', 8);
			$tbl ='		
				<table cellspacing="0" cellpadding="1" border="1" style="border-collapse: collapse; ">    
			<tr style="font-weight:bold;">
				<td width="20" rowspan="2" align="center">NO</td>
				<td width="230" rowspan="2" align="center">I. KEGIATAN TUGAS JABATAN</td>
				<td width="30" rowspan="2" align="center">AK</td>
				<td width="190" align="center">TARGET</td>
				<td width="30" rowspan="2" align="center">AK</td>
				<td width="190" align="center">REALISASI</td>
				
				<td width="75" align="center" rowspan="2">PENGHITUNGAN</td>
				<td width="50" align="center" rowspan="2">NILAI<br/>CAPAIAN<br/>SKP</td>
			</tr>
			<tr style="font-weight:bold;">
				<td width="70" align="center">KUANT/<br/>OUTPUT</td>
				<td width="30" align="center">MUTU</td>
				<td width="40" align="center">WAKTU</td>
				<td width="50" align="center">BIAYA</td>
				
				<td width="70" align="center">KUANT/<br/>OUTPUT</td>
				<td width="30" align="center">MUTU</td>
				<td width="40" align="center">WAKTU</td>
				<td width="50" align="center">BIAYA</td>
			</tr>
			
			<tr bgcolor="#d0e1e1">
				<td width="20"  align="center">1</td>
				<td width="230" align="center">2</td>
				<td width="30" align="center">3</td>
				<td width="70" align="center">4</td>
				<td width="30" align="center">5</td>
				<td width="40" align="center">6</td>
				<td width="50" align="center">7</td>
				<td width="30" align="center">8</td>
				
				<td width="70" align="center">9</td>
				<td width="30" align="center">10</td>
				<td width="40" align="center">11</td>
				<td width="50" align="center">12</td>
				
				<td width="75" align="center">13</td>
				<td width="50" align="center">14</td>
			</tr>';
			$no 		= 1;
			$total_nilai= 0;
			$total_nilai_capaian_skp = 0;
			$jumlah_kegiatan = $kegiatan->num_rows();
			foreach ($kegiatan->result() as $value)
			{
				$tbl .='<tr>
					<td width="20"  align="center">'.$no.'.</td>';
					if(!IS_NULL($value->angka_kredit)){
						$tbl .='
						<td width="230" align="left">'. $value->kegiatan_tahunan.'('.$value->angka_kredit.'/'.$value->satuan_kuantitas.')</td>
						<td width="30" align="center">'.$value->angka_kredit*$value->target_kuantitas.'</td>';
					}else{
					    $tbl .='<td width="230" align="left">'. $value->kegiatan_tahunan.'</td>
						<td width="30" align="center">-</td>';
					}						
					$tbl .='<td width="70" align="center">'.$value->target_kuantitas.' '.$value->satuan_kuantitas.'</td>
					<td width="30" align="center">100</td>
					<td width="40" align="center">'.$value->target_waktu.' bulan</td>
					<td width="50" align="center">'.$value->target_biaya.'</td>';
					if(!IS_NULL($value->angka_kredit)){						
						$tbl .='<td width="30" align="center">'.$value->angka_kredit*$value->realisasi_kuantitas.'</td>';
					}else{
						$tbl .='<td width="30" align="center">-</td>';
                    }
 					
					$tbl .='<td width="70" align="center">'.$value->realisasi_kuantitas.' '.$value->satuan_kuantitas.'</td>';
					
					$tbl .='
					<td width="30" align="center">'.$value->realisasi_kualitas.'</td>
					<td width="40" align="center">'.$value->realisasi_waktu.' bulan</td>
					<td width="50" align="center">'.$value->realisasi_biaya.'</td>
					
					<td width="75" align="center">'.ROUND($value->perhitungan,2).'</td>
					<td width="50" align="center">'.ROUND($value->nilai,2).'</td>
				</tr>';
				
				$total_nilai	= $total_nilai +  $value->nilai;
				$no++;
			}	
			
			$tbl .= '<tr style="font-weight:bold;" bgcolor="#d0e1e1">
				<td width="765" align="center" colspan="6"> Jumlah Nilai SKP</td>
				<td width="50" align="center" >'.ROUND($total_nilai/$jumlah_kegiatan,2).'</td>
			</tr>';
			
			$tbl .= '<tr style="font-weight:bold;">
				<td width="20"  align="center"></td>
				<td width="230" align="left">II. TUGAS TAMBAHAN DAN KREATIVITAS/UNSUR PENUNJANG:</td>
				<td width="30"  align="center"></td>
				<td width="190" align="center"></td>
				<td width="220" align="center"></td>
				<td width="75" align="center" ></td>
				<td width="50" align="center" ></td>
			</tr>';
			
			
			$no = 1;
			$total_tugas = 	$tugas_tambahan->num_rows();
			if ($total_tugas == 0) {
				$nilai_tgs = 0;
			} else if ($total_tugas <= 3) {
				$nilai_tgs = 1;
			} else if ($total_tugas <= 6) {
				$nilai_tgs = 2;
			} else if ($total_tugas <= 7) {
			    $nilai_tgs = 3;
			}else {
				$nilai_tgs = 3;
			}
			foreach($tugas_tambahan->result() as $value)
			{
				$tbl .='<tr>
					<td width="20"  align="center">'.$no.'.</td>
					<td width="230" align="left">'. $value->tugas_tambahan.'</td>
					<td width="30"  align="center"></td>
					<td width="190" align="center"></td>
					<td width="220" align="center"></td>
					<td width="75" align="center" ></td>';
					if($no == 1){
						$tbl .='<td width="50" align="center" valign="bottom" rowspan="'.$total_tugas.'">'.$nilai_tgs.'</td></tr>';
					}else {
						$tbl .='</tr>';
					}
					
				$no++;
			}
			
			$nilai_capai_skp =($total_nilai/$jumlah_kegiatan)+ $nilai_tgs;			
			$total_nilai_capaian_skp = $total_nilai_capaian_skp + $nilai_capai_skp;
			
			
			$tbl .='<tr style="font-weight:bold;font-size:10;">
				<td colspan="6"  align="center">NILAI CAPAIAN SKP</td>        
				<td width="50" align="center" >'.ROUND($nilai_capai_skp,2).'</td>
			</tr>
		</table>';

			$this->pdftc->SetFont('freesans', '', 9);
			$this->pdftc->SetXY(235, 165);
			$this->pdftc->Cell(30, 0, $prestasi->lokasi_spesimen.', '.$val->format_akhir_periode_skp, 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 169);
			$this->pdftc->Cell(30, 0, 'Pejabat Penilai,', 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 178);
			$this->pdftc->Cell(30, 0, $prestasi->nama_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
			
			$this->pdftc->SetXY(235, 181);
			$this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
			$this->pdftc->SetY(30);
			$this->pdftc->writeHTML($tbl, true, false, true, false, '');
		} 
		/* END LOOP PENGUKURAN*/	
        $this->pdftc->AddPage('L', 'A4');
		$this->pdftc->SetFont('helvetica', '', 10);
		$this->pdftc->SetMargins(3, 0, 3,true);
		$this->pdftc->SetAutoPageBreak(FALSE, 5);
		$tbl = <<<EOD
<table cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td width="400" height="207"> 8. REKOMENDASI</td>
        
    </tr>
    <tr>
       <td width="400" height="350"></td>	   
    </tr>
	
</table>
EOD;

		
		$this->pdftc->SetXY(75, 90);
        $this->pdftc->Cell(30, 0, '9. DIBUAT TANGGAL, 31 Desember '.$prestasi->tahun, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(75, 95);
        $this->pdftc->Cell(30, 0, 'Pejabat Penilai,', 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(75, 110);
        $this->pdftc->Cell(30, 0, $prestasi->nama_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		$this->pdftc->SetXY(75, 115);
        $this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip_atasan_langsung, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		
		$this->pdftc->SetXY(25, 125);
        $this->pdftc->Cell(30, 0, '10. DITERIMA TANGGAL, 5 Januari '.date('Y',strtotime('+1 years',strtotime($prestasi->tahun))), 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(25, 130);
        $this->pdftc->Cell(30, 0, 'Pegawai Negeri Sipil Yang Dinilai', 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(25, 145);
        $this->pdftc->Cell(30, 0, $prestasi->nama, 0,0, 'C', 0, '', 0, false, 'B', 'B');
				
		$this->pdftc->SetXY(25, 150);
        $this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip, 0,0, 'C', 0, '', 0, false, 'B', 'B');		
		
		$this->pdftc->SetXY(80, 160);
        $this->pdftc->Cell(30, 0, '11. DITERIMA TANGGAL, 7 Januari '.date('Y',strtotime('+1 years',strtotime($prestasi->tahun))), 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(80, 165);
        $this->pdftc->Cell(30, 0, 'Atasan Pejabat Penilai', 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		$this->pdftc->SetXY(80, 185);
        $this->pdftc->Cell(30, 0, $prestasi->nama_atasan_2, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		$this->pdftc->SetXY(80, 190);
        $this->pdftc->Cell(30, 0, 'NIP.'.$prestasi->nip_atasan_2, 0,0, 'C', 0, '', 0, false, 'B', 'B');
		
		
        $this->pdftc->SetY(5);		
		$this->pdftc->writeHTML($tbl, true, false, false, false, '');
		
		$this->pdftc->Image(base_url().'assets/img/garuda.png',215, 5, 20,20, '','','',false,300,'T');
		$this->pdftc->SetFont('freesans', 'B', 10);
		$this->pdftc->Text(160,25, 'PENILAIAN PRESTASI KERJA',false,false,true,0,0, 'C');
		$this->pdftc->Text(160,29, 'PEGAWAI NEGERI SIPIL',false,false,true,0,0, 'C');
		
		
		$this->pdftc->SetFont('freesans', '', 10);
		//$this->pdftc->Text(150,35, 'BADAN KEPEGAWAIAN NEGARA',false,false,true,0,0, 'L');
		$this->pdftc->MultiCell(75, 5, ucwords(strtoupper($instansi->nama_instansi)), 0, 'L', 0, 0, 149, 34, true);
		//$this->pdftc->Text(150,39, 'KANTOR REGIONAL XI',false,false,true,0,0, 'L');
		
		$this->pdftc->Text(236,34, 'JANGKA WAKTU PENILAIAN',false,false,true,0,0, 'L');
		$this->pdftc->Text(236,38, 'BULAN :  Januari s/d Desember '.$prestasi->tahun,false,false,true,0,0, 'L');
		
		$unor = ucwords(strtolower($prestasi->unitkerja_atasan_2));
		
		$tbl = <<<EOD
<table cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td width="20" rowspan="6"> 1.</td>
		<td width="387" colspan="2"> YANG DI NILAI<br/></td>       
    </tr>
    <tr>
	   
       <td width="160"> a. Nama</td>
	   <td width="227"> $prestasi->nama<br/></td>
    </tr>
	
	<tr>
	   
       <td width="160"> b. NIP</td>
	   <td width="227"> $prestasi->nip<br/></td>
    </tr>
	
	<tr>
	  
       <td width="160"> c. Pangkat, Golongan Ruang</td>
	   <td width="227"> $prestasi->Pangkat, $prestasi->Golongan<br/></td>
    </tr>
	
	<tr>
	  
       <td width="160"> d. Jabatan/Pekerjaan</td>
	   <td width="227"> $prestasi->jabatan<br/></td>
    </tr>
	
	<tr>
	   
       <td width="160"> e. Unit Organisasi</td>
	   <td width="227"> $unor</td>
    </tr>
	
	<tr>
        <td width="20" rowspan="6"> 2.</td>
		<td width="387" colspan="2"> PEJABAT PENILAI<br/></td>       
    </tr>
	
	<tr>
        <td width="160"> a. Nama</td>
	    <td width="227"> $prestasi->nama_atasan_langsung<br/></td>     
    </tr>
	
	<tr>
       	<td width="160"> b. NIP</td>
	    <td width="227"> $prestasi->nip_atasan_langsung<br/></td>     
    </tr>
	
	<tr>
	  
       <td width="160"> c. Pangkat, Golongan Ruang</td>
	   <td width="227"> $prestasi->pangkat_atasan_langsung, $prestasi->golongan_atasan_langsung<br/></td>
    </tr>
	
	<tr>
	  
       <td width="160"> d. Jabatan/Pekerjaan</td>
	   <td width="227"> $prestasi->jabatan_atasan_langsung</td>
    </tr>
	
	<tr>
	   
       <td width="160"> e. Unit Organisasi</td>
	   <td width="227"> $unor</td>
    </tr>
	
	<tr>
        <td width="20" rowspan="6"> 3.</td>
		<td width="387" colspan="2"> ATASAN PEJABAT PENILAI<br/></td>       
    </tr>
	
	<tr>
        <td width="160"> a. Nama</td>
	    <td width="227"> $prestasi->nama_atasan_2<br/></td>     
    </tr>
	
	<tr>
       	<td width="160"> b. NIP</td>
	    <td width="227"> $prestasi->nip_atasan_2<br/></td>     
    </tr>
	
	<tr>
	  
       <td width="160"> c. Pangkat, Golongan Ruang</td>
	   <td width="227"> $prestasi->pangkat_atasan_2,  $prestasi->golongan_atasan_2<br/></td>
    </tr>
	
	<tr>
	  
       <td width="160"> d. Jabatan/Pekerjaan</td>
	   <td width="227"> $prestasi->jabatan_atasan_2</td>
    </tr>
	
	<tr>
	   
       <td width="160"> e. Unit Organisasi</td>
	   <td width="227"> $unor</td>
    </tr>
	

</table>
EOD;
		
		$this->pdftc->writeHTMLCell(0, 0, 150, 43, $tbl,0,0,false,true,'T',true);	
        
		// CREATE PENILAIAN
		$this->pdftc->AddPage('L', 'A4');
		$this->pdftc->SetFont('helvetica', '', 10);
		$this->pdftc->SetMargins(3, 0, 3,true);
		$this->pdftc->SetAutoPageBreak(FALSE, 5);
		
		if($prestasi->jenis_jabatan != 2){
			 $style=" ";
     	}
		else{           
			$style="color:#d0e1e1;background-color: #d0e1e1";
		}
		
		
		$tbl = <<<EOT
<table cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td rowspan="11" width="20">4. </td>
		<td width="330"> UNSUR YANG DINILAI</td>
		<td width="50" align="center">JUMLAH<br/></td>		        
    </tr>
    <tr>
        <td width="230" align="left"> a. Sasaran Kerja Pegawai (SKP)</td> 
        <td width="50" align="center">$nilai_akhir->sasaran_kerja_pegawai</td>
        <td width="50" align="center"> x 60%</td>
        <td width="50" align="center"> $nilai_akhir->final_60_persen_nilai_capaian_skp<br/></td>		
    </tr>
	<tr>
        <td width="100" align="left" rowspan="9"> b. Perilaku Kerja</td> 
        <td width="130" align="left"> 1. Orientasi Pelayanan</td>
        <td width="50" align="center"> $nilai_akhir->orientasi_pelayanan </td>
        <td width="50" align="center"> $nilai_akhir->label_orientasi_pelayanan</td>
        <td width="50" style="color:#d0e1e1;background-color: #d0e1e1" rowspan="8"></td>		
    </tr>
	
	<tr>
        <td width="130" align="left"> 2. Integritas</td>
        <td width="50" align="center"> $nilai_akhir->integritas </td>
        <td width="50" align="center"> $nilai_akhir->label_integritas</td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> 3. Komitmen</td>
        <td width="50" align="center"> $nilai_akhir->komitmen </td>
        <td width="50" align="center"> $nilai_akhir->label_komitmen</td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> 4. Disiplin</td>
        <td width="50" align="center"> $nilai_akhir->disiplin </td>
        <td width="50" align="center"> $nilai_akhir->label_disiplin</td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> 5. Kerjasama</td>
        <td width="50" align="center"> $nilai_akhir->kerjasama</td>
        <td width="50" align="center"> $nilai_akhir->label_kerjasama</td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> 6. Kepemimpinan</td>
        <td width="50" align="center" style="$style"> $nilai_akhir->kepemimpinan </td>
        <td width="50" align="center" style="$style"> $nilai_akhir->label_kepemimpinan</td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> Jumlah</td>
        <td width="50" align="center"> $nilai_akhir->jumlah_perilaku </td>
        <td width="50" align="center" style="color:#d0e1e1;background-color: #d0e1e1"> - </td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> Nilai Rata-Rata</td>
        <td width="50" align="center"> $nilai_akhir->rata_rata_nilai_perilaku </td>
        <td width="50" align="center">$nilai_akhir->label_rata_rata_nilai_perilaku<br/></td>	
    </tr>
	
	<tr>
        <td width="130" align="left"> Nilai Perilaku Kerja</td>
        <td width="50" align="center"> $nilai_akhir->rata_rata_nilai_perilaku </td>
        <td width="50" align="center"> x 40%</td>
        <td width="50" align="center"> $nilai_akhir->final_40_persen_nilai_perilaku_kerja<br/></td>		
    </tr>
	
	
	<tr style="font-weight:bold;font-size:10;">
        <td width="20" rowspan="2"></td>
		<td align="left" colspan="4" rowspan="2"> NILAI PRESTASI KERJA</td>
        <td width="50" align="center">$nilai_akhir->final_nilai_prestasi_kerja<br/></td>		
    </tr>
	
	<tr style="font-weight:bold;">
       <td width="50" align="center">$nilai_akhir->label_final_nilai_prestasi_kerja</td>		
    </tr>
	
	
	<tr>
        <td align="left" colspan="6" height="323"> 5. KEBERATAN DARI PEGAWAI NEGERI SIPIL YANG DINILAI(APABILA ADA)</td>
             
    </tr>
	
	
	
</table>
EOT;

		
		$this->pdftc->SetY(5);
		
		$this->pdftc->writeHTML($tbl, true, false, false, false, '');
		
		$tbl = <<<EOD
<table cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td height="260"> 6. TANGGAPAN PEJABAT PENILAI ATAS KEBERATAN </td>				        
    </tr>
	 <tr>
        <td height="300"> 7. KEPUTUSAN ATASAN PEJABAT PENILAI ATAS KEBERATAN </td>				        
    </tr>
	</table>
EOD;
		
		$this->pdftc->writeHTMLCell(0, 0, 150, 5, $tbl,0,0,false,true,'T',true);
		
		$this->pdftc->Output('e-Prestasi Kerja_'.$prestasi->nip.'.pdf', 'D');
		
	}
	
	public function dompdf()
	{
		$this->load->library('PDF', array());
		$this->pdf->load_view('welcome_message');
		// (Optional) Setup the paper size and orientation
		$this->pdf->set_paper('A4', 'portrait');
		$this->pdf->render();
		$this->pdf->stream("SKP.pdf");
		
	}
	
	function _getPrestasi($id_user, $tahun){
	    
		$sql="SELECT c.nip,c.id_dd_user,c.nama,o.lokasi_spesimen,
		m.Golongan,m.Pangkat, n.jabatan,a.tahun,
c.atasan_langsung,c.atasan_2, c.atasan_3,
d.nip nip_atasan_langsung,
d.nama nama_atasan_langsung,
e.jabatan jabatan_atasan_langsung,
f.Golongan golongan_atasan_langsung,
f.Pangkat pangkat_atasan_langsung,
g.nip nip_atasan_2,
g.nama nama_atasan_2,
h.jabatan jabatan_atasan_2,
i.Golongan golongan_atasan_2,
i.Pangkat pangkat_atasan_2,
CASE
    WHEN j.jenis='JFT' THEN 2
    WHEN j.jenis='JST' THEN 1
    WHEN j.jenis='JFU' THEN 4
    ELSE 3
END
jenis_jabatan,
k.unitkerja unitkerja_atasan_langsung,
l.unitkerja unitkerja_atasan_2
FROM ekinerja.opmt_bulanan_skp a
LEFT JOIN ekinerja.dd_user c on a.id_dd_user = c.id_dd_user
LEFT JOIN ekinerja.tblgolongan m ON c.gol_ruang  = m.KodeGol
LEFT JOIN ekinerja.tbljabatan n on c.jabatan = n.kodejab
LEFT JOIN ekinerja.dd_user d on c.atasan_langsung = d.id_dd_user
LEFT JOIN ekinerja.tbljabatan e on d.jabatan = e.kodejab
LEFT JOIN ekinerja.tblgolongan f ON d.gol_ruang  = f.KodeGol 
LEFT JOIN ekinerja.dd_user g on c.atasan_2 = g.id_dd_user
LEFT JOIN ekinerja.tbljabatan h on g.jabatan = h.kodejab
LEFT JOIN ekinerja.tblgolongan i ON g.gol_ruang  = i.KodeGol 
LEFT JOIN ekinerja.tbljabatan j on c.jabatan = j.kodejab
LEFT JOIN ekinerja.tblstruktural k ON d.unit_kerja = k.kodeunit
LEFT JOIN ekinerja.tblstruktural l ON g.unit_kerja = l.kodeunit
LEFT JOIN ekinerja.dd_spesimen o ON c.lok_ker = o.id_dd_spesimen
WHERE a.tahun='$tahun' AND a.id_dd_user='$id_user' AND a.nilai_skp != 0
GROUP BY a.id_dd_user";
		
		$query		= $this->db->query($sql);
		
		return $query;
		
	}

	function _getKegiatanTahunan($id_user,$id_tahun)
	{
	    $sql	="SELECT c.*, 
ROUND(c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu,2) perhitungan,
CASE
	WHEN c.target_biaya > 0 THEN ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/4,3)
    ELSE ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/3,3)
END
nilai
FROM (SELECT b.*,
CASE
    WHEN b.persen_waktu <=24 THEN ((1.76 * b.target_Waktu - b.realisasi_waktu ) / b.target_waktu) * 100
    ELSE 76 - ((((1.76 * b.target_waktu - b.realisasi_waktu) / b.target_waktu ) * 100 ) - 100 )
END
nilai_waktu,
CASE
	WHEN b.persen_biaya <= 24 THEN  ((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya) * 100
    ELSE 76 - ((((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya ) * 100 ) - 100 )
END
nilai_biaya
FROM ( SELECT a.*,
(realisasi_kuantitas/target_kuantitas) * 100 nilai_kuantitas,
(realisasi_kualitas/100) * 100 nilai_kualitas,
100 - ( (realisasi_waktu/target_waktu) * 100) persen_waktu,
100 - ( (realisasi_biaya/target_biaya) * 100) persen_biaya
FROM 
(SELECT a.id_opmt_target_skp, a.id_opmt_detail_kegiatan_jabatan, f.angka_kredit, a.kegiatan_tahunan,a.target_kuantitas,
b.satuan_kuantitas,a.target_waktu,a.biaya target_biaya,
CASE 
	WHEN ISNULL(sum(c.kuantitas)) THEN 0
    ELSE sum(c.kuantitas)
END
realisasi_kuantitas,
CASE 
	WHEN ISNULL(d.kualitas) THEN 0
    ELSE d.kualitas
END
realisasi_kualitas,d.waktu realisasi_waktu,d.biaya realisasi_biaya, a.id_dd_user, 
e.id_opmt_tahunan_skp, e.awal_periode_skp, e.akhir_periode_skp
FROM ekinerja.opmt_target_skp a
LEFT JOIN ekinerja.dd_kuantitas b on a.satuan_kuantitas = b.id_dd_kuantitas
LEFT JOIN ekinerja.opmt_realisasi_harian_skp c ON a.id_opmt_target_skp = c.id_opmt_target_skp AND c.proses=0
LEFT JOIN ekinerja.opmt_realisasi_skp d ON a.id_opmt_target_skp = d.id_opmt_target_skp
LEFT JOIN ekinerja.opmt_tahunan_skp e ON e.id_opmt_tahunan_skp = a.id_opmt_tahunan_skp
LEFT JOIN ekinerja.opmt_detail_kegiatan_jabatan f ON a.id_opmt_detail_kegiatan_jabatan = f.id_opmt_detail_kegiatan_jabatan
WHERE a.id_dd_user='$id_user'
GROUP BY a.id_opmt_target_skp
 ) a) b ) c WHERE c.id_opmt_tahunan_skp ='$id_tahun' ";
		$query	=$this->db->query($sql);
		return $query;
	}
	
	
	function _getTugasTambahan($id,$start,$end)
	{
		$sql="SELECT a.* FROM ekinerja.opmt_tugas_tambahan  a
WHERE a.id_dd_user='$id' AND date(a.tanggal) between '$start' AND '$end' ";
        $query	=$this->db->query($sql);
		return $query;
	}
	
	function _getSKPTahunan($id_user)
	{
		/* fungsi untuk melihat beratap bayak peride SKP yang di buat dalam satu tahun */
		$sql="SELECT d.* ,ROUND((SUM(d.nilai))/count(d.nilai),2) nilai_capaian_skp,
		ekinerja.sf_formatTanggal(d.awal_periode_skp) format_awal_periode_skp,
		ekinerja.sf_formatTanggal(d.akhir_periode_skp) format_akhir_periode_skp
FROM 
(SELECT c.*, 
ROUND(c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu,2) perhitungan,
CASE
	WHEN c.target_biaya > 0 THEN ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/4,3)
    ELSE ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/3,3)
END
nilai
FROM (SELECT b.*,
CASE
    WHEN b.persen_waktu <=24 THEN ((1.76 * b.target_Waktu - b.realisasi_waktu ) / b.target_waktu) * 100
    ELSE 76 - ((((1.76 * b.target_waktu - b.realisasi_waktu) / b.target_waktu ) * 100 ) - 100 )
END
nilai_waktu,
CASE
	WHEN b.persen_biaya <= 24 THEN  ((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya) * 100
    ELSE 76 - ((((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya ) * 100 ) - 100 )
END
nilai_biaya
FROM ( SELECT a.*,
(realisasi_kuantitas/target_kuantitas) * 100 nilai_kuantitas,
(realisasi_kualitas/100) * 100 nilai_kualitas,
100 - ( (realisasi_waktu/target_waktu) * 100) persen_waktu,
100 - ( (realisasi_biaya/target_biaya) * 100) persen_biaya
FROM 
(SELECT a.id_opmt_target_skp, a.kegiatan_tahunan,a.target_kuantitas,
b.satuan_kuantitas,a.target_waktu,a.biaya target_biaya,
CASE 
	WHEN ISNULL(sum(c.kuantitas)) THEN 0
    ELSE sum(c.kuantitas)
END
realisasi_kuantitas,
CASE 
	WHEN ISNULL(d.kualitas) THEN 0
    ELSE d.kualitas
END
realisasi_kualitas,d.waktu realisasi_waktu,d.biaya realisasi_biaya, a.id_dd_user, 
e.id_opmt_tahunan_skp, e.awal_periode_skp, e.akhir_periode_skp
FROM ekinerja.opmt_target_skp a
LEFT JOIN ekinerja.dd_kuantitas b on a.satuan_kuantitas = b.id_dd_kuantitas
LEFT JOIN ekinerja.opmt_realisasi_harian_skp c ON a.id_opmt_target_skp = c.id_opmt_target_skp AND c.proses=0
LEFT JOIN ekinerja.opmt_realisasi_skp d ON a.id_opmt_target_skp = d.id_opmt_target_skp
INNER JOIN ekinerja.opmt_tahunan_skp e ON e.id_opmt_tahunan_skp = a.id_opmt_tahunan_skp
WHERE a.id_dd_user='$id_user'
GROUP BY a.id_opmt_target_skp
 ) a) b ) c ) d 
 GROUP BY d.id_opmt_tahunan_skp  
 ORDER BY d.awal_periode_skp asc";
 
        $query	=$this->db->query($sql);
		return $query;
	}
	
	
	function _getNilaiAkhir($id_user,$tahun_skp)
	
	{
	    $sql="SELECT m.*,
CASE
    WHEN (m.rata_rata_nilai_perilaku <=50 or isnull(m.rata_rata_nilai_perilaku))  THEN 'Buruk'
    WHEN m.rata_rata_nilai_perilaku <=60  THEN 'Sedang'
    WHEN m.rata_rata_nilai_perilaku <=75  THEN  'Cukup'
    WHEN m.rata_rata_nilai_perilaku <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_rata_rata_nilai_perilaku,
CASE
    WHEN (m.final_nilai_prestasi_kerja <=50 or isnull(m.final_nilai_prestasi_kerja))  THEN 'Buruk'
    WHEN m.final_nilai_prestasi_kerja <=60  THEN 'Sedang'
    WHEN m.final_nilai_prestasi_kerja <=75  THEN  'Cukup'
    WHEN m.final_nilai_prestasi_kerja <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_final_nilai_prestasi_kerja
FROM (
SELECT l.*,
l.final_60_persen_nilai_capaian_skp + l.final_40_persen_nilai_perilaku_kerja final_nilai_prestasi_kerja 
FROM (
SELECT k.*,
CASE
    WHEN (k.orientasi_pelayanan <=50 or isnull(k.orientasi_pelayanan))  THEN 'Buruk'
    WHEN k.orientasi_pelayanan <=60  THEN 'Sedang'
    WHEN k.orientasi_pelayanan <=75  THEN  'Cukup'
    WHEN k.orientasi_pelayanan <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_orientasi_pelayanan,
CASE
    WHEN (k.integritas <=50 or isnull(k.integritas))  THEN 'Buruk'
    WHEN k.integritas <=60  THEN 'Sedang'
    WHEN k.integritas <=75  THEN  'Cukup'
    WHEN k.integritas <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_integritas,
CASE
    WHEN (k.komitmen <=50 or isnull(k.komitmen))  THEN 'Buruk'
    WHEN k.komitmen <=60  THEN 'Sedang'
    WHEN k.komitmen <=75  THEN  'Cukup'
    WHEN k.komitmen <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_komitmen,
CASE
    WHEN (k.disiplin <=50 or isnull(k.disiplin))  THEN 'Buruk'
    WHEN k.disiplin <=60  THEN 'Sedang'
    WHEN k.disiplin <=75  THEN  'Cukup'
    WHEN k.disiplin <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_disiplin,
CASE
    WHEN (k.kerjasama <=50 or isnull(k.kerjasama))  THEN 'Buruk'
    WHEN k.kerjasama <=60  THEN 'Sedang'
    WHEN k.kerjasama <=75  THEN  'Cukup'
    WHEN k.kerjasama <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_kerjasama,
CASE
    WHEN (k.kepemimpinan <=50 or isnull(k.kepemimpinan))  THEN 'Buruk'
    WHEN k.kepemimpinan <=60  THEN 'Sedang'
    WHEN k.kepemimpinan <=75  THEN  'Cukup'
    WHEN k.kepemimpinan <=90  THEN  'Baik'
    ELSE 'Sangat Baik'
END
label_kepemimpinan,
CASE
	WHEN k.kepemimpinan != 0 THEN ROUND(k.jumlah_perilaku/6,2)
    ELSE ROUND(k.jumlah_perilaku/5,2)
END
rata_rata_nilai_perilaku,
CASE
	WHEN k.kepemimpinan != 0 THEN ROUND(((k.jumlah_perilaku/6)*40)/100,2)
    ELSE 
    ROUND(((k.jumlah_perilaku/5)*40)/100,2)
END
final_40_persen_nilai_perilaku_kerja
FROM 
(SELECT i.*,
ROUND(SUM(j.orientasi_pelayanan)/count(j.id_opmt_perilaku),2) orientasi_pelayanan,
ROUND(SUM(j.integritas)/count(j.id_opmt_perilaku),2) integritas,
ROUND(SUM(j.komitmen)/count(j.id_opmt_perilaku),2) komitmen,
ROUND(SUM(j.disiplin)/count(j.disiplin),2) disiplin,
ROUND(SUM(j.kerjasama)/count(j.kerjasama),2) kerjasama,
ROUND(SUM(j.kepemimpinan)/count(j.kepemimpinan),2) kepemimpinan,
ROUND(SUM(j.orientasi_pelayanan)/count(j.id_opmt_perilaku)+SUM(j.integritas)/count(j.id_opmt_perilaku)+
SUM(j.komitmen)/count(j.id_opmt_perilaku)+SUM(j.disiplin)/count(j.disiplin)+SUM(j.kerjasama)/count(j.kerjasama)+
SUM(j.kepemimpinan)/count(j.kepemimpinan),2) jumlah_perilaku
FROM 
(SELECT h.*,
ROUND(SUM(h.nilai_capaian_skp)/COUNT(h.id_dd_user),2) sasaran_kerja_pegawai,
ROUND((SUM(h.nilai_capaian_skp)/COUNT(h.id_dd_user) * 60) / 100,2)  final_60_persen_nilai_capaian_skp
FROM 
(SELECT g.* , g.jumlah_nilai_skp + g.nilai_tugas_tambahan nilai_capaian_skp
FROM (
SELECT e.* , COUNT(f.id_opmt_tugas_tambahan) jumlah_tugas_tambahan,
CASE
 WHEN (COUNT(f.id_opmt_tugas_tambahan) = 0) THEN 0
 WHEN (COUNT(f.id_opmt_tugas_tambahan) < 4) THEN 1
 WHEN (COUNT(f.id_opmt_tugas_tambahan) < 7) THEN 2
 ELSE 3
END
nilai_tugas_tambahan
FROM 
(SELECT d.*,SUM(d.nilai)/COUNT(d.nilai) jumlah_nilai_skp
FROM
(SELECT c.*, 
ROUND(c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu,2) perhitungan,
CASE
	WHEN c.target_biaya > 0 THEN ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/4,3)
    ELSE ROUND((c.nilai_kuantitas + c.nilai_kualitas + c.nilai_waktu )/3,3)
END
nilai
FROM (SELECT b.*,
CASE
    WHEN b.persen_waktu <=24 THEN ((1.76 * b.target_Waktu - b.realisasi_waktu ) / b.target_waktu) * 100
    ELSE 76 - ((((1.76 * b.target_waktu - b.realisasi_waktu) / b.target_waktu ) * 100 ) - 100 )
END
nilai_waktu,
CASE
	WHEN b.persen_biaya <= 24 THEN  ((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya) * 100
    ELSE 76 - ((((1.76 * b.target_biaya - b.realisasi_biaya) / b.target_biaya ) * 100 ) - 100 )
END
nilai_biaya
FROM ( SELECT a.*,
(realisasi_kuantitas/target_kuantitas) * 100 nilai_kuantitas,
(realisasi_kualitas/100) * 100 nilai_kualitas,
100 - ( (realisasi_waktu/target_waktu) * 100) persen_waktu,
100 - ( (realisasi_biaya/target_biaya) * 100) persen_biaya
FROM 
(SELECT a.id_opmt_target_skp, a.id_opmt_detail_kegiatan_jabatan, f.angka_kredit, a.kegiatan_tahunan,a.target_kuantitas,
b.satuan_kuantitas,a.target_waktu,a.biaya target_biaya,
CASE 
	WHEN ISNULL(sum(c.kuantitas)) THEN 0
    ELSE sum(c.kuantitas)
END
realisasi_kuantitas,
CASE 
	WHEN ISNULL(d.kualitas) THEN 0
    ELSE d.kualitas
END
realisasi_kualitas,d.waktu realisasi_waktu,d.biaya realisasi_biaya, a.id_dd_user, 
e.id_opmt_tahunan_skp, e.awal_periode_skp, e.akhir_periode_skp
FROM ekinerja.opmt_target_skp a
LEFT JOIN ekinerja.dd_kuantitas b on a.satuan_kuantitas = b.id_dd_kuantitas
LEFT JOIN ekinerja.opmt_realisasi_harian_skp c ON a.id_opmt_target_skp = c.id_opmt_target_skp AND c.proses=0
LEFT JOIN ekinerja.opmt_realisasi_skp d ON a.id_opmt_target_skp = d.id_opmt_target_skp
INNER JOIN ekinerja.opmt_tahunan_skp e ON e.id_opmt_tahunan_skp = a.id_opmt_tahunan_skp
LEFT JOIN ekinerja.opmt_detail_kegiatan_jabatan f ON a.id_opmt_detail_kegiatan_jabatan = f.id_opmt_detail_kegiatan_jabatan
GROUP BY a.id_opmt_target_skp
 ) a) b ) c ) d
 GROUP BY d.id_opmt_tahunan_skp,d.id_dd_user ) e
LEFT JOIN ekinerja.opmt_tugas_tambahan f   ON  f.id_dd_user = e.id_dd_user
WHERE  f.id_dd_user=e.id_dd_user AND DATE(f.tanggal) BETWEEN DATE(e.awal_periode_skp) AND DATE(e.akhir_periode_skp)
GROUP BY e.id_opmt_tahunan_skp,e.id_dd_user ) g
) h 
GROUP BY h.id_dd_user
) i
LEFT JOIN ekinerja.opmt_perilaku j ON (j.id_dd_user = i.id_dd_user AND j.tahun=YEAR(i.akhir_periode_skp))
GROUP BY i.id_dd_user ) k ) l
WHERE l.id_dd_user='$id_user' ) m";	    
		$query	=$this->db->query($sql);
		return $query;
	}	
	
	 function _get_instansi($id)
	{
		$sql	="SELECT nama_instansi FROM instansi WHERE id_instansi='$id' ";
		$query 		= $this->db->query($sql);
		return $query;
	}	
	
}
