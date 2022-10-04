<?php

require_once 'config/config.php';

class Telegram
{
    public static function sendFile($userId, $file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot". TOKEN ."/sendDocument?chat_id=" . $userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        $cFile = new CURLFile($file, $finfo);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "document" => $cFile
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    public static function sendMessage($userId, $message)
    {
        file_get_contents("https://api.telegram.org/bot". TOKEN ."/sendMessage?chat_id=" . $userId . '&parse_mode=Markdown&text=' . $message);
    }
}
