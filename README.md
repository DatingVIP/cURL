cURL Bundle
===========
*Because cURL isn't simple enough ... apparently ...*

Contained is a simple as you like ```Request``` and ```Response``` object for HTTP requests using the ```cURL``` API.

You can haz codez !
===================
*How to get shit done ...*

Making a simple GET request:

```php
require_once("src/DatingVIP/cURL/Request.php");
require_once("src/DatingVIP/cURL/Response.php");

use DatingVIP\cURL\Request;
use DatingVIP\cURL\Response;

try {
	$response = (new Request())
		->setHeadersUsed(true)
		->get("http://www.example.com");
} catch (\RuntimeException $ex) {
	echo (string) $ex;
} finally {
	printf("Got %d bytes from %s in %.3f seconds\n",
		strlen((string)$response),
		$response->getURL(),
		$response->getTime());
}
```

Making a POST request:

```php
require_once("src/DatingVIP/cURL/Request.php");
require_once("src/DatingVIP/cURL/Response.php");

use DatingVIP\cURL\Request;
use DatingVIP\cURL\Response;

try {
	$response = (new Request())
		->setHeadersUsed(true)
		->post("http://www.example.com", ["hello" => "world"]);
} catch (\RuntimeException $ex) {
	echo (string) $ex;
} finally {
	printf("Posted %d bytes from %s in %.3f seconds\n",
		strlen((string)$response),
		$response->getURL(),
		$response->getTime());
}
```

If you like to be super verbose about everything for no good reason:

```php
require_once("src/DatingVIP/cURL/Request.php");
require_once("src/DatingVIP/cURL/Response.php");

use DatingVIP\cURL\Request;
use DatingVIP\cURL\Response;

try {
	$request = new Request([
		CURLOPT_HTTPHEADER => [
			"x-my-header" => "x-my-value"
		],
		CURLOPT_URL => "http://www.example.com"
	]);

	$response = new Response($request);
} catch (\RuntimeException $ex) {
	echo (string) $ex;
} finally {
	if ($response instanceof Response) {
		printf("Got %d bytes from %s in %.3f seconds\n",
			strlen((string)$response),
			$response->getURL(),
			$response->getTime());
	}
}
```
