<?php

    namespace App\OurEdu\VCRSessions\Admin\Controllers;

    use App\OurEdu\BaseApp\Controllers\BaseController;
    use App\OurEdu\BaseApp\Enums\ParentEnum;
    use App\OurEdu\VCRSessions\ServiceManager\OpenTokServiceManagerInterface;
    use App\OurEdu\VCRSchedules\Models\VCRSession;

    class VCRServiceSessionsAdminController extends  BaseController
    {
        private $module,
         $title,
         $parent,
        $openTok;


        public function __construct(OpenTokServiceManagerInterface $openTok){
            $this->module = 'VCRSessions';
            $this->title = trans('VCRSessions.VCRSessions');
            $this->parent = ParentEnum::ADMIN;

            $this->openTok = $openTok;
        }

        public function getIndex()
        {
            $data['page_title'] = $this->title;
            $data['breadcrumb'] = '';
            $session = VCRSession::whereNotNull('session_id')->first();
            if(!$session){
                $session = $this->openTok->createSession();
            }
            $options = ['role' => 'publisher'];
            $token = $this->openTok->generateToken($session->session_id, $options);

            $data['session_id'] = $session->session_id;
            $data['token'] = $token;
            return view($this->parent . '.' . $this->module . '.index', $data);
        }

        public function getWhiteboard(){
            return view($this->parent . '.' . $this->module . '.whiteboard', []);
        }
    }
