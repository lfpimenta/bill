<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 16/03/16
 * Time: 20:37
 */

namespace Talkdesk;


use League\Csv\Reader;

class CsvAdapter
{
    protected $csvFile = null;

    public function __construct(Reader $reader)
    {
        $this->csvFile = $reader;
    }

    public function fetchAll()
    {
        return $this->csvFile->fetchAll();
    }

    public function fetchFilter($callableFilter)
    {
        return $this->csvFile->addFilter($callableFilter)->fetch();
    }
}