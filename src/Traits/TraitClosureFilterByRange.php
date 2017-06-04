<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 00:43
 */

namespace Talkdesk\Traits;


trait TraitClosureFilterByRange
{
    /**
     * Filters rows according to the range number fits
     * @author Luís Pimenta
     * @param $number
     * @return \Closure
     */
    protected function getCostRangeCallable($number)
    {
        return function ($row, $index) use ($number) {
            $to = empty($row[1]) ? PHP_INT_MAX : $row[1];
            return ($number >= $row[0] && $number < $to);
        };
    }
}