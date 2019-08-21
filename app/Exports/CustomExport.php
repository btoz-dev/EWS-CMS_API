<?php

namespace App\Exports;

use App\Trans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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

    private $arr;

    public function setArr(array $arr)
    {
    	$this->arr = $arr;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query =  Trans::custom($this->job, $this->arr);

        return $query;
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function map($customExport): array
    {
        if ($this->job == 'BLOK') {
            # code...
            return [
                $customExport->codeBlok,
                $customExport->totalPokok,
                $customExport->pokokDone,
                $customExport->pokokNDone,
                $customExport->persentase
            ];
        }

        if ($this->job == 'DETIL') {
            # code...
            if ($this->arr[2] == '003') {
                # code...
                return [
                    $customExport->codeTanaman,
                    $customExport->rkhDate,
                    $customExport->aktifitas,
                    $customExport->mandor,
                    $customExport->realisationDate,
                    $customExport->NamaMandor,
                    $customExport->mandorNote,
                    $customExport->kawilDate,
                    $customExport->NamaKawil,
                    $customExport->kawilNote
                ];
            }
            else {
                return [
                    $customExport->codeTanaman,
                    $customExport->rkhDate,
                    $customExport->aktifitas,
                    $customExport->mandor,
                    $customExport->realisationDate,
                    $customExport->NamaSPI,
                    $customExport->spiNote,
                    $customExport->kawilDate,
                    $customExport->NamaKawil,
                    $customExport->kawilNote
                ];
            }
        }
    }
}
