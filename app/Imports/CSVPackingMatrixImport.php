<?php

namespace App\Imports;

use App\Model\Device;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVPackingMatrixImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Device([
            // 'name' => $row[1],
            // 'mrp_no' => $row[2],
            // 'boxing' => $row[3],
            // 'barcode' => $row[0],
            // 'ship_boxing' => $row[4],
            
            'name' => $row[1],
            'lot_no_machine_code' => $row[3],
            'mrp_no' => $row[4],
            'boxing' => $row[5],
            'ship_boxing' => $row[6],
            'product_type' => $row[8],
            'barcode' => $row[9],
            'ship_boxing' => $row[6],
        ]);
    }
}
