<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
class TaskList {

        private $xmlArray;
        private $title;
        private $lastMod;
        private $tasks;
        private $status;
        private $categories;
        private $tags;

        /**
         * Initialize the task list with data from an XML file
         * 
         * @param type $xml
         */
        public function __construct($xml) {

                $this->xmlArray = $xml;

                $this->title = $xml["TODOLIST"]["@PROJECTNAME"];
                $this->lastMod = $xml["TODOLIST"]["@LASTMODSTRING"];
                $this->categories = $this->elements($xml["TODOLIST"], "CATEGORY");
                $this->tags = $this->elements($xml["TODOLIST"], "TAG");
                $this->status = $this->elements($xml["TODOLIST"], "STATUS");

                if (isset($xml["TODOLIST"]["TASK"]) || key_exists("TASK", $xml["TODOLIST"]["TASK"])) {

                        $this->tasks = $this->fillTasks($xml["TODOLIST"]["TASK"]);
                }
        }

        /**
         * 
         * 
         * @param type $tasks
         * @param array $toFill
         * 
         * @return type
         */
        private function fillTasks($tasks, array $toFill = array()) {

                if (!isset($tasks["TASK"][0])) {
                        //Put the task info inside an array 
                        //so foreach doesn't give errors
                        $tasks["TASK"] = array(
                                0 => $$tasks["TASK"]
                        );
                }

                foreach ($tasks["TASK"] as $task) {

                        if (!is_array(($task))) //Empty task <task />
                                continue;

                        $toFill[] = $this->taskData($task);

                        if (isset($task["TASK"]) || key_exists("TASK", $task)) {

                                $last = count($toFill) - 1;

                                $toFill[$last]["TASK"] = array();
                                $toFill[$last]["TASK"] = $this->fillTasks($task, $toFill[$last]["TASK"]);
                        }
                }

                return $toFill;
        }

        /**
         * 
         * 
         * @param type $xml
         * @param type $tagName
         * 
         * @return type
         */
        private function elements($xml, $tagName) {

                if (array_key_exists($tagName, $xml) && count($xml[$tagName]) > 1) {
                        $elements = array();
                        foreach ($xml[$tagName] as $element) {
                                $elements[] = $element;
                        }
                        return $elements;
                } else if (array_key_exists($tagName, $xml) && count($xml[$tagName]) == 1) {
                        return array($xml[$tagName]);
                }

                return array(); //if not exists, return an empty array
        }

        /**
         * Look for a specific task by recursion
         * 
         * @param type $id
         * 
         * @return Object|boolean Object Task() on success, false on fail
         */
        private function findTask($id) {
                foreach ($this->tasks as $task) {
                        if ($id == $task->id) {
                                return $task;
                        }
                }

                return false;
        }

        /**
         * 
         */
        public function listToJSON() {

                $array = array(
                        "data" => array(
                                "tasks" => array(),
                                "categories" => $this->elements($xmlArray["TODOLIST"], "CATEGORY"),
                                "tags" => $this->elements($xmlArray["TODOLIST"], "TAG"),
                                "status" => $this->elements($xmlArray["TODOLIST"], "STATUS")
                        )
                );
        }

        private function add() {
                
        }

        private function delete() {
                
        }

        private function change() {
                
        }

}