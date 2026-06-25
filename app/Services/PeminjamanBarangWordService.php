<?php

namespace App\Services;

use App\Models\PeminjamanBarang;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class PeminjamanBarangWordService
{
    private string $templatePath;

    private string $sourceTemplatePath;

    private string $outputDir;

    public function __construct()
    {
        $this->sourceTemplatePath = storage_path('app/temp/Form_barang.docx');
        $this->templatePath = storage_path('app/temp/Form_barang_prepared.docx');
        $this->outputDir = storage_path('app/peminjaman_barang');
    }

    public function generate(PeminjamanBarang $peminjaman): string
    {
        $this->ensureOutputDirectory();
        $this->ensureTemplateReady();

        $template = new TemplateProcessor($this->templatePath);

        $tanggalKegiatan = Carbon::parse($peminjaman->tanggal_kegiatan)->locale('id');
        $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->locale('id');

        $template->setValue('nomor_surat', $peminjaman->nomor_surat ?? '-');
        $template->setValue('nama_peminjam', $peminjaman->nama_peminjam);
        $template->setValue('divisi', $peminjaman->divisi);
        $template->setValue('nomor_hp', $peminjaman->nomor_hp);
        $template->setValue('tanggal_kegiatan', $tanggalKegiatan->translatedFormat('l, d/m/Y'));
        $template->setValue('tanggal_kembali', $tanggalKembali->translatedFormat('d/m/Y'));
        $template->setValue('tempat', $peminjaman->tempat);
        $template->setValue('nama_kegiatan', $peminjaman->nama_kegiatan);

        $items = $peminjaman->items;
        $itemCount = max($items->count(), 1);

        $template->cloneRow('nama_barang', $itemCount);

        foreach ($items as $index => $item) {
            $row = $index + 1;
            $template->setValue("no#{$row}", (string) $row);
            $template->setValue("nama_barang#{$row}", $item->nama_barang);
            $template->setValue("jumlah#{$row}", (string) $item->jumlah);
        }

        $filename = sprintf(
            'Form_Peminjaman_Barang_%s_%s.docx',
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
            throw new \RuntimeException('Template Form_barang.docx tidak ditemukan di storage/app/temp/.');
        }

        if (file_exists($this->templatePath) && $this->templateHasPlaceholders($this->templatePath)) {
            return;
        }

        $this->injectPlaceholdersIntoTemplate();
    }

    private function templateHasPlaceholders(?string $path = null): bool
    {
        $path = $path ?? $this->templatePath;
        $xml = $this->readDocumentXml($path);

        return str_contains($xml, '${nama_peminjam}') && str_contains($xml, '${nama_barang}');
    }

    private function injectPlaceholdersIntoTemplate(): void
    {
        if (! copy($this->sourceTemplatePath, $this->templatePath)) {
            throw new \RuntimeException('Gagal menyiapkan salinan template Word.');
        }

        $zip = new ZipArchive();
        if ($zip->open($this->templatePath) !== true) {
            throw new \RuntimeException('Gagal membuka template Word untuk disiapkan.');
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException('Struktur template Word tidak valid.');
        }

        $replacements = [
            '<w:t>…./</w:t></w:r><w:proofErr w:type="gramEnd"/><w:r><w:t>AST-BMIKP/SPB/</w:t></w:r><w:proofErr w:type="gramStart"/><w:r><w:t>…./</w:t></w:r><w:proofErr w:type="gramEnd"/><w:r><w:t>20…</w:t></w:r>' =>
                '<w:t>${nomor_surat}</w:t></w:r>',
            '<w:tab/><w:t>: ………………………….</w:t>' => '<w:tab/><w:t>: ${nama_peminjam}</w:t>',
            '<w:tab/><w:t>: …………………...</w:t>' => '<w:tab/><w:t>: ${divisi}</w:t>',
            '<w:tab/><w:t>: ……. …….. ……..</w:t>' => '<w:tab/><w:t>: ${nomor_hp}</w:t>',
            '<w:tab/><w:t xml:space="preserve">: …………, </w:t></w:r><w:proofErr w:type="gramStart"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>…..</w:t></w:r><w:proofErr w:type="gramEnd"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>/ ……………/ 20….</w:t>' =>
                '<w:tab/><w:t xml:space="preserve">: ${tanggal_kegiatan}</w:t>',
            '<w:tab/><w:t xml:space="preserve">: </w:t></w:r><w:proofErr w:type="gramStart"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>…..</w:t></w:r><w:proofErr w:type="gramEnd"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>/ ……………/ 20</w:t></w:r><w:proofErr w:type="gramStart"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>….</w:t></w:r><w:r><w:rPr><w:color w:val="FF0000"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:vertAlign w:val="superscript"/></w:rPr><w:t>*</w:t></w:r><w:proofErr w:type="gramEnd"/><w:r><w:rPr><w:color w:val="FF0000"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:vertAlign w:val="superscript"/></w:rPr><w:t>*</w:t>' =>
                '<w:tab/><w:t xml:space="preserve">: ${tanggal_kembali}</w:t></w:r><w:r><w:rPr><w:color w:val="FF0000"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:vertAlign w:val="superscript"/></w:rPr><w:t>*</w:t></w:r><w:r><w:rPr><w:color w:val="FF0000"/><w:sz w:val="24"/><w:szCs w:val="24"/><w:vertAlign w:val="superscript"/></w:rPr><w:t>*</w:t>',
            '<w:tab/><w:t>: …………………………………….</w:t>' => '<w:tab/><w:t>: ${tempat}</w:t>',
            '<w:tab/><w:t>: …………………………………</w:t></w:r><w:proofErr w:type="gramStart"/><w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>…..</w:t></w:r><w:proofErr w:type="gramEnd"/>' =>
                '<w:tab/><w:t>: ${nama_kegiatan}</w:t></w:r>',
        ];

        foreach ($replacements as $search => $replace) {
            if (str_contains($xml, $search)) {
                $xml = str_replace($search, $replace, $xml);
            }
        }

        $xml = $this->prepareBarangTableRow($xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    private function prepareBarangTableRow(string $xml): string
    {
        if (! preg_match('/<w:tbl>.*?<w:tr[^>]*w14:paraId="1A3AF090".*?<\/w:tr>(?<rows>.*?)<\/w:tbl>/s', $xml, $tableMatch)) {
            return $xml;
        }

        if (! preg_match('/<w:tr[^>]*w14:paraId="5B7D7941"[^>]*>.*?<\/w:tr>/s', $tableMatch['rows'], $rowMatch)) {
            return $xml;
        }

        $placeholderRow = $rowMatch[0];
        $placeholderRow = preg_replace(
            '/(<w:tc>.*?<w:pPr>.*?<\/w:pPr>)<\/w:p><\/w:tc>/s',
            '$1<w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>${no}</w:t></w:r></w:p></w:tc>',
            $placeholderRow,
            1
        );
        $placeholderRow = preg_replace(
            '/(<w:tc>.*?<w:pPr>.*?<\/w:pPr>)<\/w:p><\/w:tc>/s',
            '$1<w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>${nama_barang}</w:t></w:r></w:p></w:tc>',
            $placeholderRow,
            1
        );
        $placeholderRow = preg_replace(
            '/(<w:tc>.*?<w:pPr>.*?<\/w:pPr>)<\/w:p><\/w:tc>/s',
            '$1<w:r><w:rPr><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t>${jumlah}</w:t></w:r></w:p></w:tc>',
            $placeholderRow,
            1
        );

        $rowsSection = $tableMatch['rows'];
        $rowsSection = preg_replace('/<w:tr[^>]*w14:paraId="5B7D7941"[^>]*>.*?<\/w:tr>/s', $placeholderRow, $rowsSection, 1);
        $rowsSection = preg_replace('/<w:tr[^>]*w14:paraId="(?!5B7D7941)[^"]+"[^>]*>.*?<\/w:tr>/s', '', $rowsSection);

        return str_replace($tableMatch['rows'], $rowsSection, $xml);
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
