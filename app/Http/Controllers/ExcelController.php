<?php

namespace App\Http\Controllers;

use XLSXWriter;

class ExcelController extends Controller
{
    private $excel;


    /**
     * @param $data
     * $data = array(
            array('year','month','amount'),
            array('2003','1','220'),
            array('2003','2','153.5'),
        );
     * @param $name
     */
    public function export($data,$name)
    {
        $filename = $name ?? 'hornor.xlsx';

        $writer = new XLSXWriter();
        $writer->writeSheet($data);
        $writer->writeToFile($filename);
    }
}
