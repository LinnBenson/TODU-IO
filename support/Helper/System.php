<?php

use Todu\Bootstrap;
use Todu\Exception\LogException;

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
     * 查询 ENV 环境变量
     * - [string]:环境变量键, [mixed]|null:默认值
     * return [mixed]:返回环境变量值
     */
    if ( !function_exists( 'env' ) ) {
        function env( string $key, $default = null ) {
            if ( !isset( $_ENV ) || !is_array( $_ENV ) ) { return $default; }
            $value = isset( $_ENV[$key] ) && $_ENV[$key] !== '' ? $_ENV[$key] : $default;
            if ( strtolower( $value ) === 'true' ) { return true; }
            if ( strtolower( $value ) === 'false' ) { return false; }
            if ( strtolower( $value ) === 'null' ) { return null; }
            if ( is_numeric( $value ) ) { return $value + 0; }
            return $value;
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
            $value = Bootstrap::cache( 'thread', "config:{$keys[0]}", function()use( $keys ) {
                $result = [];
                $file0 = ToduPath()."system/config/{$keys[0]}.php";
                if ( file_exists( $file0 ) ) { $result = array_merge( $result, require $file0 ); }
                $file1 = "config/{$keys[0]}.php";
                if ( file_exists( $file1 ) ) { $result = array_merge( $result, require $file1 ); }
                // 插件介入
                if ( Bootstrap::$init && !in_array( $keys[0], [ 'app', 'todu', 'trust' ] ) ) {
                    $addons = Bootstrap::permission( 'CONFIGURATION_INFORMATION_QUERY', 'ALL', $keys[0] );
                    if ( is_array( $addons ) && !empty( $addons ) ) { $result = array_merge( $result, $addons ); }
                }
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
    /**
     * 读取语言包
     * - [string]:键名, [array]|[]:替换内容, [string]|null:语言标识
     * return [string]:语言内容
     */
    if ( !function_exists( '__' ) ) {
        function __( string $key, array $replace = [], $locale = null ) {
            // 检查是否为双重语言调用
            $check = explode( ':', $key );
            if ( count( $check ) === 2 && empty( $replace ) ) { return __( $check[0], [], $locale ).__( $check[1], [], $locale ); }
            // 整理参数
            $keys = explode( '.', $key );
            if ( empty( $locale ) ) { $locale = config( 'app.lang' ); }
            // 加载使用的语言包
            $text = Bootstrap::cache( 'thread', "lang:{$locale}|{$keys[0]}", function()use( $locale, $keys ) {
                $result = [];
                $folder1 = ToduPath()."system/resource/lang/{$locale}/{$keys[0]}.php";
                $folder2 = "resource/lang/{$locale}/{$keys[0]}.php";
                if ( !file_exists( $folder1 ) && !file_exists( $folder2 ) ) {
                    $locale = config( 'app.lang' );
                    $folder1 = ToduPath()."system/resource/lang/{$locale}/{$keys[0]}.php";
                    $folder2 = "resource/lang/{$locale}/{$keys[0]}.php";
                }
                if ( file_exists( $folder1 ) ) { $result = array_merge( $result, require $folder1 ); }
                if ( file_exists( $folder2 ) ) { $result = array_merge( $result, require $folder2 ); }
                // 插件介入
                $addons = Bootstrap::permission( 'LANGUAGE_PACK_LOADING', 'ALL', $locale,  $keys[0] );
                if ( is_array( $addons ) && !empty( $addons ) ) { $result = array_merge( $result, $addons ); }
                return is_array( $result ) ? $result : [];
            });
            // 查询语言包
            array_shift( $keys ); foreach ( $keys as $k ) {
                if ( !isset( $text[$k] ) ) { return $key; }
                $text = $text[$k];
            }
            if ( !is_string( $text ) ) { return $key; }
            if ( empty( $replace ) ) { return $text; }
            // 替换占位符
            foreach ( $replace as $k => $v ) { $text = str_replace( "{{".$k."}}", $v, $text ); }
            return $text;
        }
    }
    /**
     * 获取插件实例
     * - [string]:插件名称
     * return [\Todu\Slot\Plugin]|null:返回插件实例
     */
    if ( !function_exists( 'plugin' ) ) {
        function plugin( string $name ) {
            return Bootstrap::cache( 'thread', "plugin:{$name}", function()use( $name ) {
                $pluginPath = config( 'todu.path.plugin' )."{$name}/";
                // 检查入口文件是否存在
                $index = "{$pluginPath}index.php";
                if ( !file_exists( $index ) ) { return null; }
                $plugin = require $index;
                // 检查插件实例
                if (
                    !is_object( $plugin ) ||
                    !is_subclass_of( $plugin, \Todu\Slot\Plugin::class )
                ) {
                    throw new LogException([ 'Plugin', "The build data returned by plugin [{$name}] contains an error." ]);
                    return null;
                }
                // 检查兼容性
                if ( is_array( $plugin->compatible ) && count( $plugin->compatible ) === 2 ) {
                    $low = $plugin->compatible[0];
                    $high = $plugin->compatible[1];
                    if (
                        $low !== '*' && version_compare( config( 'todu.version' ), $low, '<' ) ||
                        $high !== '*' && version_compare( config( 'todu.version' ), $high, '>' )
                    ) {
                        throw new LogException([ 'Plugin', "Plugin [{$name}] does not support your current version of TODU.IO." ]);
                        return null;
                    }
                }
                // 检查依赖
                if ( is_array( $plugin->rely ) && !empty( $plugin->rely ) ) {
                    foreach ( $plugin->rely as $relyName ) {
                        $relyPlugin = plugin( $relyName );
                        if ( is_null( $relyPlugin ) ) {
                            throw new LogException([ 'Plugin', "The plugin [{$name}] depends on the plugin [{$relyName}] which is not installed." ]);
                            return null;
                        }
                    }
                }
                // 初始化插件
                $plugin->id = $name;
                $plugin->name = is_null( $plugin->name ) ? $name : $plugin->name;
                $plugin->path = $pluginPath;
                if ( isPublic( $plugin, 'init' ) ) {
                    try {
                        $plugin->init();
                    }catch ( \Throwable $e ) {
                        throw new LogException([ 'Plugin', "[{$name}] init error: ".$e->getMessage() ]);
                        return null;
                    }
                }
                // 返回插件实例
                return $plugin;
            });
        }
    }