<?php

    namespace App\OurEdu\VCRSessions\Models;

    use App\OurEdu\BaseApp\BaseModel;
    use Carbon\Traits\Timestamp;
    use App\Environ\Users\User;

    class LiveSessionParticipants extends BaseModel
    {
        use Timestamp;

        protected $table = 'live_session_participants';
        protected $fillable = [
            'user_id',
            'user_role',
            'user_token',
        ];

        public function user(){
            return $this->belongsTo(User::class);
        }

        /**
         * Get the owning sessionable model.
         */
        public function morphable()
        {
            return $this->morphTo();
        }
    }
