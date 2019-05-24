<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class RKMExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{

    private $heading;

    public function setHeading(array $heading)
    {
        $this->heading = $heading;
    }

    private $date_aw;

    public function setDateAw(string $date_aw = NULL)
    {
    	$this->date_aw = $date_aw;
    }

    private $date_ak;

    public function setDateAk(string $date_ak = NULL)
    {
    	$this->date_ak = $date_ak;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query =  DB::table('EWS_JADWAL_RKM')
            ->select('EWS_JADWAL_RKM.id', 'EWS_JADWAL_RKM.rkhCode', 'EWS_VW_DETAIL_MANDOR.namaPekerja', 'EWS_SUB_JOB.Description', 'EWS_JADWAL_RKM.codeBlok', 'EWS_JADWAL_RKM.barisStart', 'EWS_JADWAL_RKM.barisEnd', 'EWS_JADWAL_RKM.rkhDate')
            ->selectRaw('convert(varchar, EWS_JADWAL_RKM.rkhDate, 106) as tanggal')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) as totalPokok')
            ->selectRaw('dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokDone')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) - dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokNDone')
            ->selectRaw('dbo.EWS_f_realisasiPersen(dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd), dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok)) as persentase')
            ->join('EWS_VW_DETAIL_MANDOR', 'EWS_VW_DETAIL_MANDOR.codeMandor', '=', 'EWS_JADWAL_RKM.mandorCode')
            ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob');

        if ($this->date_aw != NULL) {
            # code...
            $query->whereBetween('EWS_JADWAL_RKM.rkhDate', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
        }else {
            $query->whereBetween('EWS_JADWAL_RKM.rkhDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function map($rkmExport): array
    {
        return [
            $rkmExport->id,
            $rkmExport->rkhCode,
            $rkmExport->tanggal,
            $rkmExport->namaPekerja,
            $rkmExport->Description,
            $rkmExport->codeBlok,
            $rkmExport->barisStart,
            $rkmExport->barisEnd,
            $rkmExport->totalPokok,
            $rkmExport->pokokDone,
            $rkmExport->pokokNDone,
            $rkmExport->persentase,
        ];
    }

}
