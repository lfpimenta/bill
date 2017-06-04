<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * TollCost
 * Date: 23-03-2016
 *
 */
namespace Talkdesk\Models\Cost;


use Talkdesk\FactoryConnection;
use Talkdesk\Interfaces\ICost;
use Talkdesk\Traits\TraitClosureFilterBegin;

class TollCost implements ICost
{
    protected $csv = null;
    protected $defaultCost = '0.01';

    use TraitClosureFilterBegin;

    public function __construct($defaultCost = null)
    {
        $this->defaultCost = $defaultCost === null ? $this->defaultCost : $defaultCost;
        $this->csv = FactoryConnection::create('csvTollFree');
    }

    /**
     * Related to talkdesk number, gets the fee for toll free or not free numbers
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $number
     * @return array
     */
    public function getCost($number)
    {

        $result = $this->csv->fetchFilter($this->getCostByBeginCallable($number));

        $costRow = ['', '', ''];
        $maxLength = 0;
        foreach ($result as $re) {
            // Get the matched wider pattern row
            $values = explode(',', $re[2]);
            foreach ($values as $value) {
                $value = trim($value);
                $currentLength = strlen($value);
                if (preg_match("/^{$value}/", $number) && $currentLength > $maxLength) {
                    $maxLength = $currentLength;
                    $costRow = $re;
                }
            }
        }

        if (empty($costRow[1])) {
            return $this->defaultCost;
        }

        return $costRow[1];
    }
}
