<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 16/03/16
 * Time: 00:30
 */

namespace Talkdesk;

use PDO;
use \Talkdesk\Interfaces\IMessage;

class DatabaseAdapter implements IMessage
{
    /**
     * @var PDO
     */
    protected $connection;

    protected $messages = [];

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $tableName
     * @return array
     */
    public function fetchAll($tableName)
    {
        return $this->connection->query("SELECT * FROM " . $tableName)->fetchAll();
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $sql
     * @param $parameters
     * @return array
     */
    public function fecth($sql, $parameters)
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($parameters);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->fetchAll();
    }

    /**
     * @param $statements
     * @return bool
     */
    public function queriesWithTransaction($statements)
    {
        $this->beginTransaction();
        foreach ($statements as $statement) {
            try {
                $sql = $statement['sql'];
                $parameters = $statement['parameters'];
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($parameters);

            } catch (\Exception $ex) {
                $this->rollback();
                $this->addMessage($ex->getMessage());
                return false;
            }
        }

        if (!$this->commit()) {
            $this->addMessage('Commit unsuccessful');
            return false;
        }
        return true;
    }

    protected function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    protected function commit()
    {
        $this->connection->commit();
    }

    protected function rollback()
    {
        $this->connection->rollBack();
    }

    protected function addMessage($msg)
    {
        $this->messages[] = $msg;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}