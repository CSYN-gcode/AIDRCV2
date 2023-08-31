<?php

namespace App\Imports;

use App\Model\Device;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVPackingMatrixUpdaterImport2 implements ToModel
{
    /**
    * @param array $col
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $col)
    {
        return new Device([
            'id' => $col[0],
            'name' => $col[1],
            'barcode' => $col[2],
            'huawei_pn' => $col[3],
            'lot_no_machine_code' => $col[4],
            'mrp_no' => $col[5],
            'boxing' => $col[6],
            'ship_boxing' => $col[7],
            'product_type' => $col[8],
            'c3_label' => $col[9],
        ]);
    }
}