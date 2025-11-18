<?php

namespace Todu\Helper;

use Todu\Bootstrap;

    /**
     * Shell 工具
     */
    class Shell {
        /**
         * 输出状态结果
         * - [boolean]:状态, [mixed]:输出内容, [boolean]|true:是否直接输出
         * return [string]:返回输出内容
         */
        public static function echo( $status, $data, $direct = true ) {
            $statusText = Bootstrap::$init ? [ __( 'shell.echo.true' ), __( 'shell.echo.false' ) ] : [ 'Success', 'Error' ];
            $status = !empty( $status ) ? "{bg}{b}{$statusText[0]}" : "{br}{b}{$statusText[1]}";
            return self::line(array_merge([
                "{$status} | {time}{end}",
                '{-}',
            ], is_array( $data ) ? $data : [ $data ] ), $direct );
        }
        /**
         * 确认操作
         * - [string]:提示文本, [mixed]:确认结果, [mixed]|null:取消结果
         * return [mixed]:返回执行结果
         */
        public static function confirm( $text, $yes, $no = null ) {
            $input = self::input( "{$text} (y/n):" );
            $input = strtolower( trim( $input ) );
            if ( in_array( $input, [ 'y', 'yes' ] ) ) {
                return is_callable( $yes ) ? call_user_func( $yes ) : $yes;
            }else {
                if ( $no === null ) {
                    $cancel = Bootstrap::$init ? __( 'shell.cancel' ) : 'The operation has been cancelled.';
                    return self::line( "{cr}{$cancel}{end}" );
                }
                return is_callable( $no ) ? call_user_func( $no ) : $no;
            }
        }
        /**
         * 显示进度条
         * - [number]:总数, [number]:当前数
         * return [void]:无返回值
         */
        public static function schedule( $total, $current ) {
            $current = $current + 1;
            $current = intval( $current ); $total = intval( $total );
            $percentage = $current / $total;
            $progress = round( $percentage * 30 );
            $schedule = "{cg}".str_repeat( '>', $progress )."{end}".str_repeat( '-', 30 - $progress );
            echo Shell::line( "\r{bg} {$total}/{$current} {end} [{$schedule}] {bg} ".round( $percentage * 100 )."% {end}", false );
            if ( round( $percentage * 100 ) >= 100 ) { echo PHP_EOL; }
        }
        /**
         * 输入选项菜单
         * - [array]:菜单数据, [string]|null:直接执行方法
         * return [mixed]:执行项目结果
         */
        public static function menu( $data, $run = null ) {
            $text = []; $method = []; $methodNum = 1;
            foreach( $data as $key => $value ) {
                // 标题
                if ( $key === 'title' && is_string( $value ) ) {
                    $text[] = "{cb}{b}{$value}{end}";
                    continue;
                }
                // 普通文本
                if ( is_numeric( $key ) && is_string( $value ) ) {
                    $text[] = $value;
                    continue;
                }
                // 选项方法
                if ( is_array( $value ) && count( $value ) === 2 && is_callable( $value[1] ) ) {
                    $option = is_string( $key ) ? $key : $methodNum;
                    $methodNum++;
                    $method["option_{$option}"] = $value[1];
                    $text[] = "{$option}. {$value[0]}";
                    continue;
                }
                // 其它选项处理
                if ( $key === 'other' && is_callable( $value ) ) {
                    $method["other"] = $value;
                    continue;
                }
            }
            if ( $run === null ) {
                self::line( $text );
                $run = self::input();
            }
            if ( isset( $method["option_{$run}"] ) && is_callable( $method["option_{$run}"] ) ) {
                return call_user_func( $method["option_{$run}"] );
            }
            $nullText = Bootstrap::$init ? __( 'shell.null' ) : 'The selected option does not exist!';
            return isset( $method["other"] ) && is_callable( $method["other"] ) ? call_user_func( $method["other"] ) : self::echo( false, $nullText );
        }
        /**
         * 获取用户输入
         * - [string]|'Please enter:':提示文本
         * return [string]:返回用户输入内容
         */
        public static function input( $text = null ) {
            if ( $text === null ) {
                $text = Bootstrap::$init ? __( 'shell.input' ) : 'Please enter:';
            }
            echo self::line( "\n{bc}{b} {$text} {end} ", false );
            $input = trim( fgets( STDIN ) );
            echo "\033[F\033[2K\r";
            return $input;
        }
        /**
         * 输出文本
         * - [mixed]:输出内容, [boolean]|true:是否直接输出
         * return [string]:返回输出内容
         */
        public static function line( $text, $direct = true ) {
            // 内容处理
            if ( is_json( $text ) ) { $text = json_encode( $text, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ); }
            $text = toString( $text );
            // 符号替换
            $symbol = array(
                'cr' => "\033[31m", // 红色文字
                'cg' => "\033[32m", // 绿色文字
                'cy' => "\033[33m", // 黄色文字
                'cb' => "\033[34m", // 蓝色文字
                'cp' => "\033[35m", // 紫色文字
                'cc' => "\033[36m", // 青色文字
                'cw' => "\033[37m", // 白色文字
                'br' => "\033[41m", // 红色背景
                'bg' => "\033[42m", // 绿色背景
                'by' => "\033[43m", // 黄色背景
                'bb' => "\033[44m", // 蓝色背景
                'bp' => "\033[45m", // 紫色背景
                'bc' => "\033[46m", // 青色背景
                'bw' => "\033[47m", // 白色背景
                'b' => "\033[1m", // 加粗
                'i' => "\033[3m", // 斜体
                'u' => "\033[4m", // 下划线
                'end' => "\033[0m", // 重置样式
                'time' => toDate(), // 当前时间
                '-' => "------------", // 分割线
                '=' => "============" // 分割线
            );
            foreach( $symbol as $k => $v ) { $text = preg_replace( '/\{'.$k.'\}/', $v, $text ); };
            // 返回内容
            if ( $direct ) { echo $text.PHP_EOL; }
            return $text;
        }
        /**
         * 解析命令行参数
         * - [array]:参数数组, [FILE|GET|POST]|null:数据类型
         * return [array|string]:返回解析内容
         */
        public static function validate( $argv, $type = null ) {
            // 参数处理
            $result = [ 'FILE' => null, 'GET' => [], 'POST' => [] ];
            if ( is_string( $type ) ) { $type = strtoupper( $type ); }
            if ( !is_array( $argv ) ) { return $type ? $result[$type] : $result; }
            // 数据处理
            if ( isset( $argv[0] ) ) { $result['FILE'] = $argv[0]; array_shift( $argv ); }
            foreach( $argv as $value ) {
                if ( preg_match( '/^[^:]{1,16}:.+$/', $value ) ) {
                    list( $k, $v ) = explode( ':', $value, 2 );
                    $k = trim( $k ); $v = trim( $v );
                    $result['POST'][$k] = $v;
                }else {
                    $result['GET'][] = $value;
                }
            }
            // 返回数据
            return $type ? $result[$type] : $result;
        }
    }