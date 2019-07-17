<?php

namespace App\Exports;

use App\Trans;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class PHExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
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
        $query =  Trans::ph($this->job);

        switch ($this->job) {
            case 'TB':
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('brutoDate', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                    $query->orWhereBetween('bonggolDate', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('brutoDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                    $query->orWhereBetween('bonggolDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                }
                break;

            case 'BT':
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('brutoDate', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('brutoDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
                }
                break;

            case 'BB':
                if ($this->date_aw != NULL) {
                    # code...
                    $query->whereBetween('bonggolDate', [$this->date_aw, $this->date_ak." 23:59:59.000"]);
                } else {
                    $query->whereBetween('bonggolDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
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

    public function map($phExport): array
    {
        if ($this->job == 'TB') {
            # code...
            return [
                $phExport->id,
                $phExport->codeTanaman,
                $phExport->brutoBerat,
                $phExport->bonggolBerat,
                $phExport->beratBersih,
                $phExport->brutoNote,
                $phExport->bonggolNote,
                $phExport->brutoDate,
                $phExport->bonggolDate,
                $phExport->userBruto,
                $phExport->userBonggol,
                $phExport->TKBruto,
                $phExport->TKBonggol
            ];
        }

        if ($this->job == 'BT') {
            # code...
            return [
                $phExport->id,
                $phExport->codeTanaman,
                $phExport->name,
                $phExport->namaPekerja,
                $phExport->brutoBerat,
                $phExport->brutoNote,
                $phExport->brutoDate,
            ];
        }

        if ($this->job == 'BB') {
            # code...
            return [
                $phExport->id,
                $phExport->codeTanaman,
                $phExport->name,
                $phExport->namaPekerja,
                $phExport->bonggolBerat,
                $phExport->bonggolNote,
                $phExport->bonggolDate,
            ];
        }

        if ($this->job == 'HT') {
            # code...
            return [
                $phExport->id,
                $phExport->codeTanaman,
                $phExport->name,
                $phExport->namaPekerja,
                $phExport->HandClass,
                $phExport->CalHandClass2,
                $phExport->CalHandClass4,
                $phExport->CalHandClass6,
                $phExport->CalHandClass8,
                $phExport->CalHandClass10,
                $phExport->CalHandClassAkhir,
                $phExport->FingerLen2,
                $phExport->FingerLen4,
                $phExport->FingerLen6,
                $phExport->FingerLen8,
                $phExport->FingerLen10,
                $phExport->FingerLenAkhir,
                $phExport->FingerHand2,
                $phExport->FingerHand4,
                $phExport->FingerHand6,
                $phExport->FingerHand8,
                $phExport->FingerHand10,
                $phExport->FingerHandAkhir,
                $phExport->Notes,
                $phExport->date
            ];
        }

        if ($this->job == 'CLT') {
            # code...
            return [
                $phExport->id,
                $phExport->codeBlok,
                $phExport->name,
                $phExport->desc,
                $phExport->berat,
                $phExport->note,
                $phExport->date
            ];
        }
    }

}
