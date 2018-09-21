<?php

class Task {

        private $id;
        private $title;
        private $percentage;
        private $priority;
        private $categories;
        private $status;
        private $tags;
        private $subtasks;
        private $subtask_done;
        private $dueDate = array("dueDate","dueDateString");
        private $lastMod = array("lastMod","lastModString");
        private $creationDate = array("creationDate","creationDateString");
        private $colour;

        public function __construct($task) {

                $this->id = $task["@ID"];
                $this->title = $task["@TITLE"];
                $this->priority = $task["@PRIORITY"];
                $this->percentage = $task["@PERCENTDONE"];
                $this->dueDate = (isset($task["@DUEDATESTRING"])) ? $task["@DUEDATESTRING"] : "";
                $this->categories = (isset($task["CATEGORY"])) ? $task["CATEGORY"] : "";
                $this->status = (isset($task["@STATUS"])) ? $task["@STATUS"] : "";
                $this->tags = (isset($task["TAG"])) ? $task["TAG"] : "";

                $this->childs($task);
        }

        public function childs($task) {
                if (isset($task["TASK"]) || array_key_exists("TASK", $task)) {

                        //May be an array of arrays(multiple childs) 

                        if (isset($task["TASK"][0]) || array_key_exists(0, $task["TASK"])) {

                                foreach ($task["TASK"] as $subtask) {
                                        $this->subtasks[] = new Task();
                                }
                        } else {
                                $this->subtasks[] = new Task();
                        }
                }
        }

        public function xmlValues() {
                return array(
                        "@ID" => $this->id,
                        "@TITLE" => $this->title,
                        "@PRIORITY" => $this->priority,
                        "@PERCENTDONE" => $this->percentage,
                );
        }

        /**
         * set function
         * 
         * @param type $name
         * 
         * @param type $value
         */
        public function __set($name, $value) {
                $this->{$name} = $value;
        }

        /**
         * get function
         * 
         * @param string $name
         * 
         * @return value of asked attribute
         */
        public function __get($name) {
                return $this->$name;
        }

        /**
         * 
         * @param string $parent
         * @param type $task
         */
        public static function subtaskDone($parent, $task) {




                if ($parent["TASK"][$task["@ID"]]["@PERCENTDONE"] == 100) {
                        $sub = explode("/", $parent["@SUBTASKDONE"]);
                        $parent["@SUBTASKDONE"] = ($sub[0] + 1) . "/" . $sub[2];
                }
        }

        
        /**
         * Modify class atributes with new ones
         * 
         * @param type $newData
         */
        public function edit($new){

                unset($new["subtask"]);
                unset($new["class"]);

                $today = new DateTime();
                $todayString = $today->format("d/m/Y");
                $days = $this->getDaysInterval($todayString);

                $this->title = $new["@TITLE"];
                $this->priority = $new["@PRIORITY"];
                $this->percentage = $new["@PERCENTDONE"];
                $this->dates["lastmod"] = $days;
                //$original["@DUEDATESTRING"] = $new["@DUEDATESTRING"];
                //$this-> = $new["@RECURRENCE"];
                $this->status = $new["@STATUS"];

                $new["CATEGORY"] = explode(",", $new["CATEGORY"]);
                $new["TAG"] = explode(",", $new["TAG"]);

                if ($new["@PERCENTDONE"] == 100) {
                        $new["@DONEDATE"] = $days;
                        $new["@DONEDATESTRING"] = $todayString;
                }
                $new["@LASTMOD"] = $days;
                $new["@LASTMODSTRING"] = $todayString;
                $new["@DUEDATE"] = $this->getDaysInterval($new["@DUEDATESTRING"]);

                return array_replace($original, $new);
                
        }
}
