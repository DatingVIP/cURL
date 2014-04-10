<?php
/**
 * cURL based HTTP Response
 *
 * @package DatingVIP
 * @subpackage cURL
 * @version 2.0
 * @copyright &copy; 2014 firstbeatmedia.com
 * @author Joe Watkins <joe@firstbeatmedia.com>
 * @version 2.0 - revise API
 */
namespace DatingVIP\cURL;

class Response 
{
/**
 * Construct response object from Request
 *
 * @param   Request   $request
 * @access	public
 */
	public function __construct(Request $request) {
		$options = $request->getOptions();
		
		if (!isset($options[CURLOPT_URL]))
			throw new \RuntimeException("the URL for the request is not set");

		$handle = curl_init();
		
		if (!$handle)
			throw new \RuntimeException("failed to initialized cURL");

		foreach ($options as $option => $value) {
			if ($option) {
				if (!curl_setopt($handle, $option, $value))
					throw new \RuntimeException(
						"failed to set option {$option} => {$value}");
			}
		}
		
		$this->data = curl_exec($handle);
		$this->error = [
			"errno" => curl_errno($handle),
			"error" => curl_error($handle)
		];
		
		if ($this->error["errno"])
			throw new \RuntimeException(
				"failed to execute request for {$options["CURLOPT_URL"]}", 
				$this->error["errno"]);
		
		$this->info = curl_getinfo($handle);
		curl_close($handle);
	}

/**
 * Get HTTP response code
 *
 * @access public
 * @return int
 */
	public function getResponseCode()   { return $this->getInfo("http_code"); }
	
/**
 * Get URL requested
 *
 * @access public
 * @return string
 */
	public function getURL()            { return $this->getInfo("url");  }

/**
 * Get content type returned
 *
 * @access public
 * @return string
 */
	public function getContentType()    { return $this->getInfo("content-type"); }
	
/**
 * Get total time
 *
 * @access public
 * @return float
 */
	public function getTime()           { return $this->getInfo("total_time"); }

/**
 * Get total time
 *
 * @param int
 * @access public
 * @return float
 */
	public function getInfo($info = 0)  { return $info ? $this->info[$info] : $this->info; }

/**
 * Get cURL error code
 *
 * @access public
 * @return int
 */
	public function getErrno()          { return $this->error["errno"]; }

/**
 * Get cURL error string
 *
 * @access public
 * @return string
 */
	public function getError()          { return $this->error["errstr"]; }

/**
 * Get response string
 *
 * @access public
 * @return string
 */
	public function getData()           { return $this->data; }

/**
 * Magic response string
 *
 * @access public
 * @return string
 */
	public function __toString()        { return (string) $this->data; }

/**
 * Response string
 *
 * @access protected
 * @var array
 */
	protected $data;

/**
 * Error information
 *
 * @access protected
 * @var array
 */
	protected $error;

/**
 * Transfer information
 *
 * @access protected
 * @var array
 */
	protected $info;
}
?>
