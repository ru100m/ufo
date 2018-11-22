<?php
session_start();

if (isset($_SESSION['login']))
{
	header('Content-Type: text/html; charset=utf-8');

	//Подключаем библиотеки
	require_once 'libraries/librus.php';

	$uploads_dir = "doc/images";

	//Массив для передаче шаблону страницы
	$var = array(
		"template" => "main",
	);

	//Подключаемся к базе данных
	$conn = new mysqli($host, $name, $psw, $bd);
	if ($conn->connect_error) sql_fatal_error($conn->connect_error);

	//Обработчик формы (add post)
	if (isset($_POST['head']) && isset($_POST['preview']) && isset($_POST['text']))
	{
		if (!empty($_POST['head']) && !empty($_POST['preview']) && !empty($_POST['text']))
		{
			$login=sql_fix_all($conn, $_SESSION['login']);
			$head=sql_fix_all($conn,$_POST['head']);
			$preview=sql_fix_all($conn,$_POST['preview']);
			$text=sql_fix_all($conn,$_POST['text']);
			$nameImg = NULL;
			
			if (is_uploaded_file($_FILES['filename']['tmp_name']))
			{
				$nameImg = $_FILES['filename']['name'];
				move_uploaded_file($_FILES['filename']['tmp_name'], "$uploads_dir/$nameImg");
				$nameImg = "$uploads_dir/$nameImg";
			}
			
			$result=sql_query($conn, "INSERT INTO blog VALUES (NULL,?,?,?,?,?,NOW(),0,0)", $login, $head, $preview, $nameImg, $text);
		}
		header('Location: http://dao.su/index.php');
	}

	//Подготовка к выводу данных из БД
	$results=sql_query($conn,"SELECT * FROM blog WHERE date < NOW() + INTERVAL 1 SECOND ORDER BY postID DESC");
	$rows = $results->num_rows;

	for ($i=0;$i<$rows;++$i)
	{
		$results->data_seek($i);
		$row=$results->fetch_array(MYSQLI_ASSOC);
		$var["postID"][$i] = $row["postID"];
		$var["head"][$i] = $row["head"];
		$var["previewImg"][$i] = $row["previewImg"];
		$var["preview"][$i] = $row["preview"];
		$var["date"][$i] = dateRus($row["date"]);
	}
	
	$results=sql_query($conn,"SELECT name FROM tags");
	$rows = $results->num_rows;

	for ($i=0;$i<$rows;++$i)
	{
		$results->data_seek($i);
		$row=$results->fetch_array(MYSQLI_ASSOC);
		$var["tags"][$i]=$row["name"];
	}

	include "templates/template.html";

	$conn->close();
}
else header('Location: http://dao.su/auth.php');
?>