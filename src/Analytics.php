<?php 

namespace Vorbind\InfluxAnalytics;

use InfluxDB\Database;
use InfluxDB\Point;


/**
*  InfluxAnalytics
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author sasa.rajkovic
*/
class Analytics implements AnalyticsInterface {

    /**
     * Save analytics
     * 
     * @param  InfluxDB\Database $db Mongo db
     * @param  string $serviceId Service id
     * @param  string $metrix    Metrix
     * @param  array $tags       Tags
     * @param  string $utcDt     Datetime
     */
    public function save($db, $serviceId, $metrix, $tags, $value = 1, $utcDt) {
      	//curl -i -XPOST 'http://localhost:8086/write?db=news' --data-binary 'sms,status=send,creator=scheduled service=1234-1234-1234-1234 value=1 1434055562000000000'
        
    	//$timeNs = isset($utcDt) ? $utcDt : exec('date +%s%N'); // Time precision has to be set to seconds!
        $timeNs = $utcDt;
        $points = array(
			new Point(
				$metrix,
				$value, // value
				$tags, // array('status' => 'send','type' => 'scheduled','campaign' => 'may')
				array('service' => $serviceId),
				$timeNs
			)
		);	    

		// we are writing unix timestamps, which have a second precision
		$result = $db->writePoints($points, Database::PRECISION_NANOSECONDS);
		//print_r($result);
      	return $result;
    }    
}