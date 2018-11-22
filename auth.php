<?php
header('Content-Type: text/html; charset=utf-8');

//Подключаем библиотеки
require_once 'libraries/librus.php';

//Подключаемся к базе данных
$conn = new mysqli($host, $name, $psw, $bd);
if ($conn->connect_error) sql_fatal_error($conn->connect_error);

//Обработчик формы регистрации
if (!empty($_POST['login']) && !empty($_POST['psw']))
{
	$login=sql_fix_all($conn,$_POST['login']);
	$psw=hash('ripemd128', "%r6".sql_fix_all($conn,$_POST['psw'])."&9Gd");
	
	$result=sql_query($conn,"SELECT * FROM users WHERE login=? AND psw=?",$login,$psw);
	
	$conn->close();
	
	if ($result->num_rows) 
	{
		session_start();
		$_SESSION['login'] = $login;
		header('Location: http://dao.su/index.php');
	}
	else 
	{
		header('Location: http://dao.su/auth.php');
	}
}

$conn->close();

include "templates/auth_template.html";
?>