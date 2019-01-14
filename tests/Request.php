<?php
namespace DatingVIP\cURL\Testing {

	use mimus\Double as double;

	class Request extends \PHPUnit\Framework\TestCase {

		public function tearDown() {
			double::unlink(\DatingVIP\cURL\Request::class);
		}

		public function testRequestConstructorNoArgs() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->rule("__construct")
				->expects()
				->executes()
				->validates(function() {
					return [
						\CURLOPT_RETURNTRANSFER => true,
						\CURLOPT_FAILONERROR    => true,
						\CURLOPT_FOLLOWLOCATION => true,
						\CURLOPT_ENCODING       => "gzip, deflate",
						\CURLOPT_SSL_VERIFYPEER => false,
						\CURLOPT_TIMEOUT        => 5
					] === $this->options;
				});
			$this->assertInstanceOf(\DatingVIP\cURL\Request::class, $builder->getInstance());
		}

		public function testRequestConstructorWithArgs() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->rule("__construct")
				->expects()
				->executes()
				->validates(function() {
					return [
						\CURLOPT_RETURNTRANSFER => false,
						\CURLOPT_FAILONERROR    => true,
						\CURLOPT_FOLLOWLOCATION => true,
						\CURLOPT_ENCODING       => "gzip, deflate",
						\CURLOPT_SSL_VERIFYPEER => false,
						\CURLOPT_TIMEOUT => 0,
						\CURLOPT_URL => "http://localhost",
					] === $this->options;
				});

			$this->assertInstanceOf(\DatingVIP\cURL\Request::class, $builder->getInstance([
				\CURLOPT_TIMEOUT => 0,
				\CURLOPT_RETURNTRANSFER => false,
				\CURLOPT_URL => "http://localhost"
			]));
		}

		public function testRequestSetCredentials() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setCredentials")
				->expects("krakjoe", "password")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_USERPWD] == "krakjoe:password";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setCredentials("krakjoe", "password"));
		}

		public function testSetReferer() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",
				"getOption"			
			]);

			$builder->rule("setReferer")
				->expects("http://localhost")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_REFERER] == "http://localhost";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setReferer("http://localhost"));
		}

		public function testSetUserAgent() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setUseragent")
				->expects("php")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_USERAGENT] == "php";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setUserAgent("php"));
		}

		public function testSetHeadersUsed() {
			$builder = double::class(\DatingVIP\cURL\Request::class);

			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setHeadersUsed")
				->expects(true)
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_HEADER] === true;
				});
			$builder->rule("setHeadersUsed")
				->expects(false)
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_HEADER] === false;
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setHeadersUsed(false));
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setHeadersUsed(true));
		}

		public function testSetBodyUsed() {
			$builder = double::class(\DatingVIP\cURL\Request::class);

			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setBodyUsed")
				->expects(true)
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_RETURNTRANSFER] === true;
				});
			$builder->rule("setBodyUsed")
				->expects(false)
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_RETURNTRANSFER] === false;
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setBodyUsed(false));
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setBodyUsed(true));
		}

		public function testSetProxy() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setProxy")
				->expects("http://localhost")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_PROXY] == "http://localhost";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setProxy("http://localhost"));
		}

		public function testSetCookieStorage() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setCookieStorage")
				->expects("/tmp/cookies")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_COOKIEJAR] == "/tmp/cookies" &&
					       $this->options[\CURLOPT_COOKIEFILE] == "/tmp/cookies";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setCookieStorage("/tmp/cookies"));
		}

		public function testSetTimeout() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setTimeout")
				->expects(10)
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_TIMEOUT] == 10;
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setTimeout(10));
		}

		public function testSetInterface() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"setOption",			
			]);

			$builder->rule("setInterface")
				->expects("eth0")
				->executes()
				->validates(function(){
					return $this->options[\CURLOPT_INTERFACE] == "eth0";
				});

			$object = $builder->getInstance();
			$this->assertInstanceOf(
				\DatingVIP\cURL\Request::class, 
				$object->setInterface("eth0"));
		}

		public function testGetOption() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"getOption",			
			]);

			$builder->rule("getOption")
				->expects(\CURLOPT_TIMEOUT)
				->executes()
				->returns(5);

			$object = $builder->getInstance();
			$this->assertSame(
				5, 
				$object->getOption(\CURLOPT_TIMEOUT));
		}

		public function testGetOptions() {
			$builder = double::class(\DatingVIP\cURL\Request::class);
			$builder->partialize([
				"getOptions",			
			]);

			$object = $builder->getInstance();
			$this->assertSame([
				\CURLOPT_RETURNTRANSFER => true,
				\CURLOPT_FAILONERROR    => true,
				\CURLOPT_FOLLOWLOCATION => true,
				\CURLOPT_ENCODING       => "gzip, deflate",
				\CURLOPT_SSL_VERIFYPEER => false,
				\CURLOPT_TIMEOUT        => 5
			],
			$object->getOptions());
		}
	}
}
