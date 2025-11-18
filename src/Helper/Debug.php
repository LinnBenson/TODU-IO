<?php

    namespace Todu\Helper;

use Todu\Bootstrap;

    /**
     * 调试助手
     */
    class Debug {
        /**
         * 输出错误信息
         * - [string]:错误信息
         * return [void]:无返回值
         */
        public static function error( string $message ) {
            $message = is_object( $message ) && method_exists( $message, 'getMessage' ) ? $message->getMessage() : $message;
            return trigger_error( $message, E_USER_WARNING );
        }
        /**
         * 显示调试信息
         * - [mixed]:调试内容, [bool:true]:是否终止程序
         * return [null]:无返回值
         */
        public static function show( $value, bool $exit = true ) {
            echo "[DEBUG]\n";
            echo "---------------------\n";
            $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[0];
            if ( endsWith( $trace['file'], 'src/Helper/Common.php' ) ) {
                $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[1];
            }
            $type = gettype( $value );
            if ( is_json( $value ) ) {
                $type = 'json';
                $value = json_encode( json_decode( $value, true ), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES );
            }
            if ( is_uuid( $value ) ) { $type = 'UUID'; }
            echo "Line: {$trace['line']}\n";
            echo "File: {$trace['file']}\n";
            echo "Type: {$type}\n";
            echo "---------------------\n";
            print_r( $value );
            if ( $exit ) { exit(); }
            return null;
        }
        /**
         * 记录日志信息
         * - [string]:日志名称, [mixed]:日志内容
         * return [bool]:是否写入成功
         */
        public static function log( string $name, $info ) {
            // 参数解析
            if ( empty( $name ) || empty( $info ) ) { return false; }
            $title = null; $text = $info;
            if ( is_array( $info ) && count( $info ) === 2 && is_string( $info[0] ) ) {
                $title = $info[0]; $text = $info[1];
            }
            if ( is_object( $text ) && method_exists( $text, 'getFile' ) && method_exists( $text, 'getLine' ) && method_exists( $text, 'getMessage' ) ) {
                $text = "{$text->getFile()}[{$text->getLine()}]: {$text->getMessage()}";
            }
            if ( is_array( $text ) || is_object( $text ) ) { $text = json_encode( $text, JSON_UNESCAPED_UNICODE ); }
            if ( !is_string( $text ) ) { return false; }
            // 准备写入
            $write = !empty( $title ) ? "{$title} | {$text}" : $text;
            $write = toDate()." {$write}\n";
            $path = Bootstrap::$init ? config( 'todu.path.log' ) : 'storage/log/';
            $file = inFolder( $path.str_replace( '.', '/', $name ).'_'.date( 'Ymd' ).'.log' );
            if ( file_exists( $file ) && filesize( $file ) > 3 * 1024 * 1024 ) { rename( $file, "{$file}.".date( 'His' ).".bak" ); }
            return file_put_contents( $file, $write, FILE_APPEND ) ? true : false;
        }
    }