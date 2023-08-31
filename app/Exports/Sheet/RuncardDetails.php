<?php

namespace App\Exports\Sheet;

use App\Model\ProductionRuncard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RuncardDetails implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $records;
    protected $runcard_no;

    function __construct($records, $runcard_no)
    {
        $this->records = $records;
        $this->runcard_no = $runcard_no;
    }

    public function view(): View {
        // if($this->export_type == 1){
            $records = $this->records;
            $runcard_no = $this->runcard_no;

            return view('exports.runcard_details', ['records' => $this->records, 'runcard_no' => $this->runcard_no]);
        // }
    }

    public function title(): string
    {
        return $this->runcard_no;
    }

    //for designs
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->getStyle('C1')->getFont()->setSize(30);
                // $event->sheet->getDelegate()->getStyle('A2:X2')->getFont()->setSize(13);
            },
        ];
    }
}
