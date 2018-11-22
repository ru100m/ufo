<?php
header('Content-Type: text/html; charset=utf-8');

//Подключаем библиотеки
require_once 'libraries/librus.php';

$uploads_dir = "doc/images/users";

if (!empty($_POST['login']) && !empty($_POST['psw']))
{
	//Подключаемся к базе данных
	$conn = new mysqli($host, $name, $psw, $bd);
	if ($conn->connect_error) sql_fatal_error($conn->connect_error);
	
	$psw=hash('ripemd128', "%r6".sql_fix_all($conn,$_POST['psw'])."&9Gd");
	$login=sql_fix_all($conn, $_POST['login']);
	
	if (!empty($_POST['email'])) $email = sql_fix_all($conn, $_POST['email']);
	else $email='NULL';
	
	if (is_uploaded_file($_FILES['filename']['tmp_name']))
	{
		$nameImg = $_FILES['filename']['name'];
		move_uploaded_file($_FILES['filename']['tmp_name'], "$uploads_dir/$nameImg");
		$nameImg = "$uploads_dir/$nameImg";
	}
	else $nameImg = 'NULL';
	
	sql_query($conn,"INSERT INTO users VALUES(?,?,?,1,?,NULL)",$login, $psw, $email, $nameImg);
	
	$conn->close();
	
	header('Location: http://dao.su/index.php');
}
else include "templates/reg_template.html";

?>