<?php

namespace App\Modules\Courses\Services;

class VideoService
{
    public function getVideoDuration($videoUrl)
    {
        $command = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($videoUrl) . " 2>/dev/null";
        $duration = shell_exec($command);
        
        return $duration ? (float) trim($duration) : null;
    }
    
    public function formatDuration($seconds)
    {
        if (!$seconds) return null;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    public function getVideoInfo($videoUrl)
    {
        $command = "ffprobe -v quiet -print_format json -show_format -show_streams " . escapeshellarg($videoUrl) . " 2>/dev/null";
        $output = shell_exec($command);
        
        return $output ? json_decode($output, true) : null;
    }
}