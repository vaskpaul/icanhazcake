<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class UsersListsController extends AppController {

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

                $f = new File($file['urls'][0], true, 0644);
                $f->delete();

                return $this->redirect('/tasklist/' . $id);
        }

        /**
         * Parse database ToDoList into .xml file
         */
        public function exportList($tasklist_id) {

                $result = $this->UsersLists->parseToXml($tasklist_id);

                $this->set('xmlString', $result["xml"]);
                $this->set('filename', $result["filename"]);

                $this->autoRender = false;

                //read database content
                //fill an array with it
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
         * Remove a tasklist from database
         * 
         * @param int|string $tasklist_id
         */
        public function deleteList($tasklist_id) {

                $this->loadModel("TaskList");

                if ($this->request->is('post')) {



                        $pass = $this->UsersLists->User->find("first", array(
                                'conditions' => array(
                                        'User.id' => $this->Session->read("Auth.User.id"),
                                        'User.user' => $this->Session->read("Auth.User.user")
                                ),
                                'fields' => array(
                                        'User.password'
                                )
                                )
                        );

                        Security::setHash('blowfish');
                        $hashed_pass = Security::hash($this->request->data("pass"), 'blowfish', $pass['User']['password']);


                        if ($hashed_pass === $pass['User']['password']) {
                                
                                $exists = $this->TaskList->hasAny(
                                        array(
                                                'id' => $tasklist_id,
                                                'owner_id' => $this->Session->read("Auth.User.id")
                                        )
                                );

                                if (!$exists) {
                                        throw new ForbiddenException("No permissions to delete this tasklist");
                                }

                                //DELETE ON CASCADE

                                $this->TaskList->delete($tasklist_id);
                        } else {
                                throw new ForbiddenException("No permissions to delete this tasklist");
                        }
                }

                $this->autoRender = false;
        }

        /**
         * Create neccesary rows 
         * 
         * @param int|string $tasklist_id
         * @param array $toShareWith
         */
        public function shareTaskList() {

                if ($this->request->is("post")) {

                        $users = json_decode($this->request->data("users_lists"), true);

                        foreach ($users as $user) {

                                $user_id = $this->UsersLists->User->find("first", array(
                                        'conditions' => array(
                                                'user' => $user["user"]
                                        ),
                                        'fields' => array(
                                                'User.id'
                                        )
                                ));

                                $exists = $this->UsersLists->hasAny(
                                        array(
                                                'tasklist_id' => $this->request->data("tasklist_id"),
                                                'user_id' => $user_id["User"]["id"]
                                        )
                                );

                                if ($exists) {
                                        echo "UPDATE";
                                        $this->UsersLists->updateAll(array(
                                                'UsersLists.permissions' => $user["permissions"],
                                                ), array(
                                                'UsersLists.user_id' => $user_id["User"]["id"],
                                                'UsersLists.tasklist_id' => $this->request->data("tasklist_id"),
                                        ));
                                } else {
                                        echo "SAVE";
                                        $this->UsersLists->create();
                                        $this->UsersLists->save(array(
                                                'user_id' => $user_id["User"]["id"],
                                                'tasklist_id' => $this->request->data("tasklist_id"),
                                                'permissions' => $user["permissions"],
                                                'owner_id' => $this->Session->read("Auth.User.id")
                                        ));
                                }
                        }
                } else {
                        die();
                }

                $this->autoRender = false;
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

        public function generateXMLFile() {
                
        }

}
