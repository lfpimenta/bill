<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 16/03/16
 * Time: 00:16
 */

namespace Talkdesk\Models;

use Talkdesk\DatabaseAdapter;
use Talkdesk\Entities\EAccount;
use Talkdesk\Entities\EAccountCallMonth;
use Talkdesk\Entities\EAccountCredit;
use Talkdesk\Entities\ECall;
use Talkdesk\Interfaces\IEntity;
use Talkdesk\Interfaces\IMessage;
use Talkdesk\Interfaces\IModelDB;

class CallModel implements IModelDB, IMessage
{
    const INBOUND_CALL = 'inbound';
    const OUTBOUND_CALL = 'outbound';
    /**
     * @var AccountModel
     */
    protected static $accountModel = null;
    protected static $costModel = null;
    protected static $accountCallMonthModel = null;
    protected $database;
    protected $tableName = 'call';
    protected $account = null;
    protected $accountCallMount = null;
    protected $messages = [];

    public function __construct(
        DatabaseAdapter $database
    )
    {
        $this->database = $database;
        self::$costModel = new CostModel();
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @return AccountModel
     */
    protected function getAccountModel()
    {
        if (self::$accountModel === null) {
            self::$accountModel = new AccountModel($this->database);
        }
        return self::$accountModel;
    }

    /**
     * @param $accountName
     * @return bool|null|EAccount
     */
    protected function getAccount($accountName)
    {
        if ($this->account === null) {
            $account = current($this->getAccountModel()->getByName($accountName));
            if ($account) {
                $entityAccount = new EAccount();
                $entityAccount->setFromArray(
                    [
                        'idAccount' => $account['id_account'],
                        'name' => $account['name'],
                        'credit' => $account['credit']
                    ]
                );
                $this->account = $entityAccount;
            } else {
                $this->addMessage("Account name not found");
                $this->account = false;
            }
        }
        return $this->account;
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @return AccountCallMonth
     */
    protected function getAccountCallMonth()
    {
        if (self::$accountCallMonthModel === null) {
            self::$accountCallMonthModel = new AccountCallMonth($this->database);
        }
        return self::$accountCallMonthModel;
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $accountName
     * @return int
     */
    protected function getAccountCallMonthMinutesUsed($accountName)
    {
        $idAccount = $this->getAccount($accountName)->idAccount;
        return $this->getAccountCallMonth()->getMinutesUsed($idAccount);
    }

    /**
     * @author Luís Pimenta
     * @param $callDuration
     * @param $accountName
     * @param $talkdeskPhoneNumber
     * @param $customerPhoneNumber
     * @param null $forwardedPhoneNumber
     * @return bool
     */
    public function add($callDuration, $accountName, $talkdeskPhoneNumber, $customerPhoneNumber, $forwardedPhoneNumber = null)
    {
        if (!$this->getAccount($accountName)) {
            return false;
        }

        $idAccount = $this->getAccount($accountName)->idAccount;
        $minutesMonth = $this->getAccountCallMonthMinutesUsed($accountName);
        $currentCredit = $this->getAccount($accountName)->credit;

        $currentCost = $this->getCost($forwardedPhoneNumber, $talkdeskPhoneNumber, $minutesMonth, $callDuration);
        $balance = (float)$currentCredit - $currentCost;
        $credit = $balance;

        $statements = $this->getStatementSet(
            $callDuration,
            $talkdeskPhoneNumber,
            $customerPhoneNumber,
            $forwardedPhoneNumber,
            $currentCost,
            $balance,
            $idAccount,
            $credit,
            $minutesMonth
        );

        if (!$this->database->queriesWithTransaction($statements)) {
            foreach ($this->database->getMessages() as $message) {
                $this->addMessage($message);
            }
        }
        return true;
    }

    /**
     * @author Luís Pimenta
     * @param $accountName
     * @param $type
     * @return bool
     */
    public function getAll($accountName, $type = 'inbound', $columns = '*')
    {
        if (!$this->getAccount($accountName)) {
            return false;
        }
        $columns = ($columns === '*') ? '*' : implode(', ', $columns);
        $rows = $this->database->fecth(
            "SELECT {$columns} FROM `{$this->tableName}` WHERE `fk_account` = ? AND `call_type` = ? order by 1 desc",
            [$this->getAccount($accountName)->idAccount, $type]
        );

        return $rows;


    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $number
     * @param $talkdeskNumber
     * @param $minutesMonth
     * @param $callDuration
     * @return float
     */
    protected function getCost($number, $talkdeskNumber, $minutesMonth, $callDuration)
    {
        if (!empty($this->account)) {
            $costPerMinute = self::$costModel->getCost($talkdeskNumber, $number, $minutesMonth);
            return round($callDuration * ($costPerMinute / 60), CostModel::$numberPrecision);

        }
        return 0.0;
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @param $entityCall
     * @return array
     */
    public function changeDataStatement(IEntity $entityCall)
    {
        $sql = "INSERT INTO `{$this->tableName}` "
            . "(`date`, `duration`, `cost`, `balance`, "
            . "`fk_account`, `talkdesk_phone_number`, `customer_phone_number`, "
            . "`forwarded_phone_number`, `call_type`) "
            . "VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
        $parameters = [
            $entityCall->duration,
            $entityCall->cost,
            $entityCall->balance,
            $entityCall->fkAccount,
            $entityCall->talkdeskPhoneNumber,
            $entityCall->customerPhoneNumber,
            $entityCall->forwardedPhoneNumber,
            $entityCall->callType
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters
        ];
    }

    /**
     * Get statements to be executed in transaction
     * @param $callDuration
     * @param $talkdeskPhoneNumber
     * @param $customerPhoneNumber
     * @param $forwardedPhoneNumber
     * @param $currentCost
     * @param $balance
     * @param $idAccount
     * @param $credit
     * @param $minutesMonth
     * @return array
     */
    protected function getStatementSet(
        $callDuration,
        $talkdeskPhoneNumber,
        $customerPhoneNumber,
        $forwardedPhoneNumber,
        $currentCost,
        $balance,
        $idAccount,
        $credit,
        $minutesMonth
    ) {
        // Insert inbound call
        $callEntity = new ECall();
        $callEntity->setFromArray([
            'duration' => $callDuration,
            'cost' => $currentCost,
            'balance' => $balance,
            'fkAccount' => $idAccount,
            'talkdeskPhoneNumber' => $talkdeskPhoneNumber,
            'customerPhoneNumber' => $customerPhoneNumber,
            'forwardedPhoneNumber' => $forwardedPhoneNumber,
            'callType' => self::INBOUND_CALL
        ]);
        $insertCallStatement = $this->changeDataStatement($callEntity);


        // Account decrease credit
        $accountCreditEntity = new EAccountCredit();
        $accountCreditEntity->setFromArray(
            [
                'idAccount' => $idAccount,
                'credit' => $credit
            ]
        );
        $accountUpdateStatement = $this->getAccountModel()->changeDataStatement($accountCreditEntity);

        // Increase usage in current month
        $accountCallMonthEntity = new EAccountCallMonth();
        $accountCallMonthEntity->setFromArray(
            [
                'idAccount' => $idAccount,
                'duration' => $callDuration,
                'minutesMonth' => $minutesMonth
            ]
        );
        $currentMonthIncreaseStatement = $this->getAccountCallMonth()->changeDataStatement($accountCallMonthEntity);

        return [$insertCallStatement, $accountUpdateStatement, $currentMonthIncreaseStatement];
    }

    /**
     * @param $msg
     */
    protected function addMessage($msg)
    {
        $this->messages[] = $msg;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}