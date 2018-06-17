<?php
// +----------------------------------------------------------------------
// | CalculaterInterface.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Know\Calculater;

use Know\Calculater\Adapter\Adder;
use Know\Calculater\Exceptions\CalculaterException;

class Calculater
{
    public $adapter = [
        '+' => Adder::class,
    ];

    public function calculater($string, $params = [])
    {
        list($cal, $string) = explode(' ', $string, 2);

        if (!isset($this->adapter[$cal])) {
            throw new CalculaterException('Calcaulater Adapter is not defined.');
        }

        $string = trim($string);

        $pre_arguments = [];
        $param = '';
        $depth = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];
            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
            }
            $param .= $char;

            if ($depth === 0) {
                if (!empty(trim($param))) {
                    $pre_arguments[] = $param;
                }
                $param = '';
            }
        }

        $arguments = [];
        foreach ($pre_arguments as $argument) {
            if (is_numeric($argument)) {
                $arguments[] = $argument;
            } else {
                preg_match('/^\((.*)\)$/', $argument, $result);
                if (!isset($result[1])) {
                    throw new BizException(ErrorCode::$ENUM_CALCULATER_STRING_INVALID);
                }

                if (is_numeric($result[1])) {
                    $arguments[] = $params[$result[1]];
                } else {
                    $arguments[] = $this->calculater($result[1], $params);
                }
            }
        }

        $adapter = new $this->adapter[$cal](...$arguments);
        return $adapter->handle();
    }
}