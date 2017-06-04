<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * IModelDB
 * Date: 24-03-2016
 *
 */

namespace Talkdesk\Interfaces;


interface IModelDB
{
    public function changeDataStatement(IEntity $values);
}