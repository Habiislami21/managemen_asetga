<?php

namespace App\Services;

use App\Models\PeminjamanKendaraan;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class PeminjamanKendaraanWordService
{
    private string $templatePath;

    private string $sourceTemplatePath;

    private string $outputDir;

    public function __construct()
    {
        $this->sourceTemplatePath = storage_path('app/temp/Form_kendaraan.docx');
        $this->templatePath       = storage_path('app/temp/Form_kendaraan_prepared.docx');
        $this->outputDir          = storage_path('app/peminjaman_kendaraan');
    }

    public function generate(PeminjamanKendaraan $peminjaman): string
    {
        $this->ensureOutputDirectory();
        $this->ensureTemplateReady();

        $template = new TemplateProcessor($this->templatePath);

        $tanggalPemakaian = Carbon::parse($peminjaman->tanggal_pemakaian)->locale('id');
        $tanggalKembali   = Carbon::parse($peminjaman->tanggal_kembali)->locale('id');

        $template->setValue('nomor_surat',       $peminjaman->nomor_surat ?? '-');
        $template->setValue('nama_peminjam',      $peminjaman->nama_peminjam);
        $template->setValue('divisi',             $peminjaman->divisi);
        $template->setValue('jabatan',            $peminjaman->jabatan);
        $template->setValue('nomor_hp',           $peminjaman->nomor_hp);
        $template->setValue('jenis_kendaraan',    $peminjaman->jenis_kendaraan);
        $template->setValue('nama_kendaraan',     $peminjaman->nama_kendaraan);
        $template->setValue('nomor_plat',         $peminjaman->nomor_plat);
        $template->setValue('tanggal_pemakaian',  $tanggalPemakaian->translatedFormat('l, d/m/Y'));
        $template->setValue('tanggal_kembali',    $tanggalKembali->translatedFormat('d/m/Y'));
        $template->setValue('peruntukan',         $peminjaman->peruntukan);
        $template->setValue('lokasi_tujuan',      $peminjaman->lokasi_tujuan);
        $template->setValue('nama_kegiatan',      $peminjaman->nama_kegiatan);

        $filename = sprintf(
            'Form_Peminjaman_Kendaraan_%s_%s.docx',
            $peminjaman->id,
            now()->format('YmdHis')
        );

        $outputPath = $this->outputDir . DIRECTORY_SEPARATOR . $filename;
        $template->saveAs($outputPath);

        return $outputPath;
    }

    public function ensureTemplateReady(): void
    {
        if (! file_exists($this->sourceTemplatePath)) {
            throw new \RuntimeException('Template Form_kendaraan.docx tidak ditemukan di storage/app/temp/.');
        }

        if (file_exists($this->templatePath) && $this->templateHasPlaceholders($this->templatePath)) {
            return;
        }

        $this->injectPlaceholdersIntoTemplate();
    }

    private function templateHasPlaceholders(?string $path = null): bool
    {
        $path = $path ?? $this->templatePath;
        $xml  = $this->readDocumentXml($path);

        return str_contains($xml, '${nama_peminjam}') && str_contains($xml, '${jenis_kendaraan}');
    }

    private function injectPlaceholdersIntoTemplate(): void
    {
        if (! copy($this->sourceTemplatePath, $this->templatePath)) {
            throw new \RuntimeException('Gagal menyiapkan salinan template Word kendaraan.');
        }

        $zip = new ZipArchive();
        if ($zip->open($this->templatePath) !== true) {
            throw new \RuntimeException('Gagal membuka template Word kendaraan untuk disiapkan.');
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException('Struktur template Word kendaraan tidak valid.');
        }

        // ── Nomor surat ──
        $xml = str_replace(
            '<w:t>Nomor: …./AST-BMIKP/SPK/…./20…</w:t>',
            '<w:t>${nomor_surat}</w:t>',
            $xml
        );

        // ── Nama Peminjam ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="78205B15">',
            '<w:t xml:space="preserve"> : ${nama_peminjam}</w:t></w:r></w:p><w:p w14:paraId="78205B15">',
            $xml
        );

        // ── Divisi Amanah ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="0C532CD1">',
            '<w:t xml:space="preserve"> : ${divisi}</w:t></w:r></w:p><w:p w14:paraId="0C532CD1">',
            $xml
        );

        // ── Amanah Jabatan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="6DEB0EE3">',
            '<w:t xml:space="preserve"> : ${jabatan}</w:t></w:r></w:p><w:p w14:paraId="6DEB0EE3">',
            $xml
        );

        // ── Nomor HP ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : ……….- ……….. -……….</w:t>',
            '<w:t xml:space="preserve"> : ${nomor_hp}</w:t>',
            $xml
        );

        // ── Jenis Kendaraan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="3D314DF2">',
            '<w:t xml:space="preserve"> : ${jenis_kendaraan}</w:t></w:r></w:p><w:p w14:paraId="3D314DF2">',
            $xml
        );

        // ── Nama Kendaraan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="626FDEED">',
            '<w:t xml:space="preserve"> : ${nama_kendaraan}</w:t></w:r></w:p><w:p w14:paraId="626FDEED">',
            $xml
        );

        // ── No. Plat ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………………………….</w:t></w:r></w:p><w:p w14:paraId="78C806DB">',
            '<w:t xml:space="preserve"> : ${nomor_plat}</w:t></w:r></w:p><w:p w14:paraId="78C806DB">',
            $xml
        );

        // ── Hari, Tgl Pemakaian ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …………, …../ ……/ 20…. s/d …………, …../………/ 20….</w:t>',
            '<w:t xml:space="preserve"> : ${tanggal_pemakaian}</w:t>',
            $xml
        );

        // ── Tanggal Pengembalian ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : …../ ……/ 20….</w:t>',
            '<w:t xml:space="preserve"> : ${tanggal_kembali}</w:t>',
            $xml
        );

        // ── Peruntukan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : Pribadi/ Keperluan Khid</w:t></w:r><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>mat (divisi non-BMI)</w:t>',
            '<w:t xml:space="preserve"> : ${peruntukan}</w:t></w:r><w:bookmarkStart w:id="0" w:name="_GoBack"/><w:bookmarkEnd w:id="0"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t></w:t>',
            $xml
        );

        // ── Lokasi Tujuan Perjalanan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : ……………………………………….</w:t></w:r></w:p><w:p w14:paraId="396C5BD2">',
            '<w:t xml:space="preserve"> : ${lokasi_tujuan}</w:t></w:r></w:p><w:p w14:paraId="396C5BD2">',
            $xml
        );

        // ── Nama Kegiatan ──
        $xml = str_replace(
            '<w:t xml:space="preserve"> : ……………………………………..', // trailing space preserved
            '<w:t xml:space="preserve"> : ${nama_kegiatan}',
            $xml
        );

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    private function readDocumentXml(string $path): string
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        return $xml;
    }

    private function ensureOutputDirectory(): void
    {
        if (! is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
}
