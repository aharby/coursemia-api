<?php


namespace App\OurEdu\VCRSessions\General\Traits;

use DateTime;
use DateTimeZone;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait Zoom
{
    public function zoomGet(string $path, array $query = [])
    {
        $url = $this->retrieveZoomUrl();
        $request = $this->zoomRequest();
        return $request->get($url . $path, $query);
    }

    private function retrieveZoomUrl()
    {
        return env('ZOOM_API_URL', 'https://api.zoom.us/v2/');
    }

    private function zoomRequest()
    {
        $jwt = $this->generateZoomToken();
//      $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6Il9iYkFFRnd0VGVlRjZURHJ2eEJvNmciLCJleHAiOjE2MTgxNjQxNDQsImlhdCI6MTYxODE1ODc0NH0.mzaol7s5WU6qXEfskOAO41wVFOwpyRyggRKAlBjNrKY';
        return Http::withHeaders([
            'authorization' => 'Bearer ' . $jwt,
            'content-type' => 'application/json',
        ]);
    }

    public function generateZoomToken()
    {
        $key = env('ZOOM_API_KEY', '_bbAEFwtTeeF6TDrvxBo6g');
        $secret = env('ZOOM_API_SECRET', 'tMHUcvKqTeKnN3JhqcxVIlodbumx06yq9BsI');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+10 minute'),
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }

    public function zoomPost(string $path, array $body = [])
    {
        $url = $this->retrieveZoomUrl();
        $request = $this->zoomRequest();
        return $request->post($url . $path, $body);
    }

    public function zoomPut(string $path, array $body = [])
    {
        $url = $this->retrieveZoomUrl();
        $request = $this->zoomRequest();

        return $request->put($url . $path, $body);
    }

    public function zoomPatch(string $path, array $body = [])
    {
        $url = $this->retrieveZoomUrl();
        $request = $this->zoomRequest();
        return $request->patch($url . $path, $body);
    }

    public function zoomDelete(string $path, array $body = [])
    {
        $url = $this->retrieveZoomUrl();
        $request = $this->zoomRequest();
        return $request->delete($url . $path, $body);
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new DateTime($dateTime);
            return $date->format('Y-m-d\TH:i:s');
        } catch (Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());
            return '';
        }
    }

    public function toUnixTimeStamp(string $dateTime, string $timezone)
    {
        try {
            $date = new DateTime($dateTime, new DateTimeZone($timezone));
            return $date->getTimestamp();
        } catch (Exception $e) {
            Log::error('ZoomJWT->toUnixTimeStamp : ' . $e->getMessage());
            return '';
        }
    }

    function generate_signature($meeting_number = '94958115116', $role = 0)
    {
        $api_key = env('ZOOM_API_KEY', '_bbAEFwtTeeF6TDrvxBo6g');
        $api_secret = env('ZOOM_API_SECRET', 'tMHUcvKqTeKnN3JhqcxVIlodbumx06yq9BsI');
        //Set the timezone to UTC
        date_default_timezone_set("UTC");

        $time = time() * 1000 - 30000;//time in milliseconds (or close enough)

        $data = base64_encode($api_key . $meeting_number . $time . $role);

        $hash = hash_hmac('sha256', $data, $api_secret, true);

        $_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);

        //return signature, url safe base64 encoded
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }
}
