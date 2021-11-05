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

// выбираем всех пользователей
$AllUsers = CUser::GetList(
	($by="id"), 
	($order="desc"), 
	array(), 
	array('FIELDS' => array("ID", "LOGIN"))
); 

// выбираем пользователей которых нельзя трогать
$rsExclusiongUsers = CUser::GetList(
	($by="id"), 
	($order="desc"), 
	array("GROUPS_ID" => array(1,)),
	array('FIELDS' => array("LOGIN"))
);

$ExclusiongUsers = array();
while ($user = $rsExclusiongUsers->Fetch()) {
	$ExclusiongUsers[] = $user["LOGIN"];
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");