<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 * 
 * TaskList Controller
 * 
 * Manages aspects related with lists and the tasks belonging to them
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
                
        }

        /**
         * Load all the tasklist and tasks information for view
         * 
         */
        public function index($tasklistId) {

                $permissions = $this->checkPermissions($tasklistId);
                if ($this->RequestHandler->isMobile()) {
                        return $this->redirect("/mobile/" . $tasklistId);
                }

                if ((int) $permissions !== 0) {

                        $tasklist = $this->TaskList->find("first", array(
                                'conditions' => array(
                                        'id' => $tasklistId,
                                ),
                                'order' => array(
                                        'id' => 'asc'
                                )
                        ));

                        $no_completed = $this->TaskList->Task->find('all', array(
                                'conditions' => array(
                                        'tasklist_id' => $tasklistId,
                                        'percentage <' => '100'
                                )
                        ));

                        $completed = $this->TaskList->Task->find('all', array(
                                'conditions' => array(
                                        'tasklist_id' => $tasklistId,
                                        'percentage ' => '100'
                                )
                        ));

                        $tasks = array_merge($no_completed, $completed);

                        $tasks = array_map(function($value) {

                                if (!is_null($value['Task']['due_date'])) {

                                        $date = str_replace("-", "/", $value['Task']["due_date"]);
                                        $datetime = new DateTime($date);
                                        $value['Task']['due_date'] = $datetime->format('d/m/Y');
                                }

                                return $value['Task'];
                        }, $tasks);

                        $status = Hash::extract($tasklist["StatusList"], '{n}.name');
                        $categories = Hash::extract($tasklist["CategoryList"], '{n}.name');
                        $tags = Hash::extract($tasklist["TagList"], '{n}.name');

                        $filter_status = $this->generateFilterList($status, $tasklist['UsersLists'][0]['filter_status']);
                        $filter_categories = $this->generateFilterList($categories, $tasklist['UsersLists'][0]['filter_categories']);
                        $filter_tags = $this->generateFilterList($tags, $tasklist['UsersLists'][0]['filter_tags']);

                        $this->set('title_for_layout', "TD: " . $tasklist["TaskList"]['title']);
                        $this->set('ListTitle', $tasklist["TaskList"]['title']);
                        $this->set('lastMod', $tasklist["TaskList"]["last_mod"]);
                        $this->set('tasks', json_encode($tasks, true));
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
                } else {
                        $this->redirect("/tasklist/read/" . $tasklistId);
                }
        }

        /**
         * 
         * @param type $tasklistId
         * @throws UnauthorizedException
         */
        public function mobile($tasklistId) {

                $permissions = $this->checkPermissions($tasklistId);

                if ((int) $permissions !== 0) {

                        $tasklist = $this->TaskList->find("first", array(
                                'conditions' => array(
                                        'id' => $tasklistId,
                                ),
                        ));

                        if (!$this->checkPermissions($tasklistId)) {
                                throw new UnauthorizedException("Denied permission");
                        }

                        $status = Hash::extract($tasklist["StatusList"], '{n}.name');
                        $categories = Hash::extract($tasklist["CategoryList"], '{n}.name');
                        $tags = Hash::extract($tasklist["TagList"], '{n}.name');

                        $this->set('title_for_layout', "TD: " . $tasklist["TaskList"]['title']);
                        $this->set('ListTitle', $tasklist["TaskList"]['title']);
                        $this->set('status', json_encode($status, true));
                        $this->set('categories', json_encode($categories, true));
                        $this->set('tags', json_encode($tags, true));
                        $this->set('filters', json_encode(array(
                                'status' => Hash::filter(explode(",", $tasklist['UsersLists'][0]['filter_status'])),
                                'categories' => Hash::filter(explode(",", $tasklist['UsersLists'][0]['filter_categories'])),
                                'tag' => Hash::filter(explode(",", $tasklist['UsersLists'][0]['filter_tags'])),
                                'completed' => $tasklist['UsersLists'][0]['filter_complete'],
                                        ), true));

                        $this->layout = 'mobile';
                } else {
                        $this->redirect("/tasklist/read/" . $tasklistId);
                }
        }

        /**
         * Load views for only reading permissions
         * 
         * @param type $tasklistId
         */
        public function onlyRead($tasklistId) {

                $permissions = $this->checkPermissions($tasklistId);

                $tasklist = $this->TaskList->find("first", array(
                        'conditions' => array(
                                'id' => $tasklistId,
                        ),
                ));

                $user_prefs = Hash::extract($tasklist['UsersLists'], '{n}[user_id=' . $this->Session->read("Auth.User.id") . ']');
                $user_prefs = $user_prefs[0];

                $status = Hash::extract($tasklist["StatusList"], '{n}.name');
                $categories = Hash::extract($tasklist["CategoryList"], '{n}.name');
                $tags = Hash::extract($tasklist["TagList"], '{n}.name');

                $this->set('title_for_layout', "TD: " . $tasklist["TaskList"]['title']);
                $this->set('ListTitle', $tasklist["TaskList"]['title']);
                $this->set('status', json_encode($status, true));
                $this->set('categories', json_encode($categories, true));
                $this->set('tags', json_encode($tags, true));
                $this->set('filters', json_encode(array(
                        'status' => Hash::filter(explode(",", $user_prefs['filter_status'])),
                        'categories' => Hash::filter(explode(",", $user_prefs['filter_categories'])),
                        'tag' => Hash::filter(explode(",", $user_prefs['filter_tags'])),
                        'completed' => $user_prefs['filter_complete'],
                                ), true));

                $this->layout = 'only_read';
        }

        /**
         * Retrieves a JSON string with tasks
         * 
         * @param type $tasklistId
         */
        public function loadMobileRows($tasklistId) {

                $tasks = $this->TaskList->Task->find("all", array(
                        'conditions' => array(
                                'tasklist_id' => $tasklistId,
                        ),
                ));

                $tasks = Hash::extract($tasks, "{n}.Task");

                $tasks = array_map(function($value) {

                        if ($value['due_date'] !== NULL) {

                                $date = str_replace("-", "/", $value["due_date"]);
                                $datetime = new DateTime($date);
                                $value['due_date'] = $datetime->format('d/m/Y');
                        }
                        return $value;
                }, $tasks);


                $orderedTasks = array();

                foreach ($tasks as $task) {

                        if (!is_null($task["parent_task_id"])) {
                                for ($i = 0, $long = count($orderedTasks); $i < $long; $i++) {

                                        if ($orderedTasks[$i]['value']["id"]['value'] == $task["parent_task_id"]) {
                                                $key = $i + 1;
                                                break;
                                        }
                                }

                                array_splice($orderedTasks, (int) $key, 0, array($this->taskToMobileFormat($task)));
                        } else {

                                $orderedTasks[] = $this->taskToMobileFormat($task);
                        }
                }


                //$this->orderTasksMobile($tasks, $orderedTasks);

                echo json_encode($orderedTasks, true);

                $this->autoRender = false;
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
         * Update task rows in database
         * 
         * Also updates tasklist's last modification date 
         */
        public function saveTasks($tasklist_id) {

                $tasks = json_decode($this->request->data["tasks"], true);
                $tasks = array_map(function($value) {
                        if ($value['due_date'] !== "NULL") {

                                $date = str_replace("/", "-", $value["due_date"]);
                                $datetime = new DateTime($date);

                                $value['due_date'] = $datetime->format('Y-m-d');
                        } else {
                                $value['due_date'] = null;
                        }

                        $value['last_mod_date'] = date('Y-m-d H:i:s');

                        return $value;
                }, $tasks);

                $this->TaskList->Task->saveMany($tasks);

                //updating last mod tasklist time
                $this->TaskList->id = (int) $tasklist_id;
                $this->TaskList->saveField('last_mod', $this->request->data["lastMod"]);

                $this->autoRender = false;
        }

        /**
         * Remove tasks from database
         * 
         * @throws BadRequestException
         */
        public function deleteTasks() {
                $deletedTasks = json_decode($this->request->data["deleted"], true);

                if (empty($deletedTasks))
                        throw new BadRequestException('Nothing to delete');

                foreach ($deletedTasks as $idToDelete) {
                        $this->TaskList->Task->delete((int) $idToDelete, true);
                }

                $this->autoRender = false;
        }

        /**
         * Insert a new task into database
         * 
         * @param int|string $tasklist_id
         */
        public function addTask($tasklist_id) {

                $permissions = $this->TaskList->UsersLists->find("first", array(
                        'conditions' => array(
                                'tasklist_id' => (int) $tasklist_id,
                                'user_id' => $this->Session->read("Auth.User.id")
                        ),
                        'fields' => array(
                                'UsersLists.permissions'
                        )
                ));

                if (((int) $permissions["UsersLists"]["permissions"]) > 0) {

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
                } else {
                        throw new UnauthorizedException("You don't have enough permissions");
                }


                $this->autoRender = false;
        }

        /**
         * Add a new task to database from mobile 
         * 
         * @param int|string $tasklist_id
         */
        public function addTaskMobile($tasklist_id) {

                $data = json_decode($this->request->data["tasks"], true);
                $data[0]["tasklist_id"] = (int) $tasklist_id;

                if ($data[0]['due_date'] !== "NULL") {

                        $date = str_replace("/", "-", $value["due_date"]);
                        $datetime = new DateTime($date);

                        $data[0]['due_date'] = $datetime->format('Y-m-d');
                }


                $data = $data[0];

                $this->TaskList->Task->create();
                $this->TaskList->Task->save($data);

                $this->autoRender = false;
        }

        /**
         * Save filter parameters for posterior use
         * 
         * @param type $tasklist_id
         */
        public function saveFilter($tasklist_id) {

                $toSave = array();

                foreach ($this->request->data as $filter => $value) {
                        $toSave[$filter] = "'" . $value . "'";
                }

                $this->TaskList->UsersLists->updateAll(
                        $toSave, array(
                        'UsersLists.tasklist_id' => (int) $tasklist_id,
                        'UsersLists.user_id' => (int) $this->Session->read("Auth.User.id")
                ));

                $this->autoRender = false;
        }

        /**
         * Add a setting (status|category|tag) to database
         * 
         * @param string|id $tasklist_id
         * @param String $setting_name status|category|tag
         */
        public function saveSetting($tasklist_id, $setting_name) {

                if ($setting_name != "status" && $setting_name != "category" && $setting_name != "tag") {
                        die();
                }

                $setting = ucfirst($setting_name) . "List";
                $this->TaskList->saveSetting($tasklist_id, $setting, $this->request->data);
                $this->autoRender = false;
        }

        /**
         * Update a status, category or tag name
         * 
         * @param type $tasklist_id
         * @param type $setting_name
         */
        public function updateSetting($tasklist_id, $setting_name) {
                if ($setting_name != "status" && $setting_name != "category" && $setting_name != "tag") {
                        die();
                }

                $this->TaskList->updateSetting($tasklist_id, $setting_name, $this->request->data);

                $this->autoRender = false;
        }

        /**
         * Remove a setting (status|category|tag) from database
         */
        public function deleteSetting($tasklist_id, $setting_name) {
                $this->TaskList->deleteSetting($tasklist_id, $setting_name, $this->request->data);
                $this->autoRender = false;
        }

        /**
         * UPDATE query for tasklist title in database
         * 
         * @param int|string $tasklist_id
         */
        public function changeTitle($tasklist_id) {

                $this->TaskList->id = (int) $tasklist_id;
                $this->TaskList->saveField("title", $this->request->data["title"]);

                $this->autoRender = false;
        }

        /**
         * Footable row format 
         * 
         * @param array $task
         * 
         * @return array
         */
        private function taskToMobileFormat($task) {

                switch ($task["task_lvl"]) {
                        case 0:
                                $class = "task";
                                break;
                        case 1:
                                $class = "subtask " . $task['parent_task_id'];
                                break;
                        case 2:
                                $class = "subtask2 " . $task['parent_task_id'];
                                break;
                        case 3:
                                $class = "subtask3 " . $task['parent_task_id'];
                }

                return array(
                        'value' => array(
                                "id" => array(
                                        'options' => array('classes' => 'id'),
                                        'value' => $task["id"],
                                ),
                                'check' => array(
                                        'options' => array('classes' => 'row-checkbox'),
                                        'value' => '<input type=checkbox>',
                                ),
                                "title" => array(
                                        'options' => array('classes' => 'title'),
                                        'value' => $task["title"],
                                ),
                                "priority" => $task["priority"],
                                "percentage" => array(
                                        'options' => array('classes' => 'percentage'),
                                        'value' => $task["percentage"],
                                ),
                                "date" => $task["due_date"],
                                "status" => array(
                                        'options' => array('classes' => 'status'),
                                        'value' => $task["status"]
                                ),
                                "category" => array(
                                        'options' => array('classes' => 'categories'),
                                        'value' => $task["category"]
                                ),
                                "tags" => array(
                                        'options' => array('classes' => 'tags'),
                                        'value' => $task["tag"]
                                ),
                                'subtask' => '<button class="subtask btn btn-default">Subtask</button>',
                                'color' => array(
                                        'options' => array('classes' => 'color'),
                                        'value' => $task["color"]
                                ),
                                'comment' => $task["comment"]
                        ),
                        'options' => array(
                                'classes' => $class
                ));
        }

        /**
         * Check if a user has permissions over a list
         * 
         * @param String|id $tasklist_id
         * 
         * @return array permissions lvl
         * 
         * @throws UnauthorizedException on not found user
         */
        private function checkPermissions($tasklist_id) {

                $exists = $this->TaskList->UsersLists->hasAny(array(
                        'user_id' => $this->Session->read("Auth.User.id"),
                        'tasklist_id' => $tasklist_id
                ));

                if (!$exists) {
                        throw new UnauthorizedException("Denied permission");
                }

                $permissions = $this->TaskList->UsersLists->find("first", array(
                        'conditions' => array(
                                'tasklist_id' => (int) $tasklist_id,
                                'user_id' => $this->Session->read("Auth.User.id")
                        ),
                        'fields' => array(
                                'UsersLists.permissions'
                        )
                ));

                return $permissions['UsersLists']['permissions'];
        }

}
