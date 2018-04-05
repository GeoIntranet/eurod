<?php

class Genius_Model_Traceur
{
    public static function track($ip,$module,$search,$value=null)
    {

        $date = new DateTime();

        $fill=
            [
                'ip' => $ip,
                'module' => $module,
                'value' => $value,
                'search' => $search,
                'created_at' => $date->format('Y-m-d H:i:s') ,
            ];
        try {
            Genius_Model_Global::insert('ec_searching', $fill);
        } catch (Zend_Exception $e) {
            var_dump($e);
        }


    }

}