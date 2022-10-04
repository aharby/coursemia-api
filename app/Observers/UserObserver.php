<?php

namespace App\Observers;

use App\GeneralQuiz;

class UserObserver
{
    /**
     * Handle the general quiz "created" event.
     *
     * @param  \App\GeneralQuiz  $generalQuiz
     * @return void
     */
    public function created(GeneralQuiz $generalQuiz)
    {
        //
    }

    /**
     * Handle the general quiz "updated" event.
     *
     * @param  \App\GeneralQuiz  $generalQuiz
     * @return void
     */
    public function updated(GeneralQuiz $generalQuiz)
    {
        //
    }

    /**
     * Handle the general quiz "deleted" event.
     *
     * @param  \App\GeneralQuiz  $generalQuiz
     * @return void
     */
    public function deleted(GeneralQuiz $generalQuiz)
    {
        //
    }

    /**
     * Handle the general quiz "restored" event.
     *
     * @param  \App\GeneralQuiz  $generalQuiz
     * @return void
     */
    public function restored(GeneralQuiz $generalQuiz)
    {
        //
    }

    /**
     * Handle the general quiz "force deleted" event.
     *
     * @param  \App\GeneralQuiz  $generalQuiz
     * @return void
     */
    public function forceDeleted(GeneralQuiz $generalQuiz)
    {
        //
    }
}
