<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 23/03/16
 * Time: 22:34
 */

namespace Talkdesk\Models\Cost;


use Talkdesk\FactoryConnection;
use Talkdesk\Interfaces\ICost;
use Talkdesk\Traits\TraitClosureFilterBegin;

class ExternalCost implements ICost
{
    use TraitClosureFilterBegin;

    protected $csv = null;
    protected $defaultCost = '0.0';

    public function __construct()
    {
        $this->csv = FactoryConnection::create('csvCountry');
    }

    /**
     * @param $number
     * @return float
     */
    public function getCost($number)
    {
        if (!$number) {
            return 0.0;
        }

        $result = $this->csv->fetchFilter($this->getCostByBeginCallable($number));

        $costRow = ['', $this->defaultCost, ''];
        $maxLength = 0;

        foreach ($result as $re) {
            // Get the matched wider pattern row
            $values = explode(',', $re[2]);
            foreach ($values as $value) {
                $value = trim($value);
                $currentLength = strlen($value);
                if (preg_match("/^{$value}/", $number) && $currentLength > $maxLength) {
                    $maxLength = $currentLength;
                    $costRow = $re;
                }
            }
        }

        return $costRow[1];
    }
}