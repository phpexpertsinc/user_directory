<?php
/**
* User Directory
*   Copyright � 2008 Theodore R. Smith <theodore@phpexperts.pro>
* 
* The following code is licensed under a modified BSD License.
* All of the terms and conditions of the BSD License apply with one
* exception:
*
* 1. Every one who has not been a registered student of the "PHPExperts
*    From Beginner To Pro" course (http://www.phpexperts.pro/) is forbidden
*    from modifing this code or using in an another project, either as a
*    deritvative work or stand-alone.
*
* BSD License: http://www.opensource.org/licenses/bsd-license.php
**/

// Change root directory
chdir('..');
 
// Required for queryDB()
require 'lib/MyDB.inc.php';
 
function __autoload($name)
{
	if (strpos($name, 'Controller') !== false)
	{
		require 'controllers/' . $name . '.inc.php';
	}
	else if (strpos($name, 'Manager') !== false)
	{
		require 'managers/' . $name . '.inc.php';
	}
	else
	{
		if (file_exists('lib/' . $name . '.inc.php'))
		{
			require 'lib/' . $name . '.inc.php';
		}
	}
}
 
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$view = isset($_GET['view']) ? $_GET['view'] : 'index';

$view_file = 'views/' . $view . '.tpl.php';

if (!file_exists($view_file))
{
    $view_file = 'views/404.tpl.php';
}

if ($view == 'browse')
{
    $action = 'browse';
}
else if ($view == 'search')
{
    $action = 'search';
}

// Initialize form variables
$result = $username = $password = $confirm = $firstName = $lastName = $email = '';

session_start();

if (SecurityController::isLoggedIn())
{
	$login_status = UserManager::LOGGED_IN;
}

$data = ControllerCommandFactory::execute($action);

// Extract $data to global namespace.
if (!is_null($data)) { extract($data); }
require 'views/header.tpl.php';
require $view_file;
require 'views/footer.tpl.php';