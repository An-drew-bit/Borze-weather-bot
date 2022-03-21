<?php

namespace config;

class Settings
{
    protected $token;
    protected $weather_token;
    protected $weather_url;

    public function __construct()
    {
        $this->token = '?';
        $this->weather_token = '?';
        $this->weather_url = "https://api.openweathermap.org/data/2.5/weather?appid={$this->weather_token}&units=metric&lang=ru";
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getWeatherUrl()
    {
        return $this->weather_url;
    }
}