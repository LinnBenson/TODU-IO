<?php
    /**
     * 应用配置文件
     */
    return [
        /**
         * 启用程序
         */
        'enable' => (bool) env( 'APP_ENABLE', true ),
        /**
         * 应用名称
         */
        'name' => env( 'APP_NAME', 'TODU.IO' ),
        /**
         * 应用标题
         */
        'title' => env( 'APP_TITLE', env( 'APP_NAME', 'TODU.IO' ) ),
        /**
         * 主机地址
         */
        'host' => env( 'APP_HOST', 'localhost' ),
        /**
         * 语言设置
         */
        'lang' => env( 'APP_LANG', 'zh-CN' ),
        /**
         * 调试模式
         */
        'debug' => (bool) env( 'APP_DEBUG', false ),
        /**
         * 运行时区
         */
        'timezone' => env( 'APP_TIMEZONE', 'Asia/Singapore' )
    ];