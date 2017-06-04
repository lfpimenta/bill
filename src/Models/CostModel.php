<?php
/**
 * @author Luís Pimenta <luis.pimenta@jumia.com>
 * CostModel
 * Date: 17-03-2016
 *
 */
namespace Talkdesk\Models;


use Talkdesk\Models\Cost\ExternalCost;
use Talkdesk\Models\Cost\MarginCost;
use Talkdesk\Models\Cost\TollCost;

/**
 * Responsible for calculate the costs
 * Class CostModel
 * @package Talkdesk\Models
 */
class CostModel
{
    public static $numberPrecision = 5;


    /**
     * Gets the price of the call per minute
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $talkdeskNumber
     * @param $externalNumber
     * @param $minutesMonth
     * @return float
     */
    public function getCost($talkdeskNumber, $externalNumber, $minutesMonth)
    {
        $talkdeskNumberCost = (new TollCost())->getCost($talkdeskNumber);
        $externalNumberCost = (new ExternalCost())->getCost($externalNumber);
        $marginCost = (new MarginCost())->getCost($minutesMonth);

        return $talkdeskNumberCost + $externalNumberCost + $marginCost;
    }
}
