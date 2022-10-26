<?php

require_once 'config/config.php';

class Telegram
{
    private const API_DOMAIN = 'https://api.telegram.org/';

    public static function sendFile($chatId, $file)
    {
        $ch = curl_init();

        $getParams = http_build_query([
            'chat_id' => $chatId
        ]);

        curl_setopt($ch, CURLOPT_URL, self::API_DOMAIN . 'bot' . TOKEN . DIRECTORY_SEPARATOR . 'sendDocument' . '?' . $getParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $fileInfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        $curlFile = new CURLFile($file, $fileInfo);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "document" => $curlFile
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    public static function sendMessage($chatId, $message)
    {
        $getParams = http_build_query([
            'chat_id' => $chatId,
            'parse_mode' => 'Markdown',
            'text' => $message
        ]);

        self::simpleRequest('bot' . TOKEN . DIRECTORY_SEPARATOR . 'sendMessage' . '?' . $getParams);
    }

    public static function simpleRequest($url)
    {
        return file_get_contents(self::API_DOMAIN . $url);
    }

    public static function getFileDataById($fileId)
    {
        $getParams = http_build_query([
            'file_id' => $fileId
        ]);

        $data = self::simpleRequest('bot' . TOKEN . DIRECTORY_SEPARATOR . 'getFile' . '?' . $getParams);

        $data = json_decode($data, true);

        $filePath = $data['result']['file_path'];

        return self::simpleRequest('file' . DIRECTORY_SEPARATOR . 'bot' . TOKEN . DIRECTORY_SEPARATOR . $filePath);
    }
}
