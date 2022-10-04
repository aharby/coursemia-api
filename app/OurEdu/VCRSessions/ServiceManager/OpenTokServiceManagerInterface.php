<?php

    namespace App\OurEdu\VCRSessions\ServiceManager;

    interface  OpenTokServiceManagerInterface
    {
        /**
         * set session config
         * as session [mediaMode | archiveMode]
         * the validation of given value are handled through opentok SDK itself
         *
         * @param array $options (Optional) This array defines options for the session.
         * @return bool
         */
        public function setSessionConfig($options = []): bool;

        /**
         * Creates a new OpenTok session and returns the session ID, which uniquely identifies
         * the session.
         * @return string opentok session id
         */
        public function createSession();

        /**
         * Creates a user token the frontend application need to passes when connecting to the session.
         *
         * @param string $sessionId The session ID corresponding to the session to which the user
         * will connect.
         *
         * @param array $options This array defines options for the token. This array includes the
         * following keys, all of which are optional:
         *
         * @return string The token string.
         */
        public function generateToken($sessionId, $options = []);
    }
