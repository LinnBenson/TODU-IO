<?php

use Todu\Bootstrap;

    /**
     * 导入文件
     * - [string]|[array]:文件路径
     * return [mixed]:返回引入内容
     */
    if ( !function_exists( 'import' ) ) {
        function import( $file ) {
            if ( is_string( $file ) ) { return require $file; }
            if ( is_array( $file ) ) {
                $result = [];
                for ( $i = 0; $i < 9999; $i++ ) {
                    if ( empty( $file[$i] ) ) { continue; }
                    $result[] = require $file[$i];
                }
                return $result;
            }
            return null;
        }
    }
    /**
     * 查询配置信息
     * - [string]:配置键, [mixed]|null:默认值
     * return [mixed]:返回配置信息
     */
    if ( !function_exists( 'config' ) ) {
        function config( string $key, $default = null ) {
            // 键拆分
            $keys = explode( '.', $key );
            // 获取配置
            $value = Bootstrap::cache( "config:{$keys[0]}", function()use( $keys ) {
                $result = [];
                $file = "config/{$keys[0]}.php";
                if ( file_exists( $file ) ) { $result = array_merge( $result, require $file ); }
                return $result;
            });
            // 获取配置值
            if ( count( $keys ) === 1 && !empty( $value ) ) { return $value; }
            if ( !is_array( $value ) || empty( $value ) ) { return $default; }
            array_shift( $keys ); foreach ( $keys as $k ) {
                if ( isset( $value[$k] ) ) {
                    $value = $value[$k];
                }else {
                    return $default;
                }
            }
            return $value;
        }
    }