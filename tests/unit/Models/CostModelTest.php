<?php

/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 26/03/16
 * Time: 16:53
 */
class CostModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return \Talkdesk\Models\CostModel
     */
    protected function getModel()
    {
        return new Talkdesk\Models\CostModel();
    }

    /**
     * @dataProvider providerTestGetCost
     */
    public function testGetCost($talkedeskNumber, $externalNumber, $minutesMonth, $expected)
    {
        $result = $this->getModel()->getCost($talkedeskNumber, $externalNumber, $minutesMonth);
        $this->assertEquals($expected, $result);
    }

    public function providerTestGetCost()
    {
        return [
            ['080022222222', '111222345344', 150, (0.06 + 0.01500 + 0.04)],
            ['936789098222', '', 300, (0.01 + 0.0 + 0.03)],
            ['180000076755', '126454345667', 2, (0.03 + 0.31500 + 0.05)],
            ['180000076755', '', 2, (0.03 + 0.0 + 0.05)],
        ];
    }
}
