<?php

/**
 * TodoList ToDo List
 *
 * @author Marcos Lopez
 * 
 * Users Controller
 */
App::uses('AppController', 'Controller');

class UsersController extends AppController {

        public $components = array(
                'Session',
                'Flash',
                'Auth' => array(
                        'loginRedirect' => array(
                                'controller' => 'list',
                                'action' => 'index'
                        ),
                        'logoutRedirect' => array(
                                'controller' => 'Users',
                                'action' => "index"
                        ),
                        'authenticate' => array(
                                'Form' => array(
                                        'passwordHasher' => 'Blowfish',
                                        "fields" => array('username' => 'user', 'password' => 'password')
                                )
                        )
                )
        );

        public function beforeFilter() {
                $this->Auth->allow();
        }

        /**
         * Render login form
         */
        public function index() {
                if ($this->Session->check("Auth.User")) {
                        return $this->redirect("/list");
                }
        }

        public function view($id = null) {
                $this->User->id = $id;
                if (!$this->User->exists()) {
                        throw new NotFoundException(__('Invalid user'));
                }
                $this->set('user', $this->User->findById($id));
        }

        /**
         * Try to login the user
         * 
         * @return type
         */
        public function login() {
                if ($this->request->is('post')) {
                        if ($this->Auth->login()) {
                                return $this->redirect($this->Auth->redirectUrl());
                        }
                        $this->Flash->error(__('Invalid username or password, try again'));
                }

                $this->render('index');
        }

        /**
         * Logout the user
         * 
         * @return type
         */
        public function logout() {
                return $this->redirect($this->Auth->logout());
        }

        /**
         * Add a new user into the database
         * 
         * @return type
         */
        public function signUp() {
                if ($this->request->is('post')) {

                        $this->User->create();

                        if ($this->User->save($this->request->data['signUp'])) {
                                $this->Flash->set(__("Your user has been created"),array(
                                        'element' => 'success'
                                ));
                                return $this->redirect(array('action' => 'index'));
                        }
                        $this->Flash->set(
                                __('The user could not be saved. Please, try again.',
                                        array(
                                                'element' => 'error.ctp'
                                        ))
                        );
                }

                $this->autoRender = false;
        }
        
        /**
         * Check in the database if an user already exists
         * 
         * @return boolean true on found user
         */
        public function checkUser(){
                
                $user = $this->request->data["user"];
                
                $result = $this->User->find("first",array(
                        'conditions' => array(
                                'user' => $user
                        )
                ));
                
                $result = array_filter($result);
                
                echo !empty($result) ? true : false;
                
                $this->autoRender = false;
                
                
        }

}
