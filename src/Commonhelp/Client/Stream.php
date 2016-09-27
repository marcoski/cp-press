<?php

namespace Commonhelp\Client;

use Commonhelp\Client\Exception;

/**
 * Stream context HTTP client.
 *
 * @author  Frederic Guillot
 */
class Stream extends Client{
    /**
     * Prepare HTTP headers.
     *
     * @return string[]
     */
    private function prepareHeaders(){
        $headers = array(
            'Connection: close',
            'User-Agent: '.$this->userAgent,
        );

        // disable compression in passthrough mode. It could result in double
        // compressed content which isn't decodeable by browsers
        if (function_exists('gzdecode') && !$this->isPassthroughEnabled()) {
            $headers[] = 'Accept-Encoding: gzip';
        }

        if ($this->etag) {
            $headers[] = 'If-None-Match: '.$this->etag;
        }

        if ($this->lastModified) {
            $headers[] = 'If-Modified-Since: '.$this->lastModified;
        }

        if ($this->proxyUsername) {
            $headers[] = 'Proxy-Authorization: Basic '.base64_encode($this->proxyUsername.':'.$this->proxyPassword);
        }

        if ($this->username && $this->password) {
            $headers[] = 'Authorization: Basic '.base64_encode($this->username.':'.$this->password);
        }

        $headers = array_merge($headers, $this->requestHeaders);

        return $headers;
    }

    /**
     * Construct the final URL from location headers.
     *
     * @param array $headers List of HTTP response header
     */
    private function setEffectiveUrl($headers){
        foreach ($headers as $header) {
            if (stripos($header, 'Location') === 0) {
                list(, $value) = explode(': ', $header);

                $this->url = Url::resolve($value, $this->url);
            }
        }
    }

    /**
     * Prepare stream context.
     *
     * @return array
     */
    private function prepareContext(){
        $context = array(
            'http' => array(
                'method' => 'GET',
                'protocol_version' => 1.1,
                'timeout' => $this->timeout,
                'max_redirects' => $this->maxRedirects,
            ),
        );

        if ($this->proxyHostname) {
            $context['http']['proxy'] = 'tcp://'.$this->proxyHostname.':'.$this->proxyPort;
            $context['http']['request_fulluri'] = true;
        }

        $context['http']['header'] = implode("\r\n", $this->prepareHeaders());

        return $context;
    }

    /**
     * Do the HTTP request.
     *
     * @return array HTTP response ['body' => ..., 'status' => ..., 'headers' => ...]
     */
    public function doRequest(){
        $body = '';

        // Create context
        $context = stream_context_create($this->prepareContext());

        // Make HTTP request
        $stream = @fopen($this->url, 'r', false, $context);
        if (!is_resource($stream)) {
            throw new InvalidUrlException('Unable to establish a connection');
        }

        // Get HTTP headers response
        $metadata = stream_get_meta_data($stream);
        list($status, $headers) = HttpHeaders::parse($metadata['wrapper_data']);

        if ($this->isPassthroughEnabled()) {
            header(':', true, $status);

            if (isset($headers['Content-Type'])) {
                header('Content-Type: '.$headers['Content-Type']);
            }

            fpassthru($stream);
        } else {
            // Get the entire body until the max size
            $body = stream_get_contents($stream, $this->maxBodySize + 1);

            // If the body size is too large abort everything
            if (strlen($body) > $this->maxBodySize) {
                throw new MaxSizeException('Content size too large');
            }

            if ($metadata['timed_out']) {
                throw new TimeoutException('Operation timeout');
            }
        }

        fclose($stream);

        $this->setEffectiveUrl($metadata['wrapper_data']);

        return array(
            'status' => $status,
            'body' => $this->decodeBody($body, $headers),
            'headers' => $headers,
        );
    }

    /**
     * Decode body response according to the HTTP headers.
     *
     * @param string      $body    Raw body
     * @param HttpHeaders $headers HTTP headers
     *
     * @return string
     */
    public function decodeBody($body, HttpHeaders $headers){
        if (isset($headers['Transfer-Encoding']) && $headers['Transfer-Encoding'] === 'chunked') {
            $body = $this->decodeChunked($body);
        }

        if (isset($headers['Content-Encoding']) && $headers['Content-Encoding'] === 'gzip') {
            $body = gzdecode($body);
        }

        return $body;
    }

    /**
     * Decode a chunked body.
     *
     * @param string $str Raw body
     *
     * @return string Decoded body
     */
    public function decodeChunked($str){
        for ($result = ''; !empty($str); $str = trim($str)) {

            // Get the chunk length
            $pos = strpos($str, "\r\n");
            $len = hexdec(substr($str, 0, $pos));

            // Append the chunk to the result
            $result .= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
        }

        return $result;
    }
}