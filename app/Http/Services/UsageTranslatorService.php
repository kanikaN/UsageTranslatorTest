<?php
namespace App\Http\Services;

use App\Models\BuildInsertQueryModel;
use Illuminate\Http\UploadedFile;
use League\Csv\Reader;
use League\Csv\Statement;

class UsageTranslatorService {

    /**
     * @param UploadedFile $csvFile
     * @param BuildInsertQueryModel $model
     * @return array
     * @throws \League\Csv\Exception
     */
    public function constructInsertQuery(UploadedFile $csvFile,  $model) {

        //get reader Object
        $records = $this->getFileRecords($csvFile);
        $json = $this->getJsonFileRecords();

        //build the table data needed to create insert query
        $model->BuildInsertData($records, $json);
        return $model->buildInsertQuery();
    }

    /**
     * @param UploadedFile $csvFile
     * @return \League\Csv\TabularDataReader
     * @throws \League\Csv\Exception
     */
    public function getFileRecords(UploadedFile $csvFile) {
        //Store uploaded file for reading
        $csvFile->move(env('UPLOAD_FILE_DIR'), $csvFile->getClientOriginalName());
        $csv = Reader::createFromPath(
            env('UPLOAD_FILE_DIR').$csvFile->getClientOriginalName());

        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);

        return $records;
    }

    /**
     * @return mixed
     */
    private function getJsonFileRecords(){
        $jsonString = file_get_contents(env('UPLOAD_FILE_DIR').'typemap.json');

        return json_decode($jsonString, true);
    }
}