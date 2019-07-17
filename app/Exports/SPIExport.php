<?php

namespace App\Exports;

use App\Trans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class SPIExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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
        $query =  Trans::spi($this->job);

        switch ($this->job) {
            case 'MANDOR':
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('created_at', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('created_at', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                }
                break;

            case 'SENSUS':
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('created_atSPI', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                    $query->orWhereBetween('created_atKawil', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('created_atSPI', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                    $query->orWhereBetween('created_atKawil', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                }
                break;
            
            default:
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('date', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('date', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                }
                break;
        }

        return $query->get();
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function map($SPIExport): array
    {
        if ($this->job == 'MANDOR') {
            # code...
            return [
                $SPIExport->id,
                $SPIExport->rkhCode,
                $SPIExport->namaSPI,
                $SPIExport->namaMandor,
                $SPIExport->namaTK,
                $SPIExport->aktifitas,
                $SPIExport->codeBlok,
                $SPIExport->codeTanaman,
                $SPIExport->spiNote,
                $SPIExport->totalHand,
                $SPIExport->totalFinger,
                $SPIExport->totalLeaf,
                $SPIExport->ribbonColor,
                $SPIExport->skimmingSize,
                $SPIExport->created_at,
            ];
        }

        if ($this->job == 'SENSUS') {
            # code...
            return [
                $SPIExport->id,
                $SPIExport->codeTanaman,
                $SPIExport->week,
                $SPIExport->girth,
                $SPIExport->jumlahDaun,
                $SPIExport->corrActSPI,
                $SPIExport->dueDate,
                $SPIExport->created_atSPI,
                $SPIExport->namaSPI,
                $SPIExport->corrActKawil,
                $SPIExport->created_atKawil,
                $SPIExport->namaKawil,
            ];
        }
    }

}
