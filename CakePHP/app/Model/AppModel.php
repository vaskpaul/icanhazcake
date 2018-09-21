<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

        /**
         * Check if a key exists in an array
         * 
         * @param Array $array
         * @param String|integer $key
         * 
         * @return boolean true if the key already exists
         */
        public function checkKey($array, $key) {

                return isset($array[$key]) || array_key_exists($key, $array);
        }

        /**
         * Transform a dd/mm/yyyy into a yyyy-mm-dd (MySQL) date
         * 
         * @return String formated date
         */
        public function dateToSqlFormat($date) {
                $date = str_replace("/", "-", $date);
                $datetime = new DateTime($date);
                return $datetime->format('Y-m-d');
        }
        
        /**
         * Format a MySQL date into dd/mm/yyyy date format
         * 
         * @param string $sqlDate
         * 
         * @return String formatted date
         */
        public function dateToTableFormat($sqlDate){
                $date = str_replace("-", "/", $sqlDate);
                $datetime = new DateTime($date);
                return $datetime->format('d/m/Y');
        }

}
