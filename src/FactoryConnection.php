<?php
/**
 * Created by PhpStorm.
 * User: luispimenta
 * Date: 16/03/16
 * Time: 22:18
 */

namespace Talkdesk;

use League\Csv\Reader;
use PDO;
use SplFileObject;


class FactoryConnection
{
    protected static $dbInstance = null;
    protected static $csvInstance = [];
    protected static $configs = [];

    protected static $dbAccessData = [
        'host' => 'localhost',
        'dbName' => 'talkdesk',
        'userName' => 'root',
        'password' => ''
    ];

    protected static $file = [
        'csvCountry' => __DIR__ . "/../problems/assets/call billing/Twilio - Voice Prices.csv",
        'csvTollFree' => __DIR__ . "/../assets/Csv/tollFreeNumbers.csv",
        'csvMargin' => __DIR__ . "/../assets/Csv/tdMargin.csv"
    ];

    public static function create($type)
    {
        $options = self::getConfig();
        switch ($type) {
            case 'db':
                return self::getDBConnection($options);
            case 'csvCountry':
                return self::getCsvReader($type, $options);
            case 'csvTollFree':
                return self::getCsvReader($type, $options);
            case 'csvMargin':
                return self::getCsvReader($type, $options);
        }
        throw new \Exception('Not implemented type ' . $type);
    }

    /**
     * @author Luís Pimenta
     * @param array $options
     * @return null|DatabaseAdapter
     */
    protected static function getDBConnection($options = [])
    {
        if (self::$dbInstance === null) {

            foreach (array_keys(self::$dbAccessData) as $key) {
                $options[$key] = isset($options[$key]) ? $options[$key] : self::$dbAccessData[$key];
            }
            $options['host'] = isset($options['host']) ? $options['host'] : self::$dbAccessData['host'];

            $pdo = new PDO(
                "mysql:host={$options['host']};dbname={$options['dbName']}",
                $options['userName'],
                $options['password'],
                array(
                    PDO::ATTR_PERSISTENT => true
                )
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::$dbInstance = new DatabaseAdapter($pdo);
        }
        return self::$dbInstance;
    }

    /**
     * @author Luís Pimenta
     * @param $type
     * @param array $options
     * @return mixed
     */
    protected static function getCsvReader($type, $options = [])
    {
        $file = !isset($options[$type]) ? self::$file[$type] : APP_PATH . $options[$type];

        if (!isset(self::$csvInstance[$type])) {
            $inputCsv = Reader::createFromPath(new SplFileObject($file));
            $inputCsv->setDelimiter(',');
            self::$csvInstance[$type] = new CsvAdapter($inputCsv);
        }
        return self::$csvInstance[$type];
    }

    /**
     * @author Luís Pimenta <lfpimenta@gmail.com>
     * @return array
     */
    protected static function getConfig()
    {
        if (static::$configs === []) {
            static::$configs = parse_ini_file(APP_PATH . "/config.ini");
        }
        return static::$configs;
    }
}