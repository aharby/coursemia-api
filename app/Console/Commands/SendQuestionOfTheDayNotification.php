<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Users\Models\User;
use App\Notifications\QuestionOfTheDay;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Resources\API\QuestionResource;

class SendQuestionOfTheDayNotification extends Command
{
    protected $signature = 'send:question-of-the-day';
    protected $description = 'Send the Question of the Day as a push notification';

    public function handle()
    {
        $this->info('sending question of the day..');

        // Define the Question of the Day
        $question = $this->getQuestionOfTheDay();

        // Get all users with an FCM token
        // $devices = UserDevice::whereNotNull('device_token')->get();

        $users = User::whereHas('devices', function ($query) {
            $query->whereNotNull('device_token');
        })->get();

        if ($users->isEmpty()) {
            $this->info("No users with FCM tokens found.");
            return;
        }
        
        $notifySuccessCount = 0;
        $notifyFailureCount = 0;

        // Send notification to each user
        foreach ($users as $user) {
            try{

                $user->notify(new QuestionOfTheDay($question));

                $notifySuccessCount++;

            } catch (CouldNotSendNotification $e) { // Catch notification-specific exceptions
                $this->logWarn('Notification failed: ' . $e->getMessage(). ' Code: '. $e->getCode());
                //clean expired tokens
                $notifyFailureCount++;
            }
        }

        $this->logInfo('Daily Question Notification sent to ' . $notifySuccessCount . ' users.');
        $this->logWarn('Daily Question Notification couldn\'t be sent to ' . $notifyFailureCount . ' users.');
        $this->logInfo('Send Notification: Question of the Day Done.');
    }

    private function getQuestionOfTheDay()
    {
        $question = Question::inRandomOrder()->first();

        return (new QuestionResource($question))->toJson();
    }

    private function logWarn($log_message){
        \Log::warning($log_message);
        $this->warn($log_message);
    }

    private function logInfo($log_message){
        \Log::info($log_message);
        $this->info($log_message);
    }
}