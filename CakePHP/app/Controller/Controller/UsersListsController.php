<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
App::uses('AppController', 'Controller');

class UsersListsController extends AppController {

        public $components = array(
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
                $this->loadModel("UsersLists");
        }

        /**
         * Generate view with information about files founded in files folder
         */
        public function index() {

                $lists = $this->UsersLists->getList($this->Session->read("Auth.User.id"));

                $this->set('owned_lists', json_encode($lists["owner"], true));
                $this->set('shared_lists', json_encode($lists["sharedWithYou"], true));
        }

        /**
         * Translates an .xml into app syntax/content
         */
        public function parseXml() {

                $file = $this->uploadFiles("files" . DS, $this->request->data['Document']);

                $id = $this->UsersLists->parseXml($file['urls'][0], $this->Session->read("Auth.User.id"));

                return $this->redirect('/tasklist/' . $id);
        }

        /**
         * Generate a new TaskList in the server
         */
        public function createList() {
                
                $data = $this->request->data["TaskList"];
                
                $id = $this->UsersLists->createList($data, $this->Session->read("Auth.User.id"));
                
                return $this->redirect('/tasklist/' . $id);
                
        }

        /**
         * uploads files to the server
         * @params:
         * 		$folder 	= the folder to upload the files e.g. 'img/files'
         * 		$formdata 	= the array containing the form files
         * 		$itemId 	= id of the item (optional) will create a new sub folder
         * @return:
         * 		will return an array with the success of each file upload
         */
        private function uploadFiles($folder, $formdata, $itemId = null) {

                // setup dir names absolute and relative
                $folder_url = WWW_ROOT . $folder;
                $rel_url = $folder;

                // create the folder if it does not exist
                if (!is_dir($folder_url)) {
                        mkdir($folder_url);
                }

                // if itemId is set create an item folder
                if ($itemId) {
                        // set new absolute folder
                        $folder_url = WWW_ROOT . $folder . '/' . $itemId;
                        // set new relative folder
                        $rel_url = $folder . '/' . $itemId;
                        // create directory
                        if (!is_dir($folder_url)) {
                                mkdir($folder_url);
                        }
                }

                // list of permitted file types, this is only images but documents can be added
                $permitted = array('text/xml');

                // loop through and deal with the files
                foreach ($formdata as $file) {
                        // replace spaces with underscores
                        $filename = str_replace(' ', '_', $file['name']);
                        // assume filetype is false
                        $typeOK = false;
                        // check filetype is ok
                        foreach ($permitted as $type) {
                                if ($type == $file['type']) {
                                        $typeOK = true;
                                        break;
                                }
                        }

                        // if file type ok upload the file
                        if ($typeOK) {
                                // switch based on error code
                                switch ($file['error']) {
                                        case 0:
                                                // check filename already exists
                                                if (!file_exists($folder_url . '/' . $filename)) {
                                                        // create full filename
                                                        $full_url = $folder_url . '/' . $filename;
                                                        $url = $rel_url . '/' . $filename;
                                                        // upload the file
                                                        $success = move_uploaded_file($file['tmp_name'], $url);
                                                } else {
                                                        // create unique filename and upload file
                                                        ini_set('date.timezone', 'Europe/London');
                                                        $now = date('Y-m-d-His');
                                                        $full_url = $folder_url . '/' . $now . $filename;
                                                        $url = $rel_url . '/' . $now . $filename;
                                                        $success = move_uploaded_file($file['tmp_name'], $url);
                                                }
                                                // if upload was successful
                                                if ($success) {
                                                        // save the url of the file
                                                        $result['urls'][] = $url;
                                                } else {
                                                        $result['errors'][] = "Error uploaded $filename. Please try again.";
                                                }
                                                break;
                                        case 3:
                                                // an error occured
                                                $result['errors'][] = "Error uploading $filename. Please try again.";
                                                break;
                                        default:
                                                // an error occured
                                                $result['errors'][] = "System error uploading $filename. Contact webmaster.";
                                                break;
                                }
                        } elseif ($file['error'] == 4) {
                                // no file was selected for upload
                                $result['nofiles'][] = "No file Selected";
                        } else {
                                // unacceptable file type
                                $result['errors'][] = "$filename cannot be uploaded. Acceptable file types: xml.";
                        }
                }
                return $result;
        }

}
