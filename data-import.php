<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обработка импорта данных");

$file = fopen($_FILES['import']["tmp_name"], 'r') or die("не удалось открыть файл");
#Получение списка полей
$Fields = explode(",", trim(fgets($file)));

#Получение данных
$Users = array();
while(!feof($file))
{
	#Создаем ассоциативный массив. 
	#	Компания	=> НПП Промавтоматика
	#	Email		=> suleymanov-promav@nowmedia.ru
	#	Поле		=> Значение
	$Users[] =  array_combine($Fields, explode(",", trim(fgets($file))));
}
fclose($file);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");