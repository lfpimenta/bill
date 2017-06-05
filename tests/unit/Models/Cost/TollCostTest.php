<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 21:54
 */



class TollCostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Talkdesk\Models\Cost\TollCost
     */
    protected function getModel()
    {
        return new Talkdesk\Models\Cost\TollCost();
    }

    /**
     * @dataProvider getCostProvider
     * @param $talkdeskNumber
     * @param $expected
     */
    public function testGetCost($talkdeskNumber, $expected)
    {
        $result = $this->getModel()->getCost($talkdeskNumber);
        $this->assertEquals($expected, $result);

    }

    public function getCostProvider()
    {
        return [
            ['080022222222', '0.06'],
            ['080877777789', '0.06'],
            ['936789098222', '0.01'],
            ['937089098222', '0.01'],
            ['180000076755', '0.03'],
        ];
    }
}
