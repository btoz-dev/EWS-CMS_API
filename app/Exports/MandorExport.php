<?php

namespace App\Exports;

use App\Trans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class MandorExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{

    private $heading;

    public function setHeading(array $heading)
    {
        $this->heading = $heading;
    }

    private $job;

    public function setJob(string $job)
    {
    	$this->job = $job;
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
        $query =  Trans::mandor($this->job);

        if ($this->date_aw != NULL) {
            # code...
            $query->whereBetween('created_at', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function map($mandorExport): array
    {
        if ($this->job == 'PLANTCARE') {
            # code...
            return [
                $mandorExport->id,
                $mandorExport->rkhCode,
                $mandorExport->rkhDate,
                $mandorExport->mandor,
                $mandorExport->tk,
                $mandorExport->Description,
                $mandorExport->codeBlok,
                $mandorExport->codeTanaman,
                $mandorExport->mandorNote,
                $mandorExport->created_at,
            ];
        }

        if ($this->job == 'FRUITCARE') {
            # code...
            return [
                $mandorExport->id,
                $mandorExport->rkhCode,
                $mandorExport->rkhDate,
                $mandorExport->mandor,
                $mandorExport->tk,
                $mandorExport->Description,
                $mandorExport->codeBlok,
                $mandorExport->codeTanaman,
                $mandorExport->mandorNote,
                $mandorExport->totalHand,
                $mandorExport->totalFinger,
                $mandorExport->totalLeaf,
                $mandorExport->ribbonColor,
                $mandorExport->created_at,
            ];
        }

        if ($this->job == 'PANEN') {
            # code...
            return [
                $mandorExport->id,
                $mandorExport->rkhCode,
                $mandorExport->rkhDate,
                $mandorExport->mandor,
                $mandorExport->tk,
                $mandorExport->Description,
                $mandorExport->codeBlok,
                $mandorExport->codeTanaman,
                $mandorExport->skimmingSize,
                $mandorExport->mandorNote,
                $mandorExport->created_at,
            ];
        }
    }

}
