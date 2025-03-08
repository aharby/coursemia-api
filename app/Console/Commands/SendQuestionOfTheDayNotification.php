<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Users\Models\User;
use App\Notifications\QuestionOfTheDay;

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
        
        // Send notification to each user
        foreach ($users as $user) {
            $user->notify(new QuestionOfTheDay($question));
        }

        $this->info('Daily Question Notification sent to ' . $users->count(). ' users.');

        $this->info('Daily Question Notification sent successfully.');
    }

    private function getQuestionOfTheDay()
    {
        // Define a set of questions
        $questions = [
            "What’s one thing you’re grateful for today?",
            "If you could have dinner with any historical figure, who would it be and why?",
            "What’s the most valuable lesson you’ve learned in the past year?",
            "If you had to describe your mood in a song, what song would it be?",
            "What’s a small change you can make today to improve your life?",
        ];

        // Return a random question each day
        return $questions[array_rand($questions)];
    }
}