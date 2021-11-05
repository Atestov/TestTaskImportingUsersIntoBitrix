<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Импорт пользователей");
?>

<form enctype="multipart/form-data" action="/data-import.php" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    Отправить файл: <input name="import" type="file" />
    <input type="submit" value="Отправить файл" />
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>