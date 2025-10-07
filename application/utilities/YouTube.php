<?php

class YouTube extends FatUtility
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function getVideoId($url)
    {
        $pattern = '%^# Match any youtube URL
            (?:https?://)?  # Optional scheme. Either http or https
            (?:www\.)?      # Optional www subdomain
            (?:             # Group host alternatives
                youtu\.be/    # Either youtu.be,
            | youtube\.com  # or youtube.com
                (?:           # Group path alternatives
                    /embed/     # Either /embed/
                | /v/         # or /v/
                | .*v=        # or /watch\?v=
                )             # End path alternatives.
            )               # End host alternatives.
            ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
            ($|&).*         # if additional parameters are also in query string after video id.
            $%x';
        if (!$result = preg_match($pattern, $url, $matches)) {
            return 0;
        }
        if (!isset($matches[1])) {
            return 0;
        }
        return $matches[1];
    }

    public static function getYoutubeVideoDuration($url)
    {
        $apiKey = FatApp::getConfig('CONF_YOUTUBE_DATA_API_KEY', FatUtility::VAR_STRING, '');
        if (empty($apiKey)) {
            return 0;
        }
        $videoId = static::getVideoId($url);
        $googleApi = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoId . '&key=' . $apiKey . '&part=snippet,contentDetails,statistics,status';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApi);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $curlResource = curl_exec($ch);
        curl_close($ch);
        $youtubeData = json_decode($curlResource);
        $youtubeVals = json_decode(json_encode($youtubeData), true);
        if (!empty($youtubeVals['error'])) {
            return false;
        }
        if ($youtubeVals['pageInfo']['totalResults'] < 1) {
            return false;
        }
        $duration = $youtubeVals['items'][0]['contentDetails']['duration'];
        $interval = new DateInterval($duration);
        return ($interval->d * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
    }

    public static function convertDuration($duration, $hours = true, $minutes = true, $seconds = false, $format = true)
    {
        $formattedTime = [];
        $time = [];
        if ($hours) {
            $hrs = floor($duration / 3600);
            if ($hrs > 0) {
                $formattedTime[] =  $hrs . strtolower(Label::getLabel('LBL_H'));
            }
            $time[] = $hrs;
        }
        if ($minutes) {
            $min = gmdate("i", $duration);
            if ($min > 0) {
                $formattedTime[] = $min . strtolower(Label::getLabel('LBL_M'));
            }
            $time[] = $min;
        }
        if ($seconds) {
            $sec = gmdate("s", $duration);
            if ($sec > 0) {
                $formattedTime[] = $sec . strtolower(Label::getLabel('LBL_S'));
            }
            $time[] = $sec;
        }
        if ($format == true) {
            return (count($formattedTime) > 0) ? implode(' ', $formattedTime) : '';
        } else {
            return (count($time) > 0 && array_sum($time) > 0) ? implode(':', $time) : '';
        }
    }
}
