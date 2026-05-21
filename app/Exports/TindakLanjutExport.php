<?php

namespace App\Exports;

use App\Models\TindakLanjut;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TindakLanjutExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $isAdmin;
    protected $user;

    public function __construct($data, $isAdmin, $user)
    {
        $this->data = $data;
        $this->isAdmin = $isAdmin;
        $this->user = $user;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->isAdmin) {
            return [
                'No',
                'Periode Keputusan',
                'Unit Kerja',
                'Arahan/Strategi',
                'Bulan/Tahun Laporan',
                'Uraian Tindak Lanjut',
                'Kendala',
                'Keterangan',
                'Status',
                'Progress Stage',
                'Dibuat Oleh',
                'Tanggal Input',
                'Catatan Approver Terakhir',
            ];
        } elseif ($this->user->hasRole('Atasan Auditi')) {
            return [
                'No',
                'Unit Kerja',
                'Arahan/Strategi',
                'Bulan/Tahun Laporan',
                'Uraian Tindak Lanjut',
                'Kendala',
                'Keterangan',
                'Status',
                'Dibuat Oleh',
                'Tanggal Input',
            ];
        } else {
            return [
                'No',
                'Arahan/Strategi',
                'Bulan/Tahun Laporan',
                'Uraian Tindak Lanjut',
                'Kendala',
                'Keterangan',
                'Status',
                'Tanggal Input',
            ];
        }
    }

    public function map($tl): array
    {
        static $i = 0;
        $i++;

        $statusLabel = [
            'pending'     => 'Menunggu Approval',
            'in_approval' => 'Sedang DiApproval',
            'approved'    => 'Selesai',
            'rejected'    => 'Revisi / TD',
        ];

        $stageRoles = [
            1 => 'Tim Monitoring',
            2 => 'Atasan Auditi',
            3 => 'Pengendali Teknis',
            4 => 'Pengendali Mutu',
            5 => 'Penanggung Jawab',
        ];

        $lastApprovedStage = $tl->approvals
            ->where('status', 'approved')
            ->sortByDesc('stage')
            ->first();

        $progressStage  = $lastApprovedStage
            ? ($stageRoles[$lastApprovedStage->stage] ?? '-')
            : '-';

        $status          = $statusLabel[$tl->status] ?? $tl->status;
        $periodelaporan  = $tl->periode_bulan . '/' . $tl->periode_tahun;
        $strategi        = $tl->arahan->strategi ?? '-';
        $unitKerja       = $tl->unitKerja->name ?? '-';
        $periode         = $tl->arahan->keputusan->periode_year ?? '-';
        $createdBy       = $tl->creator->name ?? '-';
        $tanggalInput    = $tl->created_at->format('d/m/Y H:i');
        $lastNote        = $tl->approvals->whereNotNull('note')->sortByDesc('updated_at')->first();
        $catatanApprover = $lastNote->note ?? '-';

        if ($this->isAdmin) {
            return [
                $i,
                $periode,
                $unitKerja,
                $strategi,
                $periodelaporan,
                $tl->tindak_lanjut,
                $tl->kendala ?? '-',
                $tl->keterangan ?? '-',
                $status,
                $progressStage,
                $createdBy,
                $tanggalInput,
                $catatanApprover,
            ];
        } elseif ($this->user->hasRole('Atasan Auditi')) {
            return [
                $i,
                $unitKerja,
                $strategi,
                $periodelaporan,
                $tl->tindak_lanjut,
                $tl->kendala ?? '-',
                $tl->keterangan ?? '-',
                $status,
                $createdBy,
                $tanggalInput,
            ];
        } else {
            return [
                $i,
                $strategi,
                $periodelaporan,
                $tl->tindak_lanjut,
                $tl->kendala ?? '-',
                $tl->keterangan ?? '-',
                $status,
                $tanggalInput,
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}