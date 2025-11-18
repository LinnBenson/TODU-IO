<?php

namespace Todu\Helper;

    class Web {
        private $link = null; // 请求链接
        private $headers = []; // 请求头
        private $errno = null; // 错误码
        private $cookie = null; // Cookie 数据
        private $timeout = 30; // 超时时间
        private $userAgent = 'TODU.IO Web Helper/1.0'; // 用户代理
        private $referer = null; // 来源页面
        private $method = 'GET'; // 请求方法
        private $data = null; // 请求数据
        private $options = []; // 可选参数
        public $result = null; // 响应内容
        public $status = null; // HTTP 状态码
        public $info = null; // 响应信息
        public $error = null; // 错误数据
        // 创建文件上传对象
        public static function file( string $path ) { return new \CURLFile( $path ); }
        // 构造函数
        public function __construct( string $link ) { $this->link = $link; }
        // 设置请求参数
        public function data( $data ) { $this->data = $data; return $this; }
        // 设置请求头
        public function header( array $headers ) { $this->headers = $headers; return $this; }
        // 设置请求 cookie
        public function cookie( string $cookie ) { $this->cookie = $cookie; return $this; }
        // 设置超时时间
        public function timeout( int $seconds ) { $this->timeout = $seconds; return $this; }
        // 设置用户代理
        public function userAgent( string $agent ) { $this->userAgent = $agent; return $this; }
        // 设置来源页面
        public function referer( string $referer ) { $this->referer = $referer; return $this; }
        // 自定义 curl 选项
        public function option( $key, $value ) { $this->options[$key] = $value; return $this; }
        // 发送请求
        public function request( $method = null ) {
            $this->method = !empty( $method ) ? strtoupper( $method ) : $this->method;
            // 处理 GET 参数
            if ( $this->method === 'GET' && $this->data ) {
                $this->link .= ( strpos( $this->link, '?' ) === false ? '?' : '&' ).http_build_query( $this->data );
                $this->data = null;
            }
            // 处理 body 数据
            if ( $this->method !== 'GET' && $this->data !== null ) {
                if ( is_array( $this->data ) ) {
                    $hasFile = false;
                    foreach ( $this->data as $v ) {
                        if ( $v instanceof \CURLFile ) { $hasFile = true; break; }
                    }
                    if ( !$hasFile ) {
                        $this->headers[] = 'Content-Type: application/x-www-form-urlencoded';
                        $this->data = http_build_query( $this->data );
                    }
                } else {
                    if ( is_object( $this->data ) || !is_string( $this->data ) ) {
                        $this->data = json_encode( $this->data, JSON_UNESCAPED_UNICODE );
                    }
                    $this->headers[] = 'Content-Type: application/json';
                }
            }
            // cURL 构建
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $this->link );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_TIMEOUT, $this->timeout );
            curl_setopt( $ch, CURLOPT_USERAGENT, $this->userAgent );
            if ( $this->referer ) { curl_setopt( $ch, CURLOPT_REFERER, $this->referer ); }
            if ( $this->headers ) { curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->headers ); }
            if ( $this->cookie ) { curl_setopt( $ch, CURLOPT_COOKIE, $this->cookie ); }
            if ( $this->method === 'POST' ) {
                curl_setopt( $ch, CURLOPT_POST, true );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->data );
            }else if ( in_array( $this->method, ['PUT','DELETE','PATCH'] ) ) {
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $this->method );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->data );
            }
            foreach ( $this->options as $k => $v ) {
                curl_setopt( $ch, $k, $v );
            }
            $this->result = curl_exec( $ch );
            $this->status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            $this->info = curl_getinfo( $ch );
            $this->errno = curl_errno( $ch );
            if ( $this->errno ) { $this->error = curl_error( $ch ); }
            curl_close( $ch );
            return $this;
        }
        // 获取响应数据并转换为数组
        public function toArray() { return is_json( $this->result ) ? json_decode( $this->result, true ) : null; }
    }