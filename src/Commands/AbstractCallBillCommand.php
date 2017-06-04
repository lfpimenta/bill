<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 25/03/16
 * Time: 20:11
 */

namespace Talkdesk\Commands;

use Talkdesk\FactoryConnection;
use Talkdesk\Models\CallModel;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCallBillCommand extends Command
{
    protected $callModel = null;
    protected $error = "";

    const EXIT_UNSUCCESS = 255;
    const EXIT_SUCCESS = 0;

    /**
     * @return CallModel
     * @throws \Exception
     */
    protected function getCallModel()
    {
        if ($this->callModel === null) {

            try {
                $database = FactoryConnection::create('db');
            } catch (\Exception $ex) {
                $this->error = $ex->getMessage();
                return false;
            }
            $this->callModel = new CallModel(
                $database
            );
        }
        return $this->callModel;
    }
}