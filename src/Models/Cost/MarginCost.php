<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * MarginCost
 * Date: 23-03-2016
 *
 */
namespace Talkdesk\Models\Cost;


use Talkdesk\FactoryConnection;
use Talkdesk\Interfaces\ICost;
use Talkdesk\Traits\TraitClosureFilterByRange;

class MarginCost implements ICost
{
    protected $csv = null;
    protected $defaultMargin = 0.05;

    use TraitClosureFilterByRange;

    public function __construct($defaultMargin = '')
    {
        $this->defaultMargin = empty($defaultMargin) ? $this->defaultMargin : $defaultMargin;
        $this->csv = FactoryConnection::create('csvMargin');
    }

    /**
     * Related to the margin, margin decreases as client's usage increases on current month
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $number
     * @return array
     */
    public function getCost($number)
    {
        $result = $this->csv->fetchFilter($this->getCostRangeCallable($number));

        $costRow = ['', '', $this->defaultMargin];
        foreach ($result as $re) {
            $costRow = $re;
        }

        return $costRow[2];
    }
}
