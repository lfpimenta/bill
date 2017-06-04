<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 16/03/16
 * Time: 00:01
 */

namespace Talkdesk\Models;


use Talkdesk\DatabaseAdapter;
use Talkdesk\Interfaces\IEntity;
use Talkdesk\Interfaces\IModelDB;

class AccountModel implements IModelDB
{
    protected $database;
    protected $tableName = 'account';

    public function __construct(DatabaseAdapter $database)
    {
        $this->database = $database;
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $name
     * @return array
     */
    public function getByName($name)
    {
        return $this->database->fecth("SELECT * FROM $this->tableName WHERE `name` = ?", [$name]);
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $accountCreditEntity
     * @return array
     */
    public function changeDataStatement(IEntity $accountCreditEntity)
    {
        return [
            "sql" => "UPDATE {$this->tableName} SET `credit` = ? WHERE `id_account` = ?",
            "parameters" => [
                $accountCreditEntity->credit,
                $accountCreditEntity->idAccount
            ]
        ];
    }
}