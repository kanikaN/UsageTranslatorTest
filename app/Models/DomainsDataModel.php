<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * Class model for table Domains
 * Keeping the table models separate for future customisations
 *
 * Class DomainsDataModel
 * @package App\Models
 */
class DomainsDataModel {

    protected $table = 'domains';

    private $query = '';

    /**
     * @param array $bindings
     * @return array
     */
    public function buildInsertQuery(array $bindings) {

        //split data in batch sizes
        $bindingsArr = array_chunk($bindings, env('INSERT_BATCH_SIZE'));
        foreach($bindingsArr as $data) {
            //make a prepared statement using query builder's pretend db
            //check if query needs to be executed
            if (env('EXECUTE_QUERY')) {
                DB::table($this->table)->insertOrIgnore($data);
                $query = DB::getQueryLog();
            } else
                $query = DB::pretend(function() use($data) {
                    //insert data, ignore entry in case of any exception or mismatch
                    DB::table($this->table)->insertOrIgnore($data);
                });

            $return[] = (env('RETURN_PREPARED_QUERY'))
                ? $query[0]
                : $this->getRawQuery($query);
        }
        return isset($return) ? $return : [];
    }

    /**
     * Converts Builder prepared query to raw query
     * @param $query
     * @return string
     */
    private function getRawQuery($query) {
        $queryArr = explode('?',$query[0]['query']);
        $raw = '';
        foreach($queryArr as $idx => $ele){
            if($idx < count($queryArr) - 1){
                $raw .= $ele."'".$query[0]['bindings'][$idx]."'";
            }
        }

        return $raw.$queryArr[count($queryArr) -1];

    }
}
