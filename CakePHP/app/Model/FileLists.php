<?php

/**
 * ToDo List
 * 
 * @author Marcos Lopez
 */
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('PhpReader', 'Configure');

class FileLists extends AppModel {

        /**
         * Read the file folder and give information about all the .xml
         * files that are inside
         * 
         * @return array with files info
         */
        public function listOfFiles() {

                $folder = new Folder(WWW_ROOT . "files", true, 0755);
                $xmlFiles = $folder->find('.*\.xml');

                $files = array();

                foreach ($xmlFiles as $file) {
                        $xmlArray = Xml::toArray(Xml::build(WWW_ROOT . "files" . DS . $file));

                        $f = new File(WWW_ROOT . "files" . DS . $file, false);

                        //echo $file . " tiene " . $f->perms() . "<br>";
                        
                        if(!isset($xmlArray['TODOLIST']['@PROJECTNAME']))
                                $xmlArray['TODOLIST']['@PROJECTNAME'] = "[Title error]";

                        $files[] = array(
                                'project_name' => '<a href="/tasklist/' . $file . '">' . $xmlArray['TODOLIST']['@PROJECTNAME'] . '</a>',
                                'file_name' => array(
                                        'options' => array('classes' => 'filename'),
                                        'value' => $file
                                ),
                                'actions' => "<button class='delete btn btn-danger btn-xs'>Delete</button>"
                        );
                }

                return $files;
        }

        public function readDefaultSettings() {

                Configure::config('default', new PhpReader());
                Configure::load('default_list_settings', 'default');

                return array(
                        'status' => Configure::read('status'),
                        'categories' => Configure::read("categories"),
                        'tags' => Configure::read("tags")
                );
        }

        /**
         * Create a new .xml file and insert the default ToDoList content inside
         * 
         * @param String $fileName
         * @param String $projectName TaskList title
         *
         *  @return boolean
         */
        public function createTaskList($fileName, $projectName) {
                $file = new File(WWW_ROOT . "files" . DS . $fileName, true);
                $file->close();

                chmod(WWW_ROOT . "files" . DS . $fileName, 0664);

                //chown(WWW_ROOT . "files" . DS . $fileName, 'todoapp');

                $today = new DateTime();
                $todayString = $today->format("d/m/Y");
                $days = $this->getDaysInterval($todayString);

                $defaultSettings = $this->readDefaultSettings();

                $array = array(
                        "TODOLIST" => array(
                                "@PROJECTNAME" => $projectName,
                                "@FILEFORMAT" => 11,
                                "@LASTMOD" => $days,
                                "@LASTMODSTRING" => $todayString,
                                "@NEXTUNIQUEID" => '1',
                                "@FILEVERSION" => '1',
                                "@EARLIESTDUEDATE" => '0.00000000',
                                "STATUS" => $defaultSettings["status"],
                                "CATEGORY" => $defaultSettings["categories"],
                                "TAG" => $defaultSettings["tags"]
                        )
                );

                $xmlObject = Xml::fromArray($array);
                $xmlObject->asXML(WWW_ROOT . "files" . DS . $fileName);

                echo json_encode(array(
                        'project_name' => '<a href="/tasklist/' . $fileName . '">' . $projectName . '</a>',
                        'file_name' => array(
                                'options' => array('classes' => 'filename'),
                                'value' => $fileName
                        ),
                        'actions' => "<button class='delete btn btn-danger btn-xs'>Delete</button>"
                        ), true);
        }

        /**
         * Remove specified file from files folder
         * 
         * @param String $fileName
         * 
         * @return false if file not found
         */
        public function deleteFile($fileName) {
                $folder = new Folder(WWW_ROOT . "files", true, 0755);
                $xmlFiles = $folder->find($fileName);

                if (empty($xmlFiles))
                        return false;

                $file = new File(WWW_ROOT . "files" . DS . $xmlFiles[0], true, 0644);
                $file->delete();
        }

        /**
         * 
         * @param type $settings
         */
        public function modifyDefaultSettings($settings) {

                App::uses('PhpReader', 'Configure');
                Configure::config('default', new PhpReader());
                //Configure::load('default_list_settings', 'default');

                Configure::write('status', $settings["status"]);
                Configure::write('categories', $settings["categories"]);
                Configure::write('tags', $settings["tags"]);

                Configure::dump('default_list_settings.php', 'default');
        }

        /**
         * Check if a xml file exists in the files folder
         * 
         * @param String $fileName
         * 
         * @return type
         */
        public function checkFileExistence($fileName) {

                $folder = new Folder(WWW_ROOT . "files", true, 0755);
                $xmlFiles = $folder->find($fileName);

                return !empty($xmlFiles);
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

}
