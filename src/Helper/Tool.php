<?php

namespace Todu\Helper;

    /**
     * 便捷工具集
     */
    class Tool {
        /**
         * 随机数生成器
         * - [number]:生成长度, [all|number|letter]|all:随机生成类型
         * return [string]:随机内容
         */
        public static function rand( int $length, string $type = 'all' ) {
            if ( $type === 'all' ) {
                $data = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
                'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
            }else if ( $type === 'number' ) {
                $data = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
            }else if ( $type === 'letter' ) {
                $data = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
                'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z' );
            }else {
                return null;
            }
            $dataLength = count( $data ) - 1;
            $result = '';
            for( $i = 0; $i < $length; $i++ ) {
                $result .= $data[rand( 0, $dataLength )];
            }
            return $result;
        }
        /**
         * 配置文件覆盖
         * - [string]:文件路径, [array]:配置信息
         * return [boolean]:覆盖结果
         */
        public static function coverConfig( string $file, array $arr ) {
            // 判断配置类型
            if ( endsWith( $file, '.php' ) ) {
                $type = 'php';
            } elseif ( endsWith( $file, '.json' ) ) {
                $type = 'json';
            } else {
                return false;
            }
            // 生成新的配置信息
            if ( $type === 'php' ) {
                $newConfig = array_merge( $arr );
                $content = '<?php'.PHP_EOL.'return '.var_export( $newConfig, true ).';'.PHP_EOL;
                $content = str_replace( '  ', "\t", $content);
                $content = preg_replace( '/=>\s*array \(/', '=> array (', trim( $content ) );
            }else {
                $content = json_encode( $arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES );
            }
            // 写入配置信息
            $result = file_put_contents( $file, $content );
            // 刷新文件
            if ( $type === 'php' ) {
                opcache_invalidate( $file, true );
            }else {
                flush();
            }
            return $result !== false ? true : false;
        }
    }