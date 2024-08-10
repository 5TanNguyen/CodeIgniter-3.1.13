<?php

class Api_Controller extends RestApi_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('api_auth');
        if ($this->api_auth->isNotAuthenticated()) {
            $err = array(
                'status' => false,
                'message' => 'unauthorised',
                'data' => []
            );
            $this->response($err);
        }
    }

    function getProfile()
    {
        $userId = $this->api_auth->getUserId();
        $this->load->model('User_model');
        $profileData = $this->User_model->getProfile($userId);
        $user = array(
            'status' => true,
            'message' => 'Successfully fetched profile',
            'data' => $profileData
        );
        $this->response($user, 200);
    }
}
