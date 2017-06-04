<?php
/**
 * @author Luís Pimenta <lfpimenta@gmail.com>
 * ECall
 * Date: 24-03-2016
 *
 */
namespace Talkdesk\Entities;


class ECall extends AbstractEntity
{
    protected static $duration = 0;
    protected static $cost = 0;
    protected static $balance = 0;
    protected static $fkAccount = 0;
    protected static $talkdeskPhoneNumber = 0;
    protected static $customerPhoneNumber = 0;
    protected static $forwardedPhoneNumber = 0;
    protected static $callType = '';
    protected static $data;
}
