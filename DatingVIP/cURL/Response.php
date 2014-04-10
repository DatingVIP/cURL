<?php
namespace DatingVIP\cURL;

class Response 
{
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
		
		$this->response = curl_exec($handle);
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
	
	public function getResponseCode()   { return $this->getInfo("http_code"); }
	public function getURL()            { return $this->getInfo("url");  }
	public function getContentType()    { return $this->getInfo("content-type"); }
	public function getTime()           { return $this->getInfo("total_time"); }
	public function getInfo($info = 0)  { return $info ? $this->info[$info] : $this->info; }

	public function getErrno()          { return $this->error["errno"]; }
	public function getError()          { return $this->error["errstr"]; }

	public function __toString()        { return (string) $this->response; }
	
	protected $request;
	protected $response;
	protected $error;
	protected $info;
}
?>
