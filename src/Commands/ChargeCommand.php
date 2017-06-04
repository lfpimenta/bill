<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 15/03/16
 * Time: 23:21
 */

namespace Talkdesk\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class ChargeCommand extends AbstractCallBillCommand
{
    protected function configure()
    {
        $this->setName('chargeCall')
            ->setDescription('Charges a call and remove credits from a given account')
            ->addArgument('call_duration', InputArgument::REQUIRED, 'Duration of the call')
            ->addArgument('account_name', InputArgument::REQUIRED, 'Account name')
            ->addArgument('talkdesk_phone_number', InputArgument::REQUIRED, 'Talkdesk phone number')
            ->addArgument('customer_phone_number', InputArgument::REQUIRED, 'Customer phone number')
            ->addArgument('forwarded_phone_number', InputArgument::OPTIONAL, 'Forwarded phone number');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callDuration = $input->getArgument('call_duration');
        $accountName = $input->getArgument('account_name');
        $talkdeskPhonenumber = $input->getArgument('talkdesk_phone_number');
        $customerPhoneNumber = $input->getArgument('customer_phone_number');
        $forwardedPhoneNumber = $input->getArgument('forwarded_phone_number');

        if ($this->addCall(
            $callDuration, $accountName, $talkdeskPhonenumber, $customerPhoneNumber, $forwardedPhoneNumber
        )
        ) {
            $output->writeln('Call billed with success');
            return static::EXIT_SUCCESS;
        }


        if ($this->error) {
            $output->writeln($this->error);
            return static::EXIT_UNSUCCESS;
        }

        foreach ($this->getCallModel()->getMessages() as $message) {
            $output->writeln($message);
        }
        return static::EXIT_UNSUCCESS;

    }

    /**
     * @param $callDuration
     * @param $accountName
     * @param $talkdeskPhonenumber
     * @param $customerPhoneNumber
     * @param $forwardedPhoneNumber
     * @return bool
     */
    protected function addCall(
        $callDuration, $accountName, $talkdeskPhonenumber, $customerPhoneNumber, $forwardedPhoneNumber
    )
    {
        $callModel = $this->getCallModel();
        if ($callModel) {
            return $callModel->add(
                $callDuration, $accountName, $talkdeskPhonenumber, $customerPhoneNumber, $forwardedPhoneNumber
            );
        }
        return false;
    }
}