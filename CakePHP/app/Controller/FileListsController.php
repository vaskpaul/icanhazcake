<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
class FileListsController extends AppController {

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                $this->loadModel('FileLists');
        }

        /**
         * Generate view with information about files founded in files folder
         */
        public function index() {

                $list = $this->FileLists->listOfFiles();
                $defaultSettings = $this->FileLists->readDefaultSettings();

                $this->set("files", json_encode($list, true));
                $this->set('status', json_encode($defaultSettings['status']), true);
                $this->set('categories', json_encode($defaultSettings['categories']), true);
                $this->set('tags', json_encode($defaultSettings['tags']), true);

                $this->layout = 'list';
        }

        /**
         * Generate a new TaskList in the server
         */
        public function createTaskList() {

                $fileName = $this->request->data('fileName');

                if (strpos($fileName, '.xml')) {
                        if (!preg_match("(^\w+\.xml$)", $fileName)) {
                                header("HTTP/1.0 400 Bad Request");
                                die();
                        }
                } else {
                        //check there are not irregular characters
                        if (strpos($fileName, '.')) {
                                header("HTTP/1.0 400 Bad Request");
                                die();
                        } else {
                                $fileName .= '.xml';
                        }
                }
                
                if ($this->FileLists->checkFileExistence($fileName)) {
                        header("HTTP/1.0 409 File already exists");
                        die();
                }

                $projectName = $this->request->data('listTitle');

                $this->FileLists->createTaskList($fileName, $projectName);

                $this->autoRender = false;
        }

        /**
         * Remove an existing file in the server
         */
        public function deleteFile() {

                $fileName = $this->request->data('fileName');

                $this->FileLists->deleteFile($fileName);

                $this->autoRender = false;
        }

        /**
         * 
         */
        public function modifyDefaultSettings() {

                $settings = json_decode($this->request->data('settings'), true);

                $this->FileLists->modifyDefaultSettings($settings);

                $this->autoRender = false;
        }

}
