<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVShipmentConfirmationImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'po_no' => $row[0],
            'shipment_date' => $row[1],
            'delivery_place_name' => $row[2],
            'shipment_qty' => $row[3],
        ]);
    }
}
