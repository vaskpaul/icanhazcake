<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 * 
 * TaskList Model
 * 
 * Parse xml into database 
 * Export database into xml
 */
App::uses('AppModel', 'Model');

class TaskList extends AppModel {

        public $useTable = 'tasklists';
        public $hasMany = array(
                'UsersLists',
                'Task' => array(
                        'order' => array(
                                'Task.id' => 'asc',
                        )),
                'TagList',
                'CategoryList',
                'StatusList'
        );

        /**
         * Generates a new tasklist and associates tasks in the database
         * 
         * @param type $filePath
         */
        public function xmlListToDatabase($filePath, $user_id) {

                $xml = Xml::toArray(Xml::build($filePath));

                $new_tasklist = array(
                        'title' => $xml["TODOLIST"]["@PROJECTNAME"],
                        'last_mod' => isset($xml["TODOLIST"]["@LASTMODSTRING"]) ? $this->dateToSqlFormat($xml["TODOLIST"]["@LASTMODSTRING"]) : "NOW()",
                        'creation_date' => isset($xml["TODOLIST"]["@CREATIONDATESTRING"]) ? $xml["TODOLIST"]["@CREATIONDATESTRING"] : "CURRENT_TIMESTAMP",
                        'owner_id' => $user_id
                );

                $this->create();
                $this->save($new_tasklist);

                $settings = array(
                        'StatusList' => isset($xml["TODOLIST"]["STATUS"])? $xml["TODOLIST"]["STATUS"]: array(),
                        'CategoryList' => isset($xml["TODOLIST"]["CATEGORY"])? $xml["TODOLIST"]["CATEGORY"]: array(),
                        'TagList' => isset($xml["TODOLIST"]["TAG"])? $xml["TODOLIST"]["TAG"]: array()
                );

                foreach ($settings as $index => $setting) {
                        foreach ($setting as $element) {

                                if (!empty($element)) {

                                        $this->{$index}->create();
                                        $this->{$index}->save(
                                                array(
                                                        'name' => $element,
                                                        'tasklist_id' => $this->getLastInsertId()
                                                )
                                        );
                                }
                        }
                }

                if (isset($xml["TODOLIST"]["TASK"]) && key_exists("TASK", $xml["TODOLIST"])) {
                        $this->xmlTasksToDatabase($xml["TODOLIST"]);
                }
        }

        /**
         * @Funcionality: takes tasks from xml array 
         * 
         * @param type $xml
         * @param type $toFill
         * 
         * @return array with all the tasks
         */
        private function xmlTasksToDatabase($xml, $task_lvl = 0, $parent_id = null) {

                if (!isset($xml["TASK"][0])) {
                        //Put the task info inside an array 
                        //so foreach doesn't give errors
                        $xml["TASK"] = array(
                                0 => $xml["TASK"]
                        );
                }

                foreach ($xml["TASK"] as $task) {
                        if (!is_array(($task))) //Empty task <task />
                                continue;

                        $status = $this->generateSetting($task, "@STATUS");
                        $categories = $this->generateSetting($task, "CATEGORY");
                        $tags = $this->generateSetting($task, "TAG");

                        $this->Task->create();
                        $this->Task->save(array(
                                'title' => $task["@TITLE"],
                                'color' => isset($task["@WEBCOLOR"]) ? $task["@WEBCOLOR"] : '#000',
                                'priority' => ((int) $task["@PRIORITY"] / 2) == 0 ? 1 : $task["@PRIORITY"] / 2,
                                'percentage' => isset($task["@PERCENTDONE"]) ? (int) $task["@PERCENTDONE"] : 0,
                                'due_date' => isset($task["@DUEDATESTRING"]) ? $this->dateToSqlFormat($task["@DUEDATESTRING"]) : NULL,
                                'status' => $status,
                                'category' => $categories,
                                'tag' => $tags,
                                'creation_date' => isset($task["@CREATIONDATESTRING"]) ? $this->dateToSqlFormat($task["@CREATIONDATESTRING"]) : "CURRENT_TIMESTAMP",
                                'last_mod_date' => isset($task["@LASTMODSTRING"]) ? $this->dateToSqlFormat($task["@LASTMODSTRING"]) : "CURRENT_TIMESTAMP",
                                'task_lvl' => $task_lvl,
                                'comment' => isset($task['COMMENTS']) ? $task["COMMENTS"] : "",
                                'link' => isset($task['FILEREFPATH']) ? (is_array($task["FILEREFPATH"])? $task["FILEREFPATH"][0]: $task["FILEREFPATH"] ): "",
                                'tasklist_id' => $this->getLastInsertId(),
                                'parent_task_id' => $parent_id
                        ));

                        if (isset($task["TASK"])) {
                                $this->xmlTasksToDatabase($task, $task_lvl + 1, $this->Task->getLastInsertId());
                        }
                }
        }

        /**
         * Format setting (status/categories/tags) into string for database saving
         * 
         * @param array $task
         * @param String $key status | category | tag
         * 
         * @return String
         */
        private function generateSetting($task, $key) {

                if (isset($task[$key]) && array_key_exists($key, $task)) {
                        return is_array($task[$key]) ? implode(",", $task[$key]) : $task[$key];
                }

                return "";
        }

        /**
         * Calculates the number of days between the given date and 1-1-1900
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

        /**
         * Save operation over tasklist' settings (statuses | categories | tags)
         * 
         * @param String $tasklist_id
         * @param String $verb action, can be save or deleteAll
         * @param String $setting_name status | category | tag
         * @param array $data setting name
         */
        public function saveSetting($tasklist_id, $setting_name, $data) {

                $this->{$setting_name}->create();
                $this->{$setting_name}->save(
                        array(
                                'name' => $data['name'],
                                'tasklist_id' => $tasklist_id
                        )
                );
        }

        /**
         * Update operation over tasklist' settings (statuses | categories | tags)
         * 
         * @param String $tasklist_id
         * @param String $verb action, can be save or deleteAll
         * @param String $setting_name status | category | tag
         * @param array $data setting name
         */
        public function updateSetting($tasklist_id, $setting_name, $data) {

                $setting = ucfirst($setting_name) . "List";

                $name = $data['name'];
                $old = $data['old_name'];

                $this->{$setting}->updateAll(
                        array(
                        'name' => "'$name'"
                        ), array(
                        'name' => "$old",
                        'tasklist_id' => (int) $tasklist_id
                        )
                );

                $tasks = $this->Task->find("all", array(
                        'conditions' => array(
                                'tasklist_id' => (int) $tasklist_id,
                                "$setting_name LIKE" => "%" . $old . "%"
                        )
                ));

                foreach ($tasks as $key) {

                        $task = $key["Task"];

                        $task_settings = explode(",", $task[$setting_name]);
                        $task_settings = array_map(function($a) {
                                return trim($a);
                        }, $task_settings);

                        if (($k = array_search($old, $task_settings)) !== false) {
                                $task_settings[$k] = $name;
                        }

                        $task_settings = implode(",", $task_settings);

                        $this->Task->id = $task["id"];
                        $this->Task->saveField($setting_name, $task_settings);
                }
        }

        /**
         * Delete a setting register (status|category|tag) from database and 
         * every tasks containing it
         * 
         * @param type $tasklist_id
         * @param string $setting_name status|category|tag
         * @param array $data
         */
        public function deleteSetting($tasklist_id, $setting_name, $data) {

                $setting = ucfirst($setting_name) . "List";
                $name = $data["name"];

                $this->{$setting}->deleteAll(
                        array(
                        $setting . '.name' => $name,
                        $setting . '.tasklist_id' => $tasklist_id
                        ), false);

                //delete setting from all the tasks

                $tasks = $this->Task->find("all", array(
                        'conditions' => array(
                                'tasklist_id' => (int) $tasklist_id,
                                "$setting_name LIKE" => "%" . $name . "%"
                        )
                ));

                foreach ($tasks as $key) {

                        $task = $key["Task"];

                        $task_settings = explode(",", $task[$setting_name]);

                        if (($k = array_search($name, $task_settings)) !== false) {
                                unset($task_settings[$k]);
                        }

                        $task_settings = implode(",", $task_settings);

                        $this->Task->id = $task["id"];
                        $this->Task->saveField($setting_name, $task_settings);
                }
        }

}
