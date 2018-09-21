<?php

/**
 * TodoList ToDo List
 *
 * @author Marcos Lopez
 * 
 * Login Model
 * 
 * Responsible for authenticate users and add new ones
 */
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

        public $useTable = 'users';
        public $primaryKey = 'id';
        public $validate = array(
                'login' => array(
                        'user' => array(
                                'required' => array(
                                        'rule' => 'notBlank',
                                        'message' => 'A username is required'
                                )
                        ),
                        'password' => array(
                                'required' => array(
                                        'rule' => 'notBlank',
                                        'message' => 'A password is required'
                                )
                        )
                ),
                'signUp' => array(
                        'user' => array(
                                'required' => array(
                                        'rule' => 'notBlank',
                                        'message' => 'A username is required'
                                )
                        ),
                        'password' => array(
                                'required' => array(
                                        'rule' => 'notBlank',
                                        'message' => 'A password is required'
                                )
                        ),
                        'name' => array(
                                'required' => array(
                                        'rule' => 'alphaNumeric',
                                        'message' => 'The name can only be alphanumeric characters'
                                )
                        )
                )
        );

        /**
         * Hash the password before saving into database
         * 
         * @param type $options
         * 
         * @return boolean
         */
        public function beforeSave($options = array()) {
                if (isset($this->data[$this->alias]['password'])) {
                        $passwordHasher = new BlowfishPasswordHasher();
                        $this->data[$this->alias]['password'] = $passwordHasher->hash(
                                $this->data[$this->alias]['password']
                        );
                }
                return true;
        }

}
