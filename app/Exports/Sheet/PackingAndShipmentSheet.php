<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
Use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PackingAndShipmentSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }

    protected $records;
    protected $device_name;

    function __construct($records, $device_name)
    {
        $this->records = $records;
        $this->device_name = $device_name;
    }

    public function view(): View {
        if($this->export_type == 1){
            return view('exports.packingandshipment', ['shipping_records' => $this->records, 'device_name' => $this->device_name]);
        }
	}

	public function title(): string
    {
        return 'Packing and Shipment Report';
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

    // For drawing only
    // public function drawings()
    // {
    //     $drawing = new Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('This is my logo');
    //     $drawing->setPath(public_path('storage/design/pricon_logo3.png'));
    //     $drawing->setHeight(52);
    //     $drawing->setCoordinates('A1');

    //     // $drawing2 = new Drawing();
    //     // $drawing2->setName('Other image');
    //     // $drawing2->setDescription('This is a second image');
    //     // $drawing2->setPath(public_path('storage/design/pricon_logo2.png'));
    //     // $drawing2->setHeight(52);
    //     // $drawing2->setCoordinates('G1');

    //     // return [$drawing, $drawing2];

    //     return [$drawing];
    // }
}
