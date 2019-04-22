<?php

namespace App\Exports;

use App\Trans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class KawilExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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
        $query =  Trans::kawil($this->job);

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

    public function map($kawilExport): array
    {
        if ($this->job == 'PLANTCARE') {
            # code...
            return [
                $kawilExport->id,
                $kawilExport->kawil,
                $kawilExport->kawilNote,
                $kawilExport->created_at,
                $kawilExport->rkhCode,
                $kawilExport->Description,
                $kawilExport->mandor,
                $kawilExport->tk,
                $kawilExport->codeBlok,
                $kawilExport->codeTanaman,
                $kawilExport->mandorNote,
            ];
        }

        if ($this->job == 'FRUITCARE') {
            # code...
            return [
                $kawilExport->id,
                $kawilExport->kawil,
                $kawilExport->kawilNote,
                $kawilExport->created_at,
                $kawilExport->rkhCode,
                $kawilExport->Description,
                $kawilExport->mandor,
                $kawilExport->tk,
                $kawilExport->codeBlok,
                $kawilExport->codeTanaman,
                $kawilExport->mandorNote,
                $kawilExport->totalHand,
                $kawilExport->totalFinger,
                $kawilExport->totalLeaf,
                $kawilExport->ribbonColor,
            ];
        }
    }
}
