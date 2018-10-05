<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 21:01
 * @description:  常用的数组处理工具
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Utils;

class ArrayUtil
{
    /**
     * 移除空值的key
     * @param $para
     * @return array
     * @author helei
     */
    public static function paraFilter($para)
    {
        $paraFilter = [];
        while (list($key, $val) = each($para)) {
            if ($val === '' || $val === null) {
                continue;
            } else {
                if (! is_array($para[$key])) {
                    $para[$key] = is_bool($para[$key]) ? $para[$key] : trim($para[$key]);
                }

                $paraFilter[$key] = $para[$key];
            }
        }

        return $paraFilter;
    }

    /**
     * 删除一位数组中，指定的key与对应的值
     * @param array $inputs 要操作的数组
     * @param array|string $keys 需要删除的key的数组，或者用（,）链接的字符串
     * @return array
     */
    public static function removeKeys(array $inputs, $keys)
    {
        if (! is_array($keys)) {// 如果不是数组，需要进行转换
            $keys = explode(',', $keys);
        }

        if (empty($keys) || ! is_array($keys)) {
            return $inputs;
        }

        $flag = true;
        foreach ($keys as $key) {
            if (array_key_exists($key, $inputs)) {
                if (is_int($key)) {
                    $flag = false;
                }
                unset($inputs[$key]);
            }
        }

        if (! $flag) {
            $inputs = array_values($inputs);
        }
        return $inputs;
    }

    /**
     * 对输入的数组进行字典排序
     * @param array $param 需要排序的数组
     * @return array
     * @author helei
     */
    public static function arraySort(array $param)
    {
        ksort($param);
        reset($param);

        return $param;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param array $para 需要拼接的数组
     * @return string
     * @throws \Exception
     */
    public static function createLinkstring($para)
    {
        if (! is_array($para)) {
            throw new \Exception('必须传入数组参数');
        }

        reset($para);
        $arg = '';
        while (list($key, $val) = each($para)) {
            if (is_array($val)) {
                continue;
            }

            $arg.=$key.'='.urldecode($val).'&';
        }
        //去掉最后一个&字符
        $arg && $arg = substr($arg, 0, -1);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }
    
    public static function getWxNotifyData(){
        // php://input 带来的内存压力更小
        $data = @file_get_contents('php://input');// 等同于微信提供的：$GLOBALS['HTTP_RAW_POST_DATA']
        // 将xml数据格式化为数组
        $arrData = DataParser::toArray($data);
        if (empty($arrData)) {
            return false;
        }
        // 移除值中的空格  xml转化为数组时，CDATA 数据会被带入额外的空格。
        return self::paraFilter($arrData);
    }
}