<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 * 
 * FileLists Model
 * 
 * 
 */
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Xml', 'Utility');

class UsersLists extends AppModel {

        public $useTable = "users_lists";
        public $belongsTo = array(
                'TaskList' => array(
                        'className' => 'TaskList',
                        'foreignKey' => 'tasklist_id'
                ),
                'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'owner_id'
                )
        );

        /**
         * Get all the tasklist user has access and divide them into owned and shared with him
         * 
         * @param integer $user_id
         * 
         * @return array 
         */
        public function getList($user_id) {

                $lists = $this->find("all", array(
                        'conditions' => array(
                                'user_id' => (int) $user_id
                        )
                        )
                );

                $entries = array(
                        'owner' => array(),
                        'sharedWithYou' => array()
                );

                foreach ($lists as $list) {

                        if ($list["UsersLists"]['owner_id'] === $user_id) {
                                $entries['owner'][] = array(
                                        'id' => $list["TaskList"]["id"],
                                        'title' => '<a href="/tasklist/' . $list["TaskList"]["id"] . '">' . $list["TaskList"]["title"] . '</a>',
                                        'actions' => "<button class='delete btn btn-danger btn-sm '>Delete</button>" .
                                        " <button class='share btn btn-warning btn-sm'>Share</button>" .
                                        " <a class='export btn btn-success btn-sm' href='/UsersLists/exportList/" . $list["TaskList"]["id"] . "' target='blank'>Export</a>"
                                );
                        } else {

                                if ($list["UsersLists"]['permissions'] == 0) {
                                        $permissions = "Read";
                                } elseif ($list["UsersLists"]['permissions'] == 1) {
                                        $permissions = "Read & Write";
                                }

                                $entries['sharedWithYou'][] = array(
                                        'title' => '<a href="/tasklist/' . $list["TaskList"]["id"] . '">' . $list["TaskList"]["title"] . '</a>',
                                        'sharedBy' => $list['User']['user'],
                                        'permissions' => $permissions,
                                        'export' => "<a class='export btn btn-success btn-sm' href='/UsersLists/exportList/" . $list["TaskList"]["id"] . "' target='blank'>Export</a>"
                                );
                        }
                }

                return $entries;
        }

        /**
         * Read an upload xml file and transforms it into database syntax/content
         * 
         */
        public function parseXml($fileName, $user_id) {

                $fileName = str_replace("//", "/", $fileName);
                $filePath = str_replace("\\", "/", WWW_ROOT . $fileName);

                //Create records on Tasklist and Task tables

                $this->TaskList->xmlListToDatabase($filePath, $user_id);

                //Create record on Users_Lists table

                $this->create();
                $this->save(
                        array(
                                'tasklist_id' => $this->TaskList->getLastInsertId(),
                                'user_id' => $user_id,
                                'filter_status' => 'null',
                                'filter_categories' => 'null',
                                'filter_tags' => 'null',
                                'filter_complete' => 0,
                                'permissions' => 2,
                                'owner_id' => $user_id
                        )
                );

                return $this->TaskList->getLastInsertId();
        }

        /**
         * Parse database content into Abstract Spoon ToDoList format
         * 
         * @param String|int $tasklist_id
         */
        public function parseToXml($tasklist_id) {

                $list = $this->TaskList->find("first", array(
                        'conditions' => array(
                                'id' => $tasklist_id,
                        )
                ));

                $no_completed = $this->TaskList->Task->find('all', array(
                        'conditions' => array(
                                'tasklist_id' => $tasklist_id,
                                'percentage <' => '100'
                        )
                ));

                $completed = $this->TaskList->Task->find('all', array(
                        'conditions' => array(
                                'tasklist_id' => $tasklist_id,
                                'percentage ' => '100'
                        )
                ));

                $tasks = array_map(function($value) {
                        return $value['Task'];
                }, array_merge($no_completed, $completed));

                $filename = 'todo_' . strtolower(str_replace(" ", "", $list["TaskList"]['title'])) . '.xml';

                $xml = array(
                        'TODOLIST' => array(
                                '@PROJECTNAME' => $list["TaskList"]['title'],
                                '@LASTMOD' => $this->getDaysInterval($list["TaskList"]['last_mod']),
                                '@LASTMODSTRING' => $list["TaskList"]['last_mod'],
                                '@CREATIONDATE' => $this->getDaysInterval($list["TaskList"]['creation_date']),
                                '@CREATIONDATESTRING' => $list["TaskList"]['creation_date'],
                                '@FILENAME' => $filename,
                                'STATUS' => Hash::extract($list["StatusList"], '{n}.name'),
                                'CATEGORY' => Hash::extract($list["CategoryList"], '{n}.name'),
                                'TAG' => Hash::extract($list["TagList"], '{n}.name')
                        )
                );


                if (count($list['Task']) > 0) {

                        $xml["TODOLIST"]["TASK"] = array();

                        foreach ($tasks as $task) {

                                //parse task into xml syntax

                                $xml_task = array(
                                        "@TITLE" => $task["title"],
                                        "@ID" => $task['id'],
                                        "@COMMENTSTYPE" => 'PLAIN_TEXT',
                                        "@PRIORITY" => $task['priority'],
                                        "@PERCENTDONE" => $task['percentage'],
                                        "@STATUS" => $task['status'],
                                        "@DUEDATE" => $this->getDaysInterval($task['due_date']),
                                        "@DUEDATESTRING" => $task['due_date'],
                                        "@CREATIONDATE" => $this->getDaysInterval($task['creation_date']),
                                        "@CREATIONDATESTRING" => $task['creation_date'],
                                        "@LASTMOD" => $this->getDaysInterval($task['last_mod_date']),
                                        "@LASTMODSTRING" => $task['last_mod_date'],
                                        "@TEXTWEBCOLOR" => $task['color'],
                                        "CATEGORY" => explode(",", $task["category"]),
                                        "TAG" => explode(",", $task["tag"]),
                                        "COMMENTS" => $task["comment"],
                                        "FILEREFPATH" => $task["link"],
                                );

                                //create route for inserting

                                if (!is_null($task['parent_task_id'])) { //subtasks
                                        $path = "TODOLIST.TASK";

                                        for ($i = 0; $i < $task["task_lvl"] - 1; $i++) {
                                                $path.= '.{n}.TASK';
                                        }

                                        $path.= '.{n}[@ID=' . $task['parent_task_id'] . ']';

                                        $k = count(Hash::extract($xml, $path . '.TASK'));
                                        $a = count(Hash::extract($xml, $path . '.TASK')) ? Hash::extract($xml, $path . '.TASK') : array();
                                        $a[$k] = $xml_task;

                                        $xml = Hash::insert($xml, $path, array('TASK' => $a));
                                } else {
                                        $x = count($xml["TODOLIST"]["TASK"]);
                                        $xml = Hash::insert($xml, "TODOLIST.TASK." . $x, $xml_task);
                                }
                        }
                }
                $xmlObject = Xml::fromArray(array_filter($xml));
                $xmlString = $xmlObject->asXML();

                header('Content-type: text/xml');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                print $xmlString;
        }

        /**
         * INSERT a new tasklist register into database
         * 
         * @param type $data
         */
        public function createList($data, $user_id) {

                $this->TaskList->create();
                $this->TaskList->save(array(
                        'title' => $data["title"],
                        'owner_id' => $user_id
                ));

                $this->create();
                $this->save(
                        array(
                                'tasklist_id' => $this->TaskList->getLastInsertId(),
                                'user_id' => $user_id,
                                'filter_status' => "",
                                'filter_categories' => "",
                                'filter_tags' => "",
                                'filter_complete' => 0,
                                'permissions' => 2,
                                'owner_id' => $user_id
                        )
                );

                return $this->TaskList->getLastInsertId();
        }

        /**
         * Calculates the number of days between the given date and 1-1-1900
         * for AbstractSpoon ToDoList uses
         * 
         * @param String $stringDate A date in d/m/Y format
         * 
         * @return int number of days between the two dates
         */
        private function getDaysInterval($stringDate) {

                $d = str_replace('/', '-', $stringDate);
                $date = new DateTime(date("Y-m-d", strtotime($d)));
                $aux = new DateTime('1900-01-01');
                $interval = $aux->diff($date);

                return $interval->format('%R%a') + 2;
        }

}
