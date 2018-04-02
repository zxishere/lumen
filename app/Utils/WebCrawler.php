<?php

namespace App\Utils;

use Curl\Curl;
use Curl\MultiCurl;
use Sunra\PhpSimple\HtmlDomParser;

class WebCrawler
{
    public function __construct()
    {
        $this->user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36';
    }

    public function single($request_url, $cookie = '', $method = 'GET', $requestHeaders = [], $requestData = [])
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setUserAgent($this->user_agent);
        $curl->setTimeout(30);
        if ($cookie != '') {
            if (preg_match_all('/\s*([^;=]+)=([^;]+)/i', $cookie, $matches) > 0) {
                if (isset($matches[1]) && isset($matches[2])) {
                    if (count($matches[1]) == count($matches[2])) {
                        foreach ($matches[1] as $handle => $key) {
                            $curl->setCookie($key, $matches[2][$handle]);
                        }
                    }
                }
            }
        }
        $request_method = strtolower($method);
        if (is_array($requestHeaders) && !empty($requestHeaders)) {
            foreach ($requestHeaders as $key => $value) {
                $curl->setHeader($key, $value);
            }
        }
        if (is_array($requestData) && empty($requestData)) {
            $curl->$request_method($request_url);
        } else {
            $curl->$request_method($request_url, $requestData);
        }
        if ($curl->error) {
            die($curl->errorMessage.PHP_EOL);
        } else {
            $curl->close();
            $this->verifyResponse($request_url, $curl->responseHeaders, $curl->response);
            return $this->convert($curl->responseHeaders['Content-Type'], $curl->response);
        }
    }

    public function multi($urls, $success_callback, $cookie = '')
    {
        if (empty($urls)) {
            return true;
        }
        $multi_curl = new MultiCurl();
        if ($cookie != '') {
            if (preg_match_all('/\s*([^;=]+)=([^;]+)/i', $cookie, $matches) > 0) {
                if (isset($matches[1]) && isset($matches[2])) {
                    if (count($matches[1]) == count($matches[2])) {
                        foreach ($matches[1] as $handle => $key) {
                            $multi_curl->setCookie($key, $matches[2][$handle]);
                        }
                    }
                }
            }
        }
        $multi_curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setUserAgent($this->user_agent);
        $multi_curl->setTimeout(30);
        $multi_curl->setRetry(3);
        $multi_curl->setConcurrency(20);
        foreach ($urls as $key => $url) {
            if (isset($url['post'])) {
                $get = $multi_curl->addPost($url['url'], $url['post']);
            } else {
                $get = $multi_curl->addGet($url['url']);
            }
            $get->beforeSend(function ($instance) use ($url, $key) {
                $instance->setOpt(CURLOPT_PRIVATE, serialize(['id' => $key,'url' => $url['url'],'info' => $url['info']]));
            });
        }
        $multi_curl->success($success_callback);
        $multi_curl->error(function ($instance) {
            echo 'Call to "' . $instance->url . '" was unsuccessful.' . "\n";
            echo 'Error code: ' . $instance->errorCode . "\n";
            echo 'Error message: ' . $instance->errorMessage . "\n";
        });
        $multi_curl->start();
        $multi_curl->close();
    }

    public function verifyResponse($url, $headers, $response)
    {
        preg_match('#HTTP/\d+\.\d+ (\d+)#', $headers['Status-Line'], $matches);
        $http_code = $matches[1];
        $html = 'Curl Error : '.$url.' HTTP CODE : '.$http_code.''.PHP_EOL;
        if (isset($headers['Location'])) {
            $html .= '重定向 : '.$headers['Location'].PHP_EOL;
            echo $html;
        }
        if (empty($response)) {
            $html .= '抓取内容为空:'.$url.PHP_EOL;
            die($html);
        }
        if ($http_code != 200) {
            die($html);
        }
    }

    public function convert($content_type, $content)
    {
        if (str_contains($content_type, 'json')) {
            return $content;
        } else {
            return HtmlDomParser::str_get_html($content);
        }
    }
}
