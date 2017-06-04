<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 21:54
 */



class MarginCostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Talkdesk\Models\Cost\MarginCost
     */
    protected function getModel()
    {
        return new Talkdesk\Models\Cost\MarginCost();
    }

    /**
     * @dataProvider getCostProvider
     * @param $minutesUsed
     * @param $expected
     */
    public function testGetCost($minutesUsed, $expected)
    {
        $result = $this->getModel()->getCost($minutesUsed);
        $this->assertEquals($expected, $result);

    }

    public function getCostProvider()
    {
        return [
            ['80022222222', '0.02'],
            ['150', '0.04'],
            ['300', '0.03'],
            ['350', '0.03'],
            ['501', '0.02'],
            ['', '0.05'],
            [null, '0.05']
        ];
    }
}
