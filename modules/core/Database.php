<?php


// TODO: Create promisary IDatabase class
//
class Database extends PDO // implements IDatabase
{


    public function __construct( $databaseInfo )
    {
        //if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

        /*
        $dns = $settings['database']['driver'] .
        ':host=' . $settings['database']['host'] .
        ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
        ';dbname=' . $settings['database']['schema'];
        */

        parent::__construct($databaseInfo);
    }

    public function tableExists( $name ) {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->query("SELECT 1 FROM $name LIMIT 1");
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        }
        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== false;
    }



}
