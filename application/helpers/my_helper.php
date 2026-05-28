<?php
defined('BASEPATH') or exit('No direct script access allowed');

function title($title)
{
	$hasil = strtoupper($title);
	return $hasil;
}
function angka($angka)
{
	$hasil_rupiah = number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}
function rupiah($angka)
{
	$hasil_rupiah = '<b>' . 'Rp.' . number_format($angka, 2, ',', '.') . '</b>';
	return $hasil_rupiah;
}

function tanggal($tgl)
{
	$hasil_tanggal = date_format(date_create($tgl), "d/m/Y");
	return $hasil_tanggal;
}

function tanggal2($tgl)
{
	$hasil_tanggal = date_format(date_create($tgl), "d F Y");
	return $hasil_tanggal;
}

function tanggal3($tgl)
{
	$hasil_tanggal = date_format(date_create($tgl), "d-m-Y");
	return $hasil_tanggal;
}

function penyebut($nilai)
{
	$nilai = abs($nilai);
	$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " " . $huruf[$nilai];
	} else if ($nilai < 20) {
		$temp = penyebut($nilai - 10) . " belas";
	} else if ($nilai < 100) {
		$temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " seratus" . penyebut($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " seribu" . penyebut($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
	}
	return $temp;
}

function terbilang($nilai)
{
	if ($nilai < 0) {
		$hasil = "minus " . trim(penyebut($nilai)) . ' rupiah';
	} else {
		$hasil = trim(penyebut($nilai)) . ' rupiah';
	}
	return $hasil;
}
