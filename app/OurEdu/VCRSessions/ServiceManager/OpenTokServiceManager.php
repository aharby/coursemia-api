<?php

    namespace App\OurEdu\VCRSessions\ServiceManager;

    use OpenTok\OpenTok;
    use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
    use App\OurEdu\VCRSchedules\VCRSessionEnum;
    use OpenTok\MediaMode;
    use OpenTok\ArchiveMode;
    use OpenTok\Role;

    class OpenTokServiceManager implements OpenTokServiceManagerInterface
    {
        private $opentok = null, $VCRSessionRepository;

        private $sessionOptions = [];

        public function __construct(VCRSessionRepositoryInterface $VCRSessionRepository)
        {
            if (!$this->opentok) {
                $this->opentok = new OpenTok(config('services.opentok.api_key'), config('services.opentok.api_secret'));;
            }
            $this->VCRSessionRepository = $VCRSessionRepository;
        }

        /**
         * @inheritDoc
         */
        public function setSessionConfig($options = []): bool
        {
            // default session arguments
            $defaults = array(
                'mediaMode' => MediaMode::ROUTED,
                'archiveMode' => ArchiveMode::MANUAL,
            );
            //override optional arguments by the options sent from client
            $options = array_merge($defaults, array_intersect_key($options, $defaults));
            $this->sessionOptions = $options;
            return true;
        }

        /**
         * @inheritDoc
         */
        public function createSession()
        {
            //create a new session
            $session = $this->opentok->createSession($this->sessionOptions);

            // return sessionId to store it for later use
            return $session->getSessionId();

            return $sessionId;

        }

        /**
         * @inheritDoc
         */
        public function generateToken($sessionId, $options = [])
        {
            // default token arguments
            $defaults = array(
                'role' => Role::SUBSCRIBER,
                'expireTime' => null,
                'data' => null,
            );
            //override optional arguments by the options sent from client
            $options = array_merge($defaults, array_intersect_key($options, $defaults));
            $token = $this->opentok->generateToken($sessionId, $options);
            return $token;
        }
    }
