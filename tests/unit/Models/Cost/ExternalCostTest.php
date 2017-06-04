<?php

/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 21:54
 */
class ExternalCostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Talkdesk\Models\Cost\ExternalCost
     */
    protected function getModel()
    {
        return new Talkdesk\Models\Cost\ExternalCost();
    }

    /**
     * @dataProvider getCostProvider
     * @param $externalNumber
     * @param $expected
     */
    public function testGetCost($externalNumber, $expected)
    {
        $result = $this->getModel()->getCost($externalNumber);
        $this->assertEquals($expected, $result);

    }

    public function getCostProvider()
    {
        return [
            ['111222345344', '0.01500'],
            ['111234444333', '0.01500'],
            ['936789098222', '0.29000'],
            ['937089098222', '0.41500'],
            ['937289098222', '0.41500'],
            ['937589098222', '0.41500'],
            ['355729888665', '0.25500'],
            ['126454345667', '0.31500'],
            ['316404545667', '0.05500'],
            ['999999999999', '0.0'],
            [null, '0.0']
        ];
    }
}
