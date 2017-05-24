<?php 

namespace Vorbind\InfluxAnalytics\Client;

use \Exception;
use Vorbind\InfluxAnalytics\Exception\AnalyticsException;
use Vorbind\InfluxAnalytics\Exception\AnalyticsNormalizeException;


/**
 * Client Total
 */
class ClientTotal implements ClientInterface {

	use \Vorbind\InfluxAnalytics\AnalyticsTrait;

	protected $db;
	protected $service;
	protected $metrix;
	protected $granularity;

	public function __construct($db, $inputData) {
		$this->db = $db;
		$this->metrix = isset($inputData["metrix"]) ? $inputData["metrix"] : null;
		$this->tags = isset($inputData["tags"]) ? $inputData["tags"] : array();
	}
	
	/**
	 * Get toal
	 * @return int total
	 */
	public function getTotal() {
		$where = [];

		if ( null == $this->tags["service"] || null == $this->metrix ) {
			throw new AnalyticsException("Client total missing some of input params.");
		}

		try {
			// if you not set max time he takas current date as max time
			$where[] = "time <= '2099-01-01T00:00:00Z'"; 

			foreach($this->tags as $key => $val) {
				$where[] = "$key = '" . $val . "'";
			}
		
			$results = $this->db->getQueryBuilder()
					->from($this->metrix)
					->where($where)
					->sum('value')
					->getResultSet();

			$points = $results->getPoints();
		} catch (Exception $e) {
			throw new AnalyticsException("Analytics client total get total exception", 0, $e);
		}
		return isset($points[0]) && isset($points[0]["sum"]) ? $points[0]["sum"] : 0;
	}
}