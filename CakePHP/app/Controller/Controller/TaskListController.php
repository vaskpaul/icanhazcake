<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
App::uses('Xml', 'Utility');

class TaskListController extends AppController {

        public $components = array(
                'RequestHandler',
                'Session',
                'Flash',
                'Auth' => array(
                        'loginRedirect' => array(
                                'controller' => 'Lists',
                                'action' => 'index'
                        ),
                        'logoutRedirect' => array(
                                'controller' => 'Users',
                                'action' => 'index',
                        ),
                        'authenticate' => array(
                                'Form' => array(
                                        'passwordHasher' => 'Blowfish',
                                        "fields" => array('username' => 'user', 'password' => 'password')
                                )
                        )
                )
        );
        
        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                $this->loadModel("TaskList");
        }

        /**
         * Function to be called before run an action
         * Check if we are on a mobile device
         * 
         * @return redirect to no mobile controller
         */
        public function beforeFilter() {
                if ($this->RequestHandler->isMobile()) {

                        $fileName = $this->request->pass[0];

                        return $this->redirect(array(
                                        'controller' => 'TaskListMobile',
                                        'action' => 'index',
                                        $fileName
                        ));
                }
        }

        /**
         * Load the FilesList method when action is not requested on the controller
         * 
         */
        public function index($tasklistId) {

                $tasklist = $this->TaskList->find("first", array(
                        'conditions' => array(
                                'id' => $tasklistId,
                        ),
                ));

                if ($tasklist["UsersLists"][0]["user_id"] !== $this->Session->read("Auth.User.id")) {
                        throw new UnauthorizedException("Denied permission");
                }

                $tasklist["Task"] = array_map(function($value) {
                        
                        if ($value['due_date'] !== NULL) {
                                $value['due_date'] = $this->TaskList->Task->dateToTableFormat($value["due_date"]);
                        }
                        return $value;
                }, $tasklist["Task"]);

                $status = Hash::extract($tasklist["StatusList"], '{n}.name');
                $categories = Hash::extract($tasklist["CategoryList"], '{n}.name');
                $tags = Hash::extract($tasklist["TagList"], '{n}.name');

                $filter_status = $this->generateFilterList($status, $tasklist['UsersLists'][0]['filter_status']);
                $filter_categories = $this->generateFilterList($categories, $tasklist['UsersLists'][0]['filter_categories']);
                $filter_tags = $this->generateFilterList($tags, $tasklist['UsersLists'][0]['filter_tags']);

                $this->set('title', $tasklist["TaskList"]['title']);
                $this->set('lastMod', $tasklist["TaskList"]["last_mod"]);
                $this->set('tasks', json_encode($tasklist["Task"], true));
                $this->set('status', json_encode($status, true));
                $this->set('categories', json_encode($categories, true));
                $this->set('tags', json_encode($tags, true));
                $this->set('filters', json_encode(array(
                        'status' => $filter_status,
                        'categories' => $filter_categories,
                        'tags' => $filter_tags,
                        'completed' => $tasklist['UsersLists'][0]['filter_complete'],
                                ), true));

                //Debug mode (only development)
                //$this->autoRender = false;

                $this->layout = "tasklist";
        }

        /**
         * Generate options for Bootstrap Multiselect
         * 
         * @link http://davidstutz.github.io/bootstrap-multiselect/ Documentation
         * 
         * @param array $setting array containing status/cats/tags values
         * @param array $filter
         */
        public function generateFilterList($setting, $filter) {

                $options = array();
                $filter = explode(",", $filter);

                foreach ($setting as $value) {

                        $array = array(
                                'label' => $value,
                                'title' => $value,
                                'value' => $value
                        );

                        if (in_array($value, $filter)) {
                                $array["selected"] = true;
                        }

                        $options[] = $array;
                }

                return $options;
        }

        /**
         * Update and delete rows in database
         * 
         * Also updates tasklist's last modification date 
         */
        public function saveTasks($tasklist_id) {

                $tasks = json_decode($this->request->data["tasks"], true);
                $tasks = array_map(function($value) {
                        if ($value['due_date'] !== "NULL") {
                                $value['due_date'] = $this->TaskList->Task->dateToSqlFormat($value["due_date"]);
                        } else {
                                $value['due_date'] = null;
                        }

                        return $value;
                }, $tasks);

                $this->TaskList->Task->saveMany($tasks);
                
                //updating last mod tasklist time
                $this->TaskList->id = (int)$tasklist_id;
                $this->TaskList->saveField('last_mod', $this->request->data["lastMod"]);
                        
                $this->autoRender = false;
        }

        public function deleteTasks() {
                $deletedTasks = json_decode($this->request->data["deleted"], true);
                
                if(empty($deletedTasks))
                        throw new BadRequestException('Nothing to delete');

                foreach ($deletedTasks as $idToDelete) {
                        $this->TaskList->Task->delete((int) $idToDelete, true);
                }

                $this->autoRender = false;
        }

        /**
         * 
         * @param type $tasklist_id
         */
        public function addTask($tasklist_id) {
                $this->TaskList->Task->create();

                $insert = array(
                        'tasklist_id' => (int) $tasklist_id
                );

                if ($this->request->is('post')) {
                        $insert['task_lvl'] = (int) $this->request->data('task_lvl');
                        $insert['parent_task_id'] = (int) $this->request->data('parent_id');
                }

                $this->TaskList->Task->save($insert);

                echo $this->TaskList->Task->getLastInsertId();

                $this->autoRender = false;
        }

        /**
         * 
         * @param type $tasklist_id
         */
        public function saveFilter($tasklist_id) {

                $toSave = array(
                        'user_id' => (int) $this->Session->read("Auth.User.id")
                );

                $toSave = array_merge($toSave, $this->request->data);

                $this->TaskList->UsersLists->id = (int) $tasklist_id;
                $this->TaskList->UsersLists->save($toSave);

                $this->autoRender = false;
        }

        /**
         * 
         * 
         * @param type $tasklist_id
         * @param type $verb
         * @param type $setting_name
         */
        public function saveSetting($tasklist_id, $setting_name) {

                $setting = ucfirst($setting_name) . "List";
                $this->TaskList->saveSetting($tasklist_id, $setting, $this->request->data);
                $this->autoRender = false;
        }

        /**
         * 
         */
        public function deleteSetting($tasklist_id, $setting_name) {
                $this->TaskList->deleteSetting($tasklist_id, $setting_name, $this->request->data);
                $this->autoRender = false;
        }
        
        public function changeTitle($tasklist_id){
                
                $this->TaskList->id = (int)$tasklist_id;
                $this->TaskList->saveField("title", $this->request->data["title"]);
                
                $this->autoRender = false;
        }

}
