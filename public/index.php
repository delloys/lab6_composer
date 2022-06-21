<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
echo dirname(__DIR__);

require_once dirname(__DIR__) . '/vendor/autoload.php';
$logsPath = "/var/www/html/composer/log/data.log";
$loader = new FilesystemLoader(dirname(__DIR__) . "/template/");
$log = new Logger('log');
$loggerHandler = new StreamHandler($logsPath, Logger::INFO);
$log->pushHandler($loggerHandler);
$twig = new Environment($loader);
echo $twig->render("main.html.twig");

$users = [
    "admin" =>"admin",
    "guest"=>"123"
];

if (isset($_GET['logs'])) {
    echo("Логи: ");
    $file = file_get_contents("/var/www/html/composer/log/data.log");
    $Nfile = "\n$file";
    $ArrFile = array($Nfile);
    echo '<pre>';
    print_r($ArrFile);
    echo '</pre>';
}

function add_msg($login, $message){
    if ($message !== '') {
        $info = json_decode(file_get_contents('data.json'));
        $newMessage = (object)['date' => date('d.m.y h:i:s'), 'user' => $login, 'message' => $message];
        $info[] = $newMessage;
        file_put_contents('data.json', json_encode($info));
    }
}

function print_msgs(){
    $info = json_decode(file_get_contents("messages.json"),false);
    foreach ($info->messages as $mes){
        echo '<p font-weight: bold">' . $mes->date . ' | ' . $mes->user . ' say:';
        echo '<p style="padding-left: 125px">' . $mes->message;
    }
}

if ((string)$_GET['login'] !== '' && isset($_GET['login']) && isset($_GET['password']) && isset($_GET['message'])) {
    if ($users[(string)$_GET['login']] == (string)$_GET['password']) {
        add_msg((string)$_GET['login'],(string)$_GET['message']);
        $log->info('user send message',['user' => $_GET['login'], 'send' => $_GET['message']]);
    }
    else {
        echo "<script> alert(\"Неверный пароль\") </script>";
        $log->error('wrong password');
    }
}

print_msgs();
?>

