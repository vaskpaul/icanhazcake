<?php

/**
 * ToDo List
 *
 * @author Marcos Lopez
 */
class TaskListMobileController extends AppController {

        public $components = array('RequestHandler');

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                $this->loadModel('TaskList');
        }

        /**
         * Function to be called before run an action
         * Check if we are on a mobile device
         * 
         * @return redirect to non mobile controller
         */
        public function beforeFilter() {
                /* if (!$this->RequestHandler->isMobile()) {

                  $fileName = $this->request->pass[0];

                  return $this->redirect(
                  array(
                  'controller' => 'TaskList',
                  'action' => 'index',
                  $fileName
                  ));
                  } */
        }

        /**
         * 
         */
        public function index() {
                $fileName = $this->request->pass[0];

                $columns = $this->columnsConfig();
                $rows = $this->TaskList->loadXml($fileName, true);

                $json = json_encode(array_merge($columns, $rows));

                $this->set('title', $rows["projectTitle"]);
                $this->set('lastMod', $rows["projectLastMod"]);
                $this->set('fileName', json_encode($fileName, true));
                $this->set('rows', json_encode($json, true));

                $this->layout = 'mobile';
        }

        /**
         * 
         */
        public function saveXml() {
                $this->autoRender = false;

                $fileName = $this->request->data('fileName');
                $data = json_decode($this->request->data('tasks'), true);

                return $this->TaskList->saveXml($data, $fileName, true);
        }

        /**
         * Table columns with responsive breakpoints 
         * 
         * @return array
         */
        private function columnsConfig() {

                $columns = array(
                        'columns' => array(
                                array(
                                        'name' => 'id',
                                        'title' => "id",
                                        'breakpoints' => 'xs sm lg',
                                        'visible' => false
                                ),
                                array(
                                        'name' => 'route',
                                        'title' => "route",
                                        'breakpoints' => 'xs sm lg',
                                        'visible' => false
                                ),
                                array(
                                        'name' => 'check',
                                        'title' => '',
                                ),
                                array(
                                        'name' => 'color',
                                        'title' => '',
                                        'visible' => false
                                ),
                                array(
                                        'name' => 'title',
                                        'title' => 'Title',
                                ),
                                array(
                                        'name' => 'priority',
                                        'title' => 'Priority',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'percentage',
                                        'title' => '%',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'date',
                                        'title' => 'Due Date',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'recurrence',
                                        'title' => 'Recurrence',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'status',
                                        'title' => 'Status',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'category',
                                        'title' => 'Category',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'tags',
                                        'title' => 'Tags',
                                        'breakpoints' => 'xs sm lg',
                                ),
                                array(
                                        'name' => 'subtask',
                                        'title' => 'Subtask',
                                        'breakpoints' => 'xs sm lg',
                                )
                        )
                );

                return $columns;
        }

        private function parseRow() {
                $row = array(
                        'value' => array(
                                "id" => array(
                                        'options' => array('classes' => 'id'),
                                        'value' => $task["@ID"],
                                ),
                                'check' => array(
                                        'options' => array('classes' => 'row-checkbox'),
                                        'value' => '<input type=checkbox>',
                                ),
                                "title" => array(
                                        'options' => array('classes' => 'title'),
                                        'value' => $task["@TITLE"],
                                ),
                                "priority" => $task["@PRIORITY"],
                                "percentage" => array(
                                        'options' => array('classes' => 'percentage'),
                                        'value' => $task["@PERCENTDONE"],
                                ),
                                "date" => (isset($task["@DUEDATESTRING"])) ? $task["@DUEDATESTRING"] : "",
                                "recurrence" => (isset($task["RECURRENCE"]["@"])) ? $task["RECURRENCE"]["@"] : "",
                                "status" => array(
                                        'options' => array('classes' => 'status'),
                                        'value' => (isset($task["@STATUS"])) ? $task["@STATUS"] : "",
                                ),
                                "category" => array(
                                        'options' => array('classes' => 'categories'),
                                        'value' => (isset($task["CATEGORY"])) ? $task["CATEGORY"] : "",
                                ),
                                "tags" => array(
                                        'options' => array('classes' => 'tags'),
                                        'value' => (isset($task["TAG"])) ? $task["TAG"] : "",
                                ),
                                'subtask' => '<button class=subtask>Subtask</button>',
                                'color' => array(
                                        'options' => array('classes' => 'color'),
                                        'value' => isset($task["@WEBCOLOR"]) ? $task["@WEBCOLOR"] : '#000',
                                ),
                                'route' => array(
                                        'options' => array('classes' => 'route'),
                                        'value' => $route
                                ),
                        ),
                        'options' => array(
                                'classes' => $class
                        )
                );
        }

}
