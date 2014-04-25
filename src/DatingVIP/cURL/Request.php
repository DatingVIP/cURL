<?php
/**
 * cURL based HTTP Request
 *
 * Simple but effective OOP wrapper around Curl php lib.
 *
 * @package    DatingVIP
 * @subpackage cURL
 * @copyright  &copy; 2014 firstbeatmedia.com
 * @author     Joe Watkins <joe@firstbeatmedia.com>
 * @version    2.0.4 - PSR-2 CS
 *
 * @example
 * try {
 *    $response = new DatingVIP\cURL\Request ()
 *    ->setUserAgent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)")
 *    ->setCookieStorage("/tmp/cookies.txt")
 *    ->post("http://www.foo.com/login.php", ["login" => "pera", "pass" => "secret"]);
 * } catch (\RuntimeException $ex) {
 *     echo (string) $ex;
 * } finally {
 *    echo (string) $response;
 * }
 */

namespace DatingVIP\cURL;

class Request
{
    /**
     * Constructor
     *
     * @param   array $options [=[defaults]]
     *
     * @access  public
     */
    public function __construct($options = [])
    {
        foreach ($options as $option => $value) {
            $this->options[$option] = $value;
        }
    }

    /**
     * Set username/pass for basic http auth
     *
     * @param string username
     * @param string password
     *
     * @access public
     * @return $this
     */
    public function setCredentials($username, $password)
    {
        return $this->setOption(
            CURLOPT_USERPWD,
            "{$username}:{$password}"
        );
    }

    /**
     * Set referer string
     *
     * @param string referer
     *
     * @access public
     * @return $this
     */
    public function setReferer($referer)
    {
        return $this->setOption(
            CURLOPT_REFERER,
            $referer
        );
    }

    /**
     * Set useragent string
     *
     * @param string agent
     *
     * @access public
     * @return $this
     */
    public function setUseragent($agent)
    {
        return $this->setOption(
            CURLOPT_USERAGENT,
            $agent
        );
    }

    /**
     * Set to receive output headers in all output functions
     *
     * @param boolean
     *
     * @access public
     * @return $this
     */
    public function setHeadersUsed($used)
    {
        return $this->setOption(CURLOPT_HEADER, $used);
    }

    /**
     * Set to receive body
     *
     * @param boolean
     *
     * @access public
     * @return $this
     */
    public function setBodyUsed($used)
    {
        return $this->setOption(CURLOPT_RETURNTRANSFER, $used);
    }

    /**
     * Set proxy to use for each curl request
     *
     * @param string proxy
     *
     * @access public
     * @return $this
     */
    public function setProxy($proxy)
    {
        return $this->setOption(CURLOPT_PROXY, $proxy);
    }

    /**
     * Set file location where cookie data will be stored and send on each new request
     *
     * @param string absolute path to file (must be in writable dir)
     *
     * @access public
     * @return $this
     */
    public function setCookieStorage($file)
    {
        $this->setOption(CURLOPT_COOKIEJAR, $file);
        $this->setOption(CURLOPT_COOKIEFILE, $file);

        return $this;
    }

    /**
     * Set connection timeout to use for each request
     *
     * @param int timeout
     *
     * @access public
     * @return $this
     */
    public function setTimeout($timeout)
    {
        return $this->setOption(CURLOPT_TIMEOUT, $timeout);
    }

    /**
     * Set interface to use for each request
     *
     * @param string interface
     *
     * @access public
     * @return $this
     */
    public function setInterface($interface)
    {
        return $this->setOption(CURLOPT_INTERFACE, $interface);
    }

    /**
     * Set option to value
     *
     * @param int   option
     * @param mixed value
     *
     * @access public
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Get current value of option
     *
     * @param int option
     *
     * @access public
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->options[$option];
    }

    /**
     * Get current options
     *
     * @access public
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get current headers collection
     *
     * @access public
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        foreach ($this->headers as $header => $value) {
            if (is_array($value)) {
                $headers[$header] = implode("; ", $value);
            } else {
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Adds a header to the headers collection
     *
     * @param string name
     * @param string value
     *
     * @access public
     * @return $this
     */
    public function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = [$this->headers[$name], $value];
            } else {
                $this->headers[$name][] = $value;
            }
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Sets a header in the headers collection
     *
     * @param string name
     * @param string value
     *
     * @access public
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Sets headers collection
     *
     * @param string name
     * @param array  headers
     *
     * @access public
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Prepare files for merge with post fields
     *
     * @author Dejan Marjanovic <dm@php.net>
     *
     * @param array files
     *
     * @access protected
     * @return array
     */
    protected function prepareFiles(array $files = [])
    {
        foreach ($files as $name => $location) {
            $location = realpath($location);
            if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
                $files[$name] = new \CurlFile($name, null, $location);
            } else {
                $files[$name] = sprintf("@%s", $location);
            }
        }

        return $files;
    }

    /**
     * Send post data to target URL
     *
     * @param string url
     * @param mixed  post
     * @param mixed  files
     *
     * @access public
     * @return Response
     * @throws \RuntimeException
     */
    public function post($url, $post = [], $files = [])
    {
        if (!is_array($post)) {
            $post = [];
        }

        $files = $this->prepareFiles($files);
        $post += $files;

        $this->options[CURLOPT_URL] = $url;

        $this->options[CURLOPT_POST] = true;
        $this->options[CURLOPT_POSTFIELDS] = $post;

        return new Response($this);
    }

    /**
     * Get data from target URL
     *
     * @param string url
     *
     * @access public
     * @return Response
     * @throws \RuntimeException
     */
    public function get($url)
    {
        $this->options[CURLOPT_URL] = $url;
        $this->options[CURLOPT_HTTPGET] = true;

        return new Response($this);
    }

    /**
     * Fetch data from target URL
     * and store it directly to file
     *
     * @param string   url
     * @param resource value stream resource(ie. fopen) or location
     * @param string   mode valid file stream mode
     *
     * @access public
     * @return Response
     * @throws \RuntimeException
     */
    public function downloadTo($url, $fp, $mode = "w+")
    {
        $this->options[CURLOPT_URL] = $url;
        $this->options[CURLOPT_HTTPGET] = true;

        if (!is_resource($fp)) {
            $this->options[CURLOPT_FILE] = fopen($fp, $mode);

            if (!$this->options[CURLOPT_FILE]) {
                throw new \RuntimeException(
                    "failed to open the file {$fp} for writing");
            }
        } else {
            $this->options[CURLOPT_FILE] = $fp;
        }

        $response = new Response($this);

        if (!is_resource($fp)) {
            fclose($this->options[CURLOPT_FILE]);
        }

        return $response;
    }

    /**
     * Make an upload request to $url
     *
     * return data returned from url or false if error occured
     * (contribution by vule nikolic, vule@dinke.net)
     *
     * @param string url
     * @param array  post array ie. $foo['post_var_name'] = $value
     * @param array  files array ie. $foo['file.mp3'] = '/path/to/file.mp3'
     *
     * @access     public
     * @return Response
     * @throws \RuntimeException
     * @deprecated Deprecated in Release 2.0.3
     */
    public function uploadTo($url, $post, $files)
    {
        return $this->post($url, $post, $files);
    }

    /**
     * Default sensible options for most transfers
     *
     * @access protected
     * @var array
     */
    protected $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR    => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING       => "gzip deflate",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 5
    ];

    /**
     * Headers array
     *
     * @access protected
     * @var array
     */
    protected $headers = [];
}
