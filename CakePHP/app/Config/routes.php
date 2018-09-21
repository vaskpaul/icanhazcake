<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */

Router::parseExtensions('xml');

Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

Router::connect('/', array('controller' => 'Users', 'action' => 'index'));
Router::connect('/users', array('controller' => 'Users', 'action' => 'index'));

Router::connect("/login", array(
        'controller' => 'Users',
        'action' => 'login'
));

Router::connect("/logout", array(
        'controller' => 'Users',
        'action' => 'logout'
));

Router::connect("/signup", array(
        'controller' => 'Users',
        'action' => 'signUp'
));

Router::connect('/list', array('controller' => 'UsersLists', 'action' => 'index'));

Router::connect('/list/parsexml', array(
        'controller' => 'UsersLists',
        'action' => 'parseXml'
));


Router::connect('/list/create', array('controller' => 'FileLists', 'action' => 'createTaskList')
);

Router::connect('/list/delete/:id', array('controller' => 'UsersLists', 'action' => 'deleteList'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/list/share', array(
        'controller' => 'UsersLists',
        'action' => 'shareTaskList'
));

Router::connect('/list/export/:id', array(
        'controller' => 'UsersLists',
        'action' => 'exportList'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);



Router::connect('/tasklist', array(
        "controller" => "UserLists",
        "action" => "index"
        )
);

Router::connect('/tasklist/:id', array('controller' => 'TaskList', 'action' => 'index'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/tasklist/:id/title', array('controller' => 'TaskList', 'action' => 'changeTitle'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/tasklist/:id/add', array('controller' => 'TaskList', 'action' => 'AddTask'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/tasklist/:id/savetasks', array('controller' => 'TaskList', 'action' => 'saveTasks'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);


Router::connect('/tasklist/:id/save/:setting', array('controller' => 'TaskList', 'action' => 'saveSetting'), array(
        'pass' => array('id', 'setting'),
        'id' => "\d+",
        'setting' => "\w+"
        )
);

Router::connect('/tasklist/:id/update/:setting', array('controller' => 'TaskList', 'action' => 'updateSetting'), array(
        'pass' => array('id', 'setting'),
        'id' => "\d+",
        'setting' => "\w+"
        )
);

Router::connect('/tasklist/:id/delete/:setting', array('controller' => 'TaskList', 'action' => 'deleteSetting'), array(
        'pass' => array('id', 'setting'),
        'id' => "\d+",
        'setting' => "\w+"
        )
);

Router::connect('/tasklist/:id/filter', array('controller' => 'TaskList', 'action' => 'saveFilter'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/mobile/:id', array('controller' => 'TaskList', 'action' => 'mobile'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/mobile/:id/loadrows', array('controller' => 'TaskList', 'action' => 'loadMobileRows'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/mobile/:id/add', array('controller' => 'TaskList', 'action' => 'addTaskMobile'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);

Router::connect('/mobile/:id/savetasks', array('controller' => 'TaskList', 'action' => 'saveTasks'), array(
        'pass' => array('id'),
        'id' => "\d+"
        )
);




/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
