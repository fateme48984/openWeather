<?php

/**
 * @author Fatemeh Yaghoobi
 * Class OpenWeather
 */
class OpenWeather {
    private $currentUrl = 'api.openweathermap.org/data/2.5/weather';
    private $apiKey = "";

    /**
     * @param array $cities array('تهران' => 'Tehran')
     *
     * @return array
     */
    public function getWeather($cities) {
        $output = array();
        $json = array();

        foreach ($cities as $name => $en_name) {
            $queryCur = $this->query($en_name , $this->currentUrl);
            $json[$en_name] = $queryCur;
        }
        
        if(!empty($json)) {
            foreach ($json as $en_name => $value) {
                if(is_array($value) && !empty($value)) {
                    $currentWehater = json_decode($value,true);
                    $output[$en_name] = $this->formatWeather($currentWehater);
                }
            }
        }

       return $output;
    }


    /**
     * @param string $query
     * @param string $url
     *
     * @return bool|string
     */
    private function query($query,$url) {
        $data = array('q'=>$query,'mode'=>'json','units'=>'Metric','appid'=>$this->apiKey);
        $queryString = $this->http_build_query($data);
        $httpHeaders = array(
            'Accept: application/json',
            'Host: api.openweather.org'
        );

        $ch = curl_init($url.'?'.$queryString);
        curl_setopt($ch,CURLOPT_TIMEOUT,120);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$httpHeaders);
        return curl_exec($ch);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    private function http_build_query($params) {
        $string = array();
        foreach ($params as $key => $value) {
            $string[] = sprintf('%s=%s',rawurldecode($key),rawurldecode($value));
        }
        $query = implode('&',$string);
        return $query;
    }

    /**
     * @param array $crrentWeahter
     *
     * @return array
     */
    private function formatWeather($crrentWeahter)
    {
        $weather = array(
            'updated_date_array' => explode('/', date('d/M/Y',$crrentWeahter['dt'])),
            'updated_time' => date('h:i a',$crrentWeahter['dt']),
            'wind_speed' => $crrentWeahter['wind']['speed'],
            'wind_speed_unit' => 'km',
            'humidity' => $crrentWeahter['main']['humidity'],
            'sunrise' => date('h:i a',$crrentWeahter['sys']['sunrise']),
            'sunset' => date('h:i a',$crrentWeahter['sys']['sunset']),
            'temperature' => $crrentWeahter['main']['temp'],
            'min_temp' =>$crrentWeahter['main']['temp_min'],
            'max_temp' =>$crrentWeahter['main']['temp_max'],
        );

        return $weather;
    }
}
