<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * AccountCallMonth
 * Date: 22-03-2016
 *
 */
namespace Talkdesk\Models;


use Talkdesk\DatabaseAdapter;
use Talkdesk\Interfaces\IEntity;
use Talkdesk\Interfaces\IModelDB;

class AccountCallMonth implements IModelDB
{
    protected $month;
    protected $year;
    protected $database;
    protected $tableName = 'account_call_month';

    public function __construct(DatabaseAdapter $database)
    {
        $this->database = $database;
        $now = new \DateTime('now');
        $this->month = (int)$now->format('m');
        $this->year = $now->format('Y');
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $fkAccount
     * @return int
     */
    public function getMinutesUsed($fkAccount)
    {
        $row = $this->database->fecth(
            "SELECT duration FROM $this->tableName WHERE `fk_account` = ? AND `year` = ? and `month` = ?",
            [$fkAccount, $this->year, $this->month]);

        if (!$row) {
            return 0;
        }

        return current($row)['duration'];
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $entityAccountCall
     * @return array
     */
    public function changeDataStatement(IEntity $entityAccountCall)
    {
        $minutesToAdd = $entityAccountCall->duration / 60;
        $newMinutes = round($entityAccountCall->minutesMonth + $minutesToAdd, CostModel::$numberPrecision);
        if ($entityAccountCall->minutesMonth == 0) {
            $sql = "INSERT INTO {$this->tableName} (`duration`, `fk_account`, `month`, `year`) VALUES (?, ?, ?, ?)";
        } else {
            $sql = "UPDATE {$this->tableName} SET `duration` = ? WHERE `fk_account` = ? AND `month` = ? AND `year` = ?";
        }

        return [
            'sql' => $sql,
            'parameters' => [$newMinutes, $entityAccountCall->idAccount, $this->month, $this->year]
        ];

    }
}
