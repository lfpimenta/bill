<?php

/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 26/03/16
 * Time: 18:28
 */
class CallModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return \Talkdesk\Models\CallModel
     */
    protected function getModel()
    {
        $stub = $this->getMockBuilder('\Talkdesk\DatabaseAdapter')
            ->disableOriginalConstructor()
            ->getMock();
        $model = new \Talkdesk\Models\CallModel($stub);
        return $model;
    }

    /**
     * @dataProvider providerTestGetCost
     */
    public function testGetCost($validAccount, $talkedeskNumber, $externalNumber, $minutesMonth, $duration, $expected)
    {
        $model = $this->getModel();
        $this->setProtectedProperty($model, 'account', $validAccount);
        //var_dump(get_class_methods(get_class($model)));

        $result = $this->invokeMethod(
            $model,
            'getCost',
            [
                'number' => $externalNumber,
                'talkdeskNumber' => $talkedeskNumber,
                'minutesMonth' => $minutesMonth,
                'callDuration' => $duration
            ]
        );
        $this->assertEquals(
            round($expected, \Talkdesk\Models\CostModel::$numberPrecision),
            round($result, \Talkdesk\Models\CostModel::$numberPrecision)
        );
    }

    public function providerTestGetCost()
    {
        return [
            [true, '080022222222', '111222345344', 150, 1, (0.06 + 0.01500 + 0.04) / 60],
            [true, '936789098222', '', 300, 2, 2 * ((0.01 + 0.0 + 0.03) / 60)],
            [true, '180000076755', '126454345667', 2, 89, 89 * (0.03 + 0.31500 + 0.05) / 60],
            [true, '180000076755', '', 2, 56, 56 * (0.03 + 0.0 + 0.05) / 60],
            [false, '180000076755', '126454345667', 2, 56, 0.0],
        ];
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function setProtectedProperty(&$object, $property, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
