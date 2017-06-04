<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 15/03/16
 * Time: 23:34
 */

namespace Talkdesk\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Talkdesk\Entities\AbstractEntity;

class ListCommand extends AbstractCallBillCommand
{
    protected $callModel = null;

    protected function configure()
    {
        $this->setName('listInboundCalls')
            ->setDescription('Lists the charges for the given account')
            ->addArgument('account_name', InputArgument::REQUIRED, 'Account name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountName = $input->getArgument('account_name');
        $result = $this->getCalls(
            $accountName,
            'inbound',
            [
                'date',
                'call_type',
                'duration',
                'cost',
                'balance',
                'talkdesk_phone_number',
                'customer_phone_number',
                'forwarded_phone_number'
            ]);
        
        if (!$result) {
            $output->writeln("No results found for that account");
            return static::EXIT_UNSUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(
            [
                'Date',
                'Type',
                'Duration',
                'Cost',
                'Balance',
                'Talkdesk Phone Number',
                'Customer Phone Number',
                'Forwarded Phone Number'
            ]
        );

        $table->setRows($result)->render();
    }

    protected function getCalls($accountName, $type, $columns)
    {
        $callModel = $this->getCallModel();
        if ($callModel) {
            return $callModel->getAll($accountName, $type, $columns);
        }
        return false;
    }

}