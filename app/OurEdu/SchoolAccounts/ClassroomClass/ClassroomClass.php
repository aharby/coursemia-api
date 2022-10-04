<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\Enums\ClassroomClassEnum;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ClassroomClass extends BaseModel
{
    protected $dates = ['from', 'to', 'starts', 'ends', 'until_date'];
    protected $fillable = [
        'id',
        'classroom_id',
        'subject_id',
        'instructor_id',
//        'starts',
//        'ends',
        'from',
        'from_time',
        'to',
        'to_time',
        'all_day',
        'until_date',
        'repeat',
//        'repetition_times',
        'sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'sun_from',
        'mon_from',
        'tue_from',
        'wed_from',
        'thu_from',
        'fri_from',
        'sat_from',
        'sun_to',
        'mon_to',
        'tue_to',
        'wed_to',
        'thu_to',
        'fri_to',
        'sat_to',
    ];

    public function classroom()
    {

        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function subject()
    {

        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function instructor()
    {

        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function createOrUpdateSessions()
    {
        // Delete sessions if exists
        $this->sessions()->whereDate('from', '>', now())->delete();
        $start = $this->from;
        $end = $this->from;
        $until = $this->until_date;
//        $repetitionTimes = $this->repetition_times;
        $repeat = $this->repeat;

        if ($this->from_time) {
            $start->setHour(explode(":", $this->from_time)[0]);
            $start->setMinute(explode(":", $this->from_time)[1]);
        }
        if ($this->to_time) {
            $end->setHour(explode(":", $this->to_time)[0]);
            $end->setMinute(explode(":", $this->to_time)[1]);
        }

        $sessions = $this->calculateSessions($repeat, $start, $end, $until);

        if ($sessions instanceof ClassroomClassSession) {
            $sessions = new Collection($sessions);
        }

        if ($sessions->count() > 0) {

//            $starts = $sessions->sortBy('from')->first()->from;
//            $ends = $sessions->sortByDesc('to')->first()->to;

//            $this->update([
//                'starts' => $starts,
//                'ends'   => $ends
//            ]);
        }
        return $sessions;
    }

    public function sessions()
    {

        return $this->hasMany(ClassroomClassSession::class, 'classroom_class_id');
    }

    /**
     * Calculates the number of sessions
     * @param        $type
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @param int $times
     * @param array $options
     * @return Session|Collection
     */
    private function calculateSessions($type, Carbon $start, Carbon $end, ?Carbon $until, $times = 0)
    {
        if ($type == ClassroomClassEnum::NOREPEAT) {

            // Create the first Session
            $sessions = new Collection();
            $session = $this->createSession($start, $end);
            if ($session) {
                $sessions->push($session);
            }
            return $sessions;

        } elseif ($type == ClassroomClassEnum::HOURLY) {

            // Repeat the session hourly
            $until = $until->addDays();
            return $this->repeatHourly($start, $end, $until);

        } elseif ($type == ClassroomClassEnum::DAILY) {

            // Repeat the session daily
            return $this->repeatDaily($start, $end, $until);

        } elseif ($type == ClassroomClassEnum::WEEKLY) {

            // Repeat the session weekly
            return $this->repeatWeekly($start, $end, $until);

        } elseif ($type == ClassroomClassEnum::MONTHLY) {

            // Repeat the session monthly
            return $this->repeatMonthly($start, $end, $until);
        }

        return new Collection();
    }

    /**
     * Create Session
     * @param Carbon $start
     * @param Carbon $end
     * @return Session
     * @throws ValidationException
     */
    public function createSession(Carbon $start, Carbon $end)
    {
        $start = $start->format('Y-m-d H:i:s');
        $end = $end->format('Y-m-d H:i:s');
//        $contradictionSession = ClassroomClassSession::where('classroom_id', $this->classroom_id)
//            //contain case
//            ->where(function ($query) use ($start, $end) {
//                $query->where('from', '>=', $start)->where('to', '<=', $end);
//            })
////        //from overlap
//            ->orWhere(function ($query) use ($start) {
//                $query->where('from', '<=', $start)->where('to', '>=', $start);
//            })
//            //to overlap
//            ->orWhere(function ($query) use ($end) {
//                $query->where('to', '<=', $end)->where('to', '>=', $end);
//            })->exists();

        $contradictionSessionClass = ClassroomClassSession::query()
            ->where('classroom_id', $this->classroom_id)
            ->where("from", "<=", $end)
            ->where("to", ">=", $start)
            ->first();

        $contradictionSession = ClassroomClassSession::query()
            ->where('instructor_id', $this->instructor_id)
            ->where("from", "<=", $end)
            ->where("to", ">=", $start)
            ->first();

        if (!$contradictionSession && !$contradictionSessionClass) {
            $session = $this->sessions()->create([
                "classroom_id" => $this->classroom_id,
                "subject_id" => $this->subject_id,
                "instructor_id" => $this->instructor_id,
                "from" => $start,
                "to" => $end,
            ]);

            return $session;
        } else {
            $classroomClass = isset($contradictionSessionClass) ? $contradictionSessionClass->classroom_class_id : $contradictionSession->classroom_class_id;

            $messageAttributes =  [
                'from' => $contradictionSession->from ?? $contradictionSessionClass->from ?? "",
                'to' => $contradictionSession->to ?? $contradictionSessionClass->to ?? "",
                'instructor' => $contradictionSession->instructor->name ?? '',
                ];

            if ($classroomClass) {
                $messageAttributes['link'] = "<a target='_blank' href='" . route('school-branch-supervisor.sessions.class.get.index',['classroomClass' => $classroomClass])."'>". trans("see sessions") ."</a>";
            }

            throw ValidationException::withMessages([
                'contradiction' => trans('classroomClass.there_are_some_contradiction_this_schedule_from_to_with_instructor', $messageAttributes)
            ]);
        }
    }

    /**
     * Repeat a session hourly
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @return Collection
     */
    private function repeatHourly(Carbon $start, Carbon $end, Carbon $until)
    {
        $sessions = collect();

        // Get the first session amount of minutes
        $minutes = $end->diffInMinutes($start);

        // While we did not reach the until day yet..
        while ($until->gte($start)) {
            $sessions->merge($this->createHourlySession($start, $end, $until, $minutes));
        }

        return $sessions;
    }

    /**
     * Create a Session Hourly
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @param        $minutes
     * @param array $options
     * @return Collection
     */
    private function createHourlySession(Carbon $start, Carbon $end, Carbon $until, $minutes)
    {
        $days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        $dayOfWeek = $start->dayOfWeek;
        $sessions = collect();

        for ($index = $dayOfWeek; $index < 7; $index++) {
            $day = $days[$index];

            // Check if the day is checked
            if ($this->$day and $until->gte($start)) {
                $varFrom = $days[$index] . '_from';
                $varTo = $days[$index] . '_to';

                // First we get the day repetition range time
                $from = explode(':', $this->$varFrom);
                $to = explode(':', $this->$varTo);

                // Set when the first session of the day will start
                if (array_key_exists(0, $from)) {
                    $start->hour = (integer)$from[0];
                }
                if (array_key_exists(1, $from)) {
                    $start->minute = (integer)$from[1];
                }

                // Set when the first session of the day will end
                $end = new Carbon($start->toDateTimeString());
                $end->addMinutes($minutes);

                // Set When the last session of the day will end
                $endOfDay = new Carbon($start->toDateString());

                if (array_key_exists(0, $to)) {
                    $endOfDay->hour = (integer)$to[0];
                }

                if (array_key_exists(1, $to)) {
                    $endOfDay->minute = (integer)$to[1];
                }

                while ($endOfDay->gt($start)) {
                    $session = $this->createSession($start, $end);
                    if ($session) {
                        $sessions->push($session);
                    }
                    if ($this->repetition_times) {
                        $start->addHours($this->repetition_times);
                        $end->addHours($this->repetition_times);
                        continue;
                    }

                    $start->addHours(1);
                    $end->addHours(1);
                }
            }

            // Get to the next day
            $start->addDay();
            $end->addDay();
        }
        return $sessions;
    }

    /**
     * Repeat a session daily
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @return Collection
     */
    private function repeatDaily(Carbon $start, Carbon $end, Carbon $until)
    {
        $sessions = collect();
        do {
            $session = $this->createSession($start, $end);
            if ($session) {
                $sessions->push($session);
            }
            $start->addDays($this->repetition_times);
            $end->addDays($this->repetition_times);
        } while ($until->gte($start));
        return $sessions;
    }

    /**
     * Repeat a session weekly
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @return Collection
     */
    private function repeatWeekly(Carbon $start, Carbon $end, Carbon $until)
    {

        $sessions = collect();

        $days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        $filterDays = [];
        foreach ($days as $key => $day) {
            if ($this->$day == 1) {
                $filterDays[$key] = $day;
            }
        }
        $filterDaysKeys = array_keys($filterDays);
        $ranges = CarbonPeriod::create($start->toDateTimeString(), $until->toDateString() . ' ' . $end->toTimeString());

        foreach ($ranges as $date) {
            if (in_array($date->dayOfWeek, $filterDaysKeys)) {
                $endSession = Carbon::parse($date->toDateString() . ' ' . $end->toTimeString());
//                dump($date->toDateTimeString(),$endSession->toDateTimeString());
                $session = $this->createSession($date, $endSession);
                if ($session) {
                    $sessions->push($session);
                }
            }
        }
        return $sessions;
    }

    /**
     * Repeat a session monthly
     * @param Carbon $start
     * @param Carbon $end
     * @param Carbon $until
     * @param array $options
     * @return Collection
     */
    private function repeatMonthly(Carbon $start, Carbon $end, Carbon $until)
    {
        $sessions = collect();
        do {
            $session = $this->createSession($start, $end);
            if ($session) {
                $sessions->push($session);
            }
            $start->addMonths($this->repetition_times);
            $end->addMonths($this->repetition_times);
        } while ($until->gte($start));
        return $sessions;
    }

    public function createSessions()
    {
        $monday = Carbon::parse('First Monday of January 2000');
        $tuesday = Carbon::parse('First Tuesday of January 2000');
        $sunday = Carbon::parse('Last Sunday of January 2000');
        $now = Carbon::now();
        $mondays = new DatePeriod(
            $monday,
            CarbonInterval::week(),
            $now
        );
        $tuesdays = new DatePeriod(
            $tuesday,
            CarbonInterval::week(),
            $now
        );
        $sundays = new DatePeriod(
            $sunday,
            CarbonInterval::week(4),
            $now
        );

        $allDays = [];
        foreach ($mondays as $day) {
            $allDays[] = $day;
        }
        foreach ($tuesdays as $day) {
            $allDays[] = $day;
        }
        foreach ($sundays as $day) {
            $allDays[] = $day;
        }
        usort($allDays, function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });
        foreach ($allDays as $day) {
            echo $day->format("M D Y-m-d") . "<br>";
        }
    }

    /**
     * @param array $options
     * @return Carbon|null
     */
    private function convertFirstSessionStartDateToCarbon(array $options)
    {
        return $this->convertFirstSessionDateToCarbon($options, "from", "from_time");
    }

    /**
     * @param array $options
     * @param       $dateField
     * @param       $timeField
     * @return Carbon|null
     */
    private function convertFirstSessionDateToCarbon(array $options, $dateField, $timeField)
    {
        $date = null;

        if (array_has($options, $dateField)) {
            $date = new Carbon($options[$dateField]);
        }

        if (array_has($options, 'all_day')) {
            return $date;
        }

        if (array_has($options, $timeField)) {
            $time = explode(':', $options[$timeField]);
            $date->hour = $time[0];
            $date->minute = $time[1];
            return $date;
        }

        return $date;
    }

    /**
     * @param array $options
     * @return Carbon|null
     */
    private function convertFirstSessionEndDateToCarbon(array $options)
    {
        return $this->convertFirstSessionDateToCarbon($options, "to", "to_time");
    }
}
