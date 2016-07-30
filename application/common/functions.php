<?php
/**
* @desc 应用自定义函数文件。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月7日    上午12:05:45
*/

use framework\utils\Pagination2;

/**
 * 创建分页。
 * @param unknown $total 记录总数
 * @param unknown $page 当前页
 * @param number $size 每页数
 * @return string 分页HTML代码
 */
function getPagination($total, $page, $size = 10)
{
    $totalPage = ceil($total/$size);
    return Pagination2::create([
        'total' => $totalPage, 
        'current' => $page,
        'centerSize' => 2,
        'sideSize' => 1,
        'firstText' => false,
        'lastText' => false,
    ]);
}

if (!function_exists('array_column'))
{
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $res = array();
        if (empty($indexKey))
        {
            if (empty($columnKey))
            {
                return $input;
            }
            foreach ($input as $value)
            {
                if (isset($value[$columnKey]))
                {
                    $res[] = $value[$columnKey];
                }
            }
        }
        else
        {
            if (empty($columnKey))
            {
                foreach ($input as $value)
                {
                    if (isset($value[$indexKey]))
                    {
                        $res[$value[$indexKey]] = $value;
                    }
                }
            }
            else
            {
                foreach ($input as $value)
                {
                    if (isset($value[$indexKey]) && isset($value[$columnKey]))
                    {
                        $res[$value[$indexKey]] = $value[$columnKey];
                    }
                }
            }
        }
        return $res;
    }
}
