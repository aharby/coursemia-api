<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Users\Models\User;
use App\Notifications\QuestionOfTheDayUpdated;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Resources\API\QuestionResource;

class SendQuestionOfTheDayNotification extends Command
{
    protected $signature = 'update:question-of-the-day';
    protected $description = 'Question of the Day updated notification as a push notification';

    private $qotd;
    private $qotdTitle;

    public function handle()
    {
        $this->info('sending question of the day..');

        // Define the Question of the Day
        if(!$this->updateQotd()){
            $this->logWarn('no questions available');
            return;
        }


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

                $cleanTitle = html_entity_decode(strip_tags($this->qotdTitle));
                $user->notify(new QuestionOfTheDayUpdated($this->qotd, $cleanTitle));

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

    private function updateQotd()
    {
        $newQuestion = Question::where('is_qotd', false)->inRandomOrder()->first();

        if(!$newQuestion)
            return false;

        Question::where('is_qotd', true)->update(['is_qotd' => false]);

        $newQuestion->update(['is_qotd' => true]);

        $this->logInfo('Question of the day updated');
        
        $this->qotd = (new QuestionResource($newQuestion))->toJson();

        $this->qotdTitle = $newQuestion->title;

        return true;
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