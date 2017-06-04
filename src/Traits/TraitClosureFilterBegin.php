<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * TraitCallBackBegin
 * Date: 24-03-2016
 *
 */
namespace Talkdesk\Traits;


trait TraitClosureFilterBegin
{
    /**
     * Filter rows that match the beginning of the number
     * @author Luís Pimenta
     * @param $number
     * @return \Closure
     */
    protected function getCostByBeginCallable($number)
    {
        return function ($row, $index) use ($number) {
            // protect this
            if (strpos($row[2], ',')) {
                $values = explode(',', $row[2]);
            } else {
                $values = [$row[2]];
            }
            foreach ($values as $value) {
                $value = trim($value);
                if (preg_match("/^{$value}/", $number)) {
                    return true;
                }
            }
            return false;
        };
    }
}
