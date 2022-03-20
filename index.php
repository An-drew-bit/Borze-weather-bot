<?php

require_once 'vendor/autoload.php';

use Telegram\Bot\Api;

$token = '?';
$weather_token = '?';
$weather_url = "https://api.openweathermap.org/data/2.5/weather?appid={$weather_token}&units=metric&lang=ru";

$telegram = new Api($token);

$update = $telegram->getWebhookUpdates();

$chat_id = $update['message']['chat']['id'] ?? '';
$text = $update['message']['text'] ?? '';


if ($text == '/start') {

    $response = $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Привет, {$update['message']['chat']['first_name']}, я бот-синоптик и могу подсказать тебе погоду в любом городе мира!\n
                    Для получения погоды отправьте геолокацию (<b>доступно с мобильных устройств</b>).\n
                    Так же возможно указать город в формате:\n
                    <b>Город</b> или <b>Город, код страны</b>\n
                    Пример: <b>London</b> или <b>Moscow, ru</b>, или <b>Белград</b>",
        'parse_mode' => 'HTML',
    ]);

} elseif (!empty($text)) {
    $weather_url .= "&q={$text}";

    $res = json_decode(file_get_contents($weather_url));

} elseif (isset($update['message']['location'])) {
    $weather_url .= "&lat={$update['message']['location']['latitude']}{$text}&lon={$update['message']['location']['longitude']}";

    $res = json_decode(file_get_contents($weather_url));

} else {
    $response = $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Я текстовый бот-переводчик, пожалуйста напиши мне текст..',
    ]);
}

if (empty($res) and $text != '/start') {
    $response = $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Укажите корректный формат..',
    ]);

} else {
    $temp = round($res->main->temp);
    $answer = "<u>Информация о погоде:</u>\n
                Город: <b>{$res->name}</b>\nСтрана: <b>{$res->sys->country}</b>
                Погода: <b>{$res->weather[0]->description}</b>\nТемпература: {$temp}℃";

    $response = $telegram->sendPhoto([
        'chat_id' => $chat_id,
        'photo' => "https://openweathermap.org/img/wn/{$res->weather[0]->icon}@4x.png",
        'caption' => $answer,
        'parse_mode' => 'HTML',
    ]);
}