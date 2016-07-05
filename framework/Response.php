<?php
/**
* @desc 响应类。看Laravel,Symfony,Yii2之类，实现一大堆SPL，实际开发中用得多少？
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月5日    上午2:15:04
*/

namespace framework;

class Response
{
    private static $statusCodes = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
    
    /**
     * 常见Content-Type
     * @var unknown
     */
    private static $contentTypes = [
        'html' => 'text/html',
        'xml' => 'text/xml',
        'text' => 'text/plain',
        'json' => 'application/json',
        'script' => 'application/javascript',
    ];
    
    private $headers = [];
    
    private $content;
    
    private $statusCode;//只需要状态码如200即可发送Status Code。http://stackoverflow.com/questions/3258634/php-how-to-send-http-response-code
    
    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($statusCode);
        $this->headers = $headers;
    }
    
    /**
     * 设置Status Code
     * @param unknown $statusCode
     * @throws \InvalidArgumentException
     */
    public function setStatusCode($statusCode)
    {
        if (!isset(self::$statusCodes[$statusCode])) {
            throw new \InvalidArgumentException("Invalid statusCode: $statusCode");
        }
        $this->statusCode = $statusCode;
    }
    
    public function setContent($content)
    {
        if (is_array($content)) {
            $this->content = json_encode($content);
            $this->setContent('json');
        } elseif (is_string($content)) {
            $this->content = $content;
            $this->setContentType('html');
        } else {
            throw new \InvalidArgumentException("Invalid content: " . gettype($content));
        }
    }
    
    public function appendContent($content, $prepend = false)
    {
        $content = (string)$content;
        if ($prepend) {
            $this->content = $content . $this->content;
        } else {
            $this->content .= $content;
        }
    }
    
    public function setHeader($key, $value)
    {
       if (!is_string($key) || !is_string($value)) {
           throw new \InvalidArgumentException("Invalid key or value, both must be string.");
       }
       $this->headers[$key] = $value;
       return true; 
    }
    
    public function setContentType($contentType)
    {
        if (!isset(self::$contentTypes[$contentType])) {
            throw new \InvalidArgumentException("Invalid contentType: $contentType.");
        }
        $this->headers['Content-Type'] = self::$contentTypes[$contentType];
        return true;
    }
    
    public function send()
    {
        if (headers_sent()) {
            //已经发送过了
            return false;
        }
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value", false);
        }
        
        echo $this->content;
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
       
        //symfony里边还看ob_get_status(true)有几层，再清理。这里都到脚本结尾了，有意义？
        
        die;
    }
    
    
}