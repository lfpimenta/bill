<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 00:05
 */

namespace Talkdesk\Entities;


use Talkdesk\Interfaces\IEntity;

abstract class AbstractEntity implements IEntity
{
    /**
     * @param $array
     */
    public function setFromArray($array)
    {
        foreach ($array as $key => $value) {
            if (property_exists(get_class($this), $key)) {
                static::$$key = $value;
            }
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (property_exists(get_class($this), $name)) {
            return static::$$name;
        }
        throw new \Exception("Property {$name} invalid on " . get_class($this));
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (property_exists(get_class($this), $name)) {
            static::$$name = $value;
        }
        throw new \Exception("Property {$name} invalid");
    }
}