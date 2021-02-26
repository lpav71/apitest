<?php
header("Content-type: application/json; charset=utf-8");
$user_agent = $_SERVER["HTTP_USER_AGENT"];
if (strpos($user_agent, "Firefox") !== false) $browser = "Firefox";
elseif (strpos($user_agent, "Opera") !== false) $browser = "Opera";
elseif (strpos($user_agent, "Chrome") !== false) $browser = "Chrome";
elseif (strpos($user_agent, "MSIE") !== false) $browser = "Internet Explorer";
elseif (strpos($user_agent, "Safari") !== false) $browser = "Safari";
else $browser = "NoName";

// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Получаем данные из тела запроса
$formData = getFormData($method);

// Получение данных из тела запроса
function getFormData($method) {

    // GET или POST: данные возвращаем как есть
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;

    // PUT, PATCH или DELETE
    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));

    foreach($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }

    return $data;
}

// Разбираем url
$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

// Определяем роутер и url data
$router = $urls[0];
$urlData = array_slice($urls, 1);

// Подключаем файл-роутер и запускаем главную функцию
if ($router != '')
{
    include_once 'routers/' . $router . '.php';
    route($method, $urlData, $formData);
}
else
{

    if ($browser != "NoName"){
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <h1 style='font-family: Arial,serif;font-weight: bold;font-size: 50px;margin-top: 70px;' align='center'>Браузеры не поддерживаются</h1>;
        <?php
    }
    else
    {
        // Возвращаем ошибку

        header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
            'error' => 'Bad Request'
        ));
    }
}


//$.ajax({url: 'http://apitest.local/goods/98', method: 'GET', dataType: 'json', success: function(response){console.log('response:', response)}})
//Проверка в браузере

//curl -X PATCH http://apitest.local/goods/15  --data-urlencode "asd=78&qwerty=135"
//Проверка через команду curl в терминале
