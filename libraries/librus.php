<?php 
//ver 0.2

$host='localhost';
$bd='blog';
$name='bloger';
$psw='27720915';

function sql_fix ($conn, $var) //экранирование данных из зпроса sql
{
	if (get_magic_quotes_gpc()) $var=stripslashes($var);
	$var = strip_tags($var);
	return $conn->real_escape_string($var);
}

function sql_fix_all ($conn, $var) //экранирование данных из запроса html
{
	return htmlspecialchars(sql_fix($conn, $var));
}

function sql_fatal_error($msg)//вывод подробного сообщения об ошибке
{
	$msg2=mysql_error();
	echo <<<_END
	<p>К сожалению, завершить запрашиваемую задачу не представляется возможным.</p>
	<p>Было получено следующее сообщение об ошибке:</p>
	<p>$msg: $msg2</p>
	<p>Пожалуйста, нажмите кнопку возврата вашего браузера и повторите попытку. Если проблемы не прекратятся, пожалуйста, <a href="mailto:admin@php-sql.net">сообщите о них нашему администратору</a>. Спасибо!</p>
_END;
}

function text_formatting($text)
{
	$text=str_replace('\r\n','</p><p>',$text);
	$text=str_replace('\&quot;','"',$text);
	return $text;
}

function sql_query()//запрос с использованием указателей;
{
	$arg = func_get_args();
	$conn = $arg[0];
	$query = $arg[1];
	$query = $conn->prepare($query);
	
	if (!$query) 
	{
		echo sql_fatal_error($conn->error);
		return FALSE;
	}
	
	switch (count($arg)) {
		case 3: $query->bind_param('s',$arg[2]);
		break;
		case 4: $query->bind_param('ss',$arg[2],$arg[3]);
		break;
		case 5: $query->bind_param('sss',$arg[2],$arg[3],$arg[4]);
		break;
		case 6: $query->bind_param('ssss',$arg[2],$arg[3],$arg[4],$arg[5]);
		break;
		case 7: $query->bind_param('sssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6]);
		break;
		case 8: $query->bind_param('ssssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],$arg[7]);
		break;
		case 9: $query->bind_param('sssssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],$arg[7],$arg[8]);
		break;
		case 10: $query->bind_param('ssssssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],$arg[7],$arg[8],$arg[9]);
		break;
		case 11: $query->bind_param('sssssssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],$arg[7],$arg[8],$arg[9],$arg[10]);
		break;
		case 12: $query->bind_param('ssssssssss',$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],$arg[7],$arg[8],$arg[9],$arg[10],$arg[11]);
		break;
	}

	$query->execute();
	$result = $query->get_result();
	
	return $result;
}

function dateRus ($date) {
	$mass = date_parse_from_format('Y-m-d H:i:s',$date);
	
	switch ($mass['month']) {
		case 1: $month="января";
		break;
		case 2: $month="февраля";
		break;
		case 3: $month="марта";
		break;
		case 4: $month="апреля";
		break;
		case 5: $month="мая";
		break;
		case 6: $month="июня";
		break;
		case 7: $month="июля";
		break;
		case 8: $month="августа";
		break;
		case 9: $month="сентября";
		break;
		case 10: $month="октября";
		break;
		case 11: $month="ноября";
		break;
		case 12: $month="декабря";
		break;
	}
	
	return $mass['day']." ".$month." ".$mass['year'];
}
?>