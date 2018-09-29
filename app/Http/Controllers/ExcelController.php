<?php

namespace App\Http\Controllers;

use App\Models\Vote\VoteModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Excel;

class ExcelController extends Controller
{
    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function export()
    {



//        return $this->excel->download($title,$filename);
    }
}
