<?php

class Api_Controller extends RestApi_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Todo_model');

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

    function getAllTodo()
    {
        // $user_id = $_SESSION['user_id'];
        $user_id = $this->session->userdata('user_id');
        // echo $user_id;
        // die();

        $query = $this->Todo_model->getAllTodo($user_id);

        $user = array(
            'status' => true,
            'message' => 'Successfully fetched profile',
            'data' => $query
        );
        $this->response($user, 200);
    }
}
