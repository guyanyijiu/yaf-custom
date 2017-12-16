<?php

/**
 * 数学计算
 *
 * Class Math
 *
 * @author  liuchao
 */
class Math {

    /**
     * 向上保留
     *
     * @param     $number
     * @param int $precision
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function roundUp($number, $precision = 2) {
        $mult = $mult = pow(10, abs($precision));

        return $precision < 0
            ? ceil($number / $mult) * $mult : ceil($number * $mult) / $mult;
    }

    /**
     * 向下保留
     *
     * @param     $number
     * @param int $precision
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function roundDown($number, $precision = 2) {
        $mult = $mult = pow(10, abs($precision));

        return $precision < 0
            ? floor($number / $mult) * $mult : floor($number * $mult) / $mult;
    }

    /**
     * 四舍五入
     *
     * @param     $number
     * @param int $precision
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function round($number, $precision = 2) {
        return \round($number, $precision);
    }

    /**
     * 计算期率
     *
     * @param $annual
     * @param $stage_step
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function stageRate($annual, $stage_step) {
        return $annual / 360 * $stage_step;
    }

    /**
     * PPMT
     *
     * @param $rate
     * @param $current_stage
     * @param $total_stage
     * @param $amount
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function ppmt($rate, $current_stage, $total_stage, $amount) {
        return
            ($amount * $rate * pow(1 + $rate, $current_stage - 1))
            /
            (pow(1 + $rate, $total_stage) - 1);
    }

    /**
     * IPMT
     *
     * @param $rate
     * @param $current_stage
     * @param $total_stage
     * @param $amount
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function ipmt($rate, $current_stage, $total_stage, $amount) {
        return
            ($amount * $rate * (pow(1 + $rate, $total_stage) - pow(1 + $rate, $current_stage - 1)))
            /
            (pow(1 + $rate, $total_stage) - 1);
    }

    /**
     * PMT
     *
     * @param $rate
     * @param $total_stage
     * @param $amount
     *
     * @return float|int
     *
     * @author  liuchao
     */
    public static function pmt($rate, $total_stage, $amount) {
        return
            ($amount * $rate * pow(1 + $rate, $total_stage))
            /
            (pow(1 + $rate, $total_stage) - 1);
    }


}