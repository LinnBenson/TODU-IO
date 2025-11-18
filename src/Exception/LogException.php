<?php

namespace Todu\Exception;

use Exception;
use Todu\Helper\Debug;

/**
 * 记录日志的异常
 */
class LogException extends Exception {
    /**
     * 抛出异常
     */
    public function __construct( $message ) {
        Debug::log( 'Bootstrap', $message );
        if ( is_array( $message ) ) { $message = implode( ' | ', $message ); }
        parent::__construct( $message );
    }
}