<?php

namespace Todu;

use Dotenv\Dotenv;
use Todu\Helper\Debug;

    /**
     * 核心驱动器
     */
    class Bootstrap {
        // 驱动器初始化状态
        public static $init = false;
        // 组件缓存
        public static $cache = [
            'thread' => [],
            'autoload' => [],
            'permissions' => []
        ];
        /**
         * 初始化驱动器
         * - [callable|null]:初始化完成后的回调方法
         * return [mixed]:回调方法的返回值
         */
        public static function init( $method = null ) {
            // 引入系统级通用函数
            require_once ToduPath().'support/Helper/System.php';
            // 尝试导入 env 配置
            if ( file_exists( '.env' ) ) {
                try {
                    $dotenv = Dotenv::createImmutable( getcwd() );
                    $dotenv->load();
                }catch( \Throwable $e ) {
                    Debug::error( ".env import error: ".$e->getMessage() );
                }
            }
            // 检查应用是否启用
            if ( !config( 'app.enable' ) ) { exit( "Application is disabled.\n" ); }
            // 调试模式
            if ( config( 'app.debug' ) ) {
                error_reporting( E_ALL ); ini_set( 'display_errors', 1 );
            }else {
                error_reporting( 0 ); ini_set( 'display_errors', 0 );
            }
            // 设定时区
            $timezone = config( 'app.timezone', 'Asia/Singapore' );
            if ( !@date_default_timezone_set( $timezone ) ) {
                Debug::error( "Error in setting timezone: {$timezone}" );
            }
            // 引入自动加载
            spl_autoload_register(function( $class ) {
                if ( isset( self::$cache['autoload'][$class] ) ) {
                    import( self::$cache['autoload'][$class] ); return true;
                }
                if ( startsWith( $class, 'App\\' ) ) {
                    $path = config( 'todu.path.app' ).str_replace( '\\', '/', substr( $class, 4 ) ).'.php';
                    if ( file_exists( $path ) ) { import( $path ); } return true;
                }
                if ( startsWith( $class, 'Todu\\' ) ) {
                    $path = ToduPath().'support/'.str_replace( '\\', '/', substr( $class, 5 ) ).'.php';
                    if ( file_exists( $path ) ) { import( $path, false ); } return true;
                }
                return false;
            });
            // 开始缓存信任插件
            foreach( config( 'trust.plugin', [] ) as $pluginName ) {
                plugin( $pluginName );
            }
            // 标记初始化完成
            self::$init = true;
            // 执行回调方法
            return self::permission( 'SYSTEM_STARTUP_RESULTS', 'LAST', is_callable( $method ) ? call_user_func( $method ) : null );
        }
        /**
         * 线程缓存工具
         * - [string]:缓存名称, [callable]:回调方法
         * return [mixed]:返回缓存数据
         */
        public static function cache( string $type, string $name, callable $method ) {
            if ( !is_callable( $method ) ) { return null; }
            switch ( $type ) {
                case 'thread':
                    // 从线程中搜索
                    if ( isset( self::$cache['thread'][$name] ) ) { return self::$cache['thread'][$name]; }
                    // 如果没有数据，则执行回调
                    self::$cache['thread'][$name] = call_user_func( $method );
                    return self::$cache['thread'][$name];
                    break;
                case 'file':
                    // 调试模式禁用时不从文件加载缓存
                    if ( Bootstrap::$init && config( 'app.debug', false ) === true ) { return $method(); }
                    // 从文件中搜索
                    $fileType = endsWith( $name, 'php' ) ? 'php' : 'txt';
                    $name = h( $name );
                    $file = inFolder( config( 'todu.path.cache' )."Bootstrap/{$name}.{$fileType}" );
                    if ( file_exists( $file ) ) { return $fileType === 'php' ? require $file : file_get_contents( $file ); }
                    // 如果没有数据，则执行回调
                    $result = $method();
                    if ( $fileType === 'php' ) {
                        if ( is_array( $result ) ) {
                            file_put_contents( $file, "<?php\nreturn ".var_export( $result, true ).";\n" );
                        }else {
                            file_put_contents( $file, $result ); $result = require $file;
                        }
                        return $result;
                    }
                    file_put_contents( $file, $result );
                    return $result;
                    break;

                default: break;
            }
            // 查询失败
            return null;
        }
        /**
         * 插件权限介入
         * - [string]:权限名称, ['LAST','ALL']:回传方式, [mixed]|null:传递参数, ...[mixed]:附加参数
         * return [mixed]:传递参数
         */
        public static function permission( string $permission, string $echo, $argv = null, ...$addArgv ) {
            if ( !self::$init ) { // 要求驱动已注册
                switch ( $echo ) {
                    case 'LAST': return $argv; break;
                    case 'ALL': return null; break;
                    default: return null; break;
                }
            }
            // 加载执行的任务
            $methods = self::$cache['permissions'][$permission] ?? [];
            if ( !is_array( $methods ) ) { $methods = []; }
            // 顺序执行
            $results = [];
            foreach( $methods as $method ) {
                if ( !is_callable( $method ) ) { continue; }
                $data = call_user_func( $method, $argv, ...$addArgv );
                if ( $data !== null ) { $results[] = $data; }
            }
            switch ( $echo ) {
                case 'LAST':
                    if ( count( $results ) === 0 ) { return $argv; }
                    return $results[count( $results ) - 1];
                    break;
                case 'ALL':
                    if ( count( $results ) === 0 ) { return null; }
                    return array_merge( ...$results );
                    break;

                default: return null; break;
            }
            return $argv;
        }
    }