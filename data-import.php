<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("импорт данных");

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
$rsRegisterUsers = CUser::GetList(
	($by="id"), 
	($order="desc"), 
	array(), 
	array('FIELDS' => array("ID", "LOGIN"))
);

$RegisterUsers = array();
while ($user = $rsRegisterUsers->Fetch()) {
	$RegisterUsers[$user["LOGIN"]] = array(
		"ID" => $user["ID"],
		"LOGIN" => $user["LOGIN"]);
}
$listRegisterUsers = array_keys($RegisterUsers); 

// выбираем пользователей которых нельзя трогать
$rsExclusiongUsers = CUser::GetList(
	($by="id"), 
	($order="desc"), 
	array("GROUPS_ID" => array(1,)),
	array('FIELDS' => array("LOGIN"))
); #Группа 1 => Администраторы

while ($user = $rsExclusiongUsers->Fetch()) {
	# и убираем их из списка 
	unset($RegisterUsers[$user["LOGIN"]]);
}

$newUserPasswords = "";
#Добавляем пользователей на сайт
foreach ($Users as $user) {
	#Если пользователь есть на сайте пропускаем его.
	#array_combine возвращает false если количество полей и значений не совпадает, тоже пропускаем.
	if (in_array($user["Email"], $listRegisterUsers) || $user == false) {
		#Убираем из списка всех пользователей, что были на сайте. 
		#После выполнения цикла foreach в $RegisterUsers останутся только пользователи которых нужно деактивировать.
		unset($RegisterUsers[$user["Email"]]);
		continue;
	};
	$createUser = new CUser;
	$password = random_int(100000, 999999); #В качестве пароля зададим число
	$arFields = Array(
		"EMAIL"             => $user["Email"],
		"LOGIN"             => $user["Email"],
		"ACTIVE"            => "Y",
		"GROUP_ID"          => array(6),
		"PASSWORD"          => $password,
		"CONFIRM_PASSWORD"  => $password,
		"WORK_COMPANY"      => $user["Компания"]
	); #Группа 6 => Зарегистрированные пользователи
	$ID = $createUser->Add($arFields);
	$newUserPasswords .= $user["Компания"]." ".$user["Email"] ." ".$password."<br>";
}

#Выводим пароли для новых учетных записей.
echo $newUserPasswords ? $newUserPasswords : "Все пользователи уже есть на сайте";

#Деактивируем учетные записи пользователей
foreach ($RegisterUsers as $rec) {
	$user = new CUser;
	$user->Update($rec["ID"], Array("ACTIVE" => "F"));
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");