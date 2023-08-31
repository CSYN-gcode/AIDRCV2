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

class PartsLotSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $records;
    protected $device_name;
    protected $start_date;
    protected $end_date;

    function __construct($records, $device_name, $start_date, $end_date)
    {
        $this->records = $records;
        $this->device_name = $device_name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function view(): View {
        // if($this->export_type == 1){
            $partsLot_records = $this->records;
            $device_name = $this->device_name;
            $start_date = $this->start_date;
            $end_date = $this->end_date;

            return view('exports.partslot', ['partsLot_records' => $this->records, 'device_name' => $this->device_name, 'start_date' => $start_date, 'end_date' => $end_date]);
        // }
    }

    public function title(): string
    {
        return $this->device_name;
    }

    //for designs
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
            	// $event->sheet->setAllBorders('thin');
                // $event->sheet->getDelegate()->getStyle('C1')->getFont()->setSize(30);
                // $event->sheet->getDelegate()->getStyle('A2:X2')->getFont()->setSize(13);
            },
        ];
    }
}
