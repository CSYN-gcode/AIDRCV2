<?php

namespace App\Imports;

use App\Model\Device;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVPackingMatrixUpdaterImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Device([
            'name' => $row[1],
            'huawei_pn' => $row[2],
            'lot_no_machine_code' => $row[3],
            'mrp_no' => $row[4],
            'boxing' => $row[5],
            'ship_boxing' => $row[6],
            'status' => $row[7],
            'product_type' => $row[8],
            'barcode' => $row[9],
            'created_by' => $row[10],
            'last_updated_by' => $row[11],
            'created_at' => $row[12],
            'updated_at' => $row[13],
        ]);
    }
}