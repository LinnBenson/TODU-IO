<?php

namespace Todu;

    /**
     * 核心驱动器
     */
    class Bootstrap {
        // 驱动器初始化状态
        public static $init = false;
        // 组件缓存
        private static $cache = [
            'thread' => [],
        ];
        /**
         * 初始化驱动器
         * return [void]:无返回值
         */
        public static function init() {
            // 引入系统级通用函数
            require_once ToduPath().'support/Helper/System.php';
            // 引入系统级配置文件
            self::$cache['thread']['config:Todu'] = import( ToduPath( 'config.php' ) );
            // 尝试导入用户配置
            if ( is_array( config( 'app' ) ) ) {
                self::$cache['thread']['config:Todu']['app'] = config( 'app' );
            }
            // 调试模式
            if ( config( 'Todu.app.debug' ) ) {
                error_reporting( E_ALL ); ini_set( 'display_errors', 1 );
            }else {
                error_reporting( 0 ); ini_set( 'display_errors', 0 );
            }
            // 设定时区
            $timezone = config( 'Todu.app.timezone', 'Asia/Singapore' );
            if ( !@date_default_timezone_set( $timezone ) ) {
                trigger_error( "Error in setting timezone: $timezone", E_USER_WARNING );
            }
            // 标记初始化完成
            self::$init = true;
        }
        /**
         * 线程缓存工具
         * - [string]:缓存名称, [callable]:回调方法
         * return [mixed]:返回缓存数据
         */
        public static function cache( string $name, callable $method ) {
            // 从线程中搜索
            if ( isset( self::$cache['thread'][$name] ) ) { return self::$cache['thread'][$name]; }
            // 如果没有数据，则执行回调
            self::$cache['thread'][$name] = call_user_func( $method );
            return self::$cache['thread'][$name];
        }
    }