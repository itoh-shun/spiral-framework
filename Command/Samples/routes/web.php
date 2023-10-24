<?php
echo '
<?php
require_once (defined(\'BASE_PATH\') ? BASE_PATH : "") . "framework/autoload_static.php";
require_once "'.$projectName.'/autoload_static.php";

use framework\Routing\Router;
use '.$projectName.'\App\Http\Controllers\Web\WelcomeController;

/** */

/** components */

//param _method="" を指定すると GET PUT DELETE GET PATCH を区別できる

const VIEW_FILE_ROOT = "'.$projectName.'/resources";

/** sample */

Router::map("GET", "/", [WelcomeController:: class , "index"]);

//Router::map("GET", "/:userId", [HogeHogeController:: class , "show"]);
//Router::map("POST", "/user", [HogeHogeController:: class , "create"]);
//Router::map("PATCH", "/:userId", [HogeHogeController:: class , "update"]);
//Router::map("DELETE", "/", [HogeHogeController:: class , "delete"]);


$router = new Router();
//$router->middleware();毎回必ずチェックする場合はこっち
$app = new '.$projectName.'\\'.$projectName.'Application();
$exceptionHandler = new '.$projectName.'\App\Exceptions\ExceptionHandler();
$kernel = new \framework\Http\Kernel($app, $router ,$exceptionHandler);
$request = new \framework\Http\Request();

$kernel->handle($request);

';