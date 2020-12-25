<?php
/**
 * Created by PhpStorm.
 * User: robra
 * Date: 2020-12-21
 * Time: 3:08 PM
 */
namespace App\Models;


use Illuminate\Support\Facades\Log;

class BuildInsertQueryModel {

    private const SKIP_PARTNER_IDS = [26392];
    private static $reductionRule = ['EA000001GB0O'=> 1000,
        'PMQ00005GB0R'=> 5000,
        'SSX006NR'=>1000,
        'SPQ00001MB0R'=> 2000];

    private $json = [];

    /**
     * @var array
     */
    private $chargeableData;
    /**
     * @var array
     */
    private $domainsData;

    /** Build data to be inserted in the query
     * @param $records
     * @param $json
     */
    public function BuildInsertData($records, $json) {
        $this->json = $json;

        //parse aggay of records
        foreach ($records->getRecords() as $record) {
            //check for errors
            if(empty($record['PartNumber'])) {
                Log::error('PartNumber is empty for PartnerID:'. $record['PartnerID']);
                continue;
            }
            if($record['itemCount'] < 0) {
                Log::error('itemCount is non positive for PartnerID:'. $record['PartnerID']);
                continue;
            }
            If(!in_array($record['PartnerID'] ,self::SKIP_PARTNER_IDS)) {
                continue;
            }


            //create insert data array for
            $this->chargeableData[] = [
                'partnerID' => intval($record['PartnerID']),
                'product'=>$this->getProduct($record['PartNumber']),
                'partnerPurchasedPlanID' => $this->getParnerPurchasedPlanId($record['partnerGuid']),
                'plan'=> $record['plan'],
                'usage' => intval($this->getUsage($record['PartNumber'], $record['itemCount']))
            ];

            $this->domainsData[$record['domains']] = ['partnerPurchasedPlanID' => $record['accountGuid'],
                'domain' => $record['domains'],
                ];
        }
    }

    /** Builds insert query for tables chargeable and domains
     * @return array
     */
    public function buildInsertQuery() {
        $chargeable = app(ChargeableDataModel::class)->buildInsertQuery($this->chargeableData);
        $domains = app(DomainsDataModel::class)->buildInsertQuery($this->domainsData);

        return ['chargeable' => $chargeable,
            'domains'=> $domains];
    }

    /**
     * @param $partNumber
     * @return string
     */
    private function  getProduct($partNumber) {
        return isset($this->json[$partNumber]) ?
            $this->json[$partNumber]:
            '';
    }

    /**
     * @param $accountGuid
     * @return null|string|string[]
     */
     private function getParnerPurchasedPlanId($accountGuid) {

        return ($accountGuid)?
            preg_replace('/[^\da-z]/i', '', $accountGuid):
            '';
     }

     private function getUsage($partNumber, $itemCount) {

         return (isset(self::$reductionRule[$partNumber])) ?
            self::$reductionRule[$partNumber]/$itemCount:
            $itemCount;
     }
}