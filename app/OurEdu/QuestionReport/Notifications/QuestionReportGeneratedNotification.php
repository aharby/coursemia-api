<?php

namespace App\OurEdu\QuestionReport\Notifications;

use App\OurEdu\BaseApp\Enums\UrlActionEnums;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuestionReportGeneratedNotification extends Notification implements ShouldQueue
{
    protected $questionReport;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($questionReport)
    {
        $this->questionReport = $questionReport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        App::setlocale($notifiable->language ?? config('app.locale'));

        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $multiLanguageData = [];

        foreach (config('translatable.locales') as $locale) {
            $multiLanguageData[$locale] =
                 [
                    'title' => trans('notification.Question report generated', [], $locale),
                    'body' => trans('notification.Question report generated! check it out', [], $locale),
                    'endpoint_url' => UrlActionEnums::getQuestionReportUrl($this->questionReport)
                ];
        }

        return [
            'title' => trans('notification.Question report generated'),
            'body' => trans('notification.Question report generated! check it out'),
            'endpoint_url' => UrlActionEnums::getQuestionReportUrl($this->questionReport),
            'localization'  => $multiLanguageData,
        ];
    }
}
