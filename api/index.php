 <?php
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', dirname(__FILE__));

    // load configuration
    require_once(ROOT . DS . 'config' . DS . 'config.php');
    require_once(ROOT . DS . 'Database.php');

    function autoload($className)
    {
        if (file_exists(ROOT . DS . 'routes' . DS . $className . '.php')) {
            require_once(ROOT . DS . 'routes' . DS . $className . '.php');
        }
    }

    spl_autoload_register('autoload');

    $url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : [];
    $url_params_tmp = isset($_SERVER['REQUEST_URI']) ? explode('?', ltrim($_SERVER['REQUEST_URI'])) : [];
    $url_params_tmp2 = isset($url_params_tmp[1]) ? explode('&', ltrim($url_params_tmp[1])) : [];
    
    $url_params = [];
    foreach ($url_params_tmp2 as $param) {
        $tmp = explode('=',$param);
        if (count($tmp) == 2) {
            $url_params[$tmp[0]] = urldecode($tmp[1]);
        }
    }
    

    $params = [];
    if (count($url) > 1) {
        $params = array_slice($url, 1);
    }

    $controller = null;
    $controller_name = ucfirst($url[0]);

    if (class_exists($controller_name)) {
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        $db = Database::getInstance();
        $controller = new $controller_name($db);
        //echo "Method: " . $_SERVER["REQUEST_METHOD"];
        //echo "<br>";
        switch ($_SERVER["REQUEST_METHOD"]) {
            case 'GET':
                $controller->get($params[0], $url_params);
                break;

            case 'POST':
                $controller->post($params, $url_params);
                break;
            case 'PUT':
                $controller->put($params[0], $url_params);
            default:
                # code...
                break;
        }
    } else {
        http_response_code(404);
        die("404");
    }


    ?>