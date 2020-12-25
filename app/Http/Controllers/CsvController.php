<?php
namespace App\Http\Controllers;


use App\Models\BuildInsertQueryModel;
use App\Http\Services\UsageTranslatorService;
use Illuminate\Http\Request;

class CsvController extends Controller
{
    function upload(Request $request,
                    UsageTranslatorService $service,
                    BuildInsertQueryModel $model):string {
        $csvReport = $request->file("sample_report");

        if($queries = $service->constructInsertQuery($csvReport, $model)) {
           return json_encode($queries);
        }
        return '';
    }
}