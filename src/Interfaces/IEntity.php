<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * IEntity
 * Date: 24-03-2016
 *
 */

namespace Talkdesk\Interfaces;


interface IEntity
{
    public function setFromArray($array);

    public function __get($name);

    public function __set($name, $value);
}