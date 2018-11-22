<?php
session_start();

if (isset($_SESSION['login']))
{
header('Content-Type: text/html; charset=utf-8');

//Подключаем библиотеки
require_once 'libraries/librus.php';

//Массив для передаче шаблону страницы
$var = array(
	"template" => "page",
);

//Подключаемся к базе данных
$conn = new mysqli($host, $name, $psw, $bd);
if ($conn->connect_error) sql_fatal_error($conn->connect_error);

//Обработчик формы
if (isset($_GET['postID']))
{
	if (empty($_GET['postID'])) header('Location: http://dao.su/index.php');
	else
	{
	$login = sql_fix_all($conn,$_SESSION['login']);
	$postID = sql_fix_all($conn,$_GET['postID']);
	$results=sql_query($conn,"SELECT * FROM blog WHERE postID=?",$postID);
	$rows = $results->num_rows;

		for ($i=0;$i<$rows;++$i)
		{
			$results->data_seek($i);
			$row=$results->fetch_array(MYSQLI_ASSOC);
			$var["postID"][$i] = $row["postID"];
			$var["head"][$i] = $row["head"];
			$var["previewImg"][$i] = $row["previewImg"];
			$var["text"][$i] = text_formatting($row["text"]);
			$var["date"][$i] = dateRus($row["date"]);
			$var["lkCount"][$i] = $row["lkCount"];
		}
		$last_of_i=$i-1; //для лайков (ниже), передаюм к lkCount
		
		//Обработчик lk (надо бы на ajax переделать)
		if (!empty($_POST['like']))
		{
			$login = sql_fix_all($conn,$_SESSION['login']);
			sql_query($conn,"INSERT INTO likes VALUES (?,?,1,NOW())",$login,$postID);
			
			$results=sql_query($conn,"SELECT COUNT(login) FROM likes WHERE postID=?",$postID);
			$rows = $results->num_rows;
			for ($i=0;$i<$rows;++$i)
				{
					$results->data_seek($i);
					$row=$results->fetch_array(MYSQLI_NUM);
					$lkCount=$row[0];
				}
			
			sql_query($conn,"UPDATE blog SET lkCount=? WHERE postID=?",$lkCount,$postID);
			$var["lkCount"][$last_of_i] = $lkCount;
		}
		
		//Обработчик формы комментария
		if (!empty($_POST['comText']))
		{
			$comText = sql_fix_all($conn,$_POST['comText']);
			sql_query($conn,"INSERT INTO comments VALUES (?,?,?,NOW())",$postID,$login,$comText);
		}
		
		$results=sql_query($conn,"SELECT com.*, us.login, us.avatar FROM comments AS com LEFT JOIN users AS us ON us.login=com.login WHERE postID=?",$postID);
		$rows = $results->num_rows;

		for ($i=0;$i<$rows;++$i)
		{
			$results->data_seek($i);
			$row=$results->fetch_array(MYSQLI_ASSOC);
			$com["login"][$i] = $row["login"];
			$com["postID"][$i] = $row["postID"];
			$com["comText"][$i] = text_formatting($row["comText"]);
			$com["comDate"][$i] = dateRus($row["comDate"]);
			$com["avatar"][$i] = $row["avatar"];
		}
		$com["count"] = $i;
	}
}
else header('Location: http://dao.su/index.php');

include "templates/template.html";

$conn->close();
}
else header('Location: http://dao.su/auth.php');
?>