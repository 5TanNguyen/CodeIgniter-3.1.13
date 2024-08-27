<?php

class Auth_Controller extends RestApi_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->model('User_model');
        $this->load->library('session');
    }

    function register()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $image = '';

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');

        if ($this->form_validation->run()) {
            $data = array(
                'email' => $email,
                'password' => sha1($password),
                'firstname' => $firstname,
                'lastname' => $lastname,
                'image' => $image
            );
            $this->User_model->registerUser($data);
            $responseData = array(
                'status' => true,
                'message' => 'Successfully Registered',
                'data' => []
            );

            return $this->response($responseData, 200);
        } else {
            $responseData = array(
                'status' => false,
                'message' => 'Fill all the required fields',
                'data' => []
            );

            return $this->response($responseData);
        }
    }

    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run()) {
            $data = array('email' => $email, 'password' => sha1($password));
            $loginStatus = $this->User_model->checkLogin($data);
            if ($loginStatus != false) {
                $userId = $loginStatus->id;
                $bearerToken = $this->api_auth->generateToken($userId);

                $this->session->set_userdata('user_id', $userId);

                $responseData = array(
                    'status' => true,
                    'message' => 'Successfully Loggoed In',
                    'token' => $bearerToken
                );
                return $this->response($responseData, 200);
            } else {
                $responseData = array(
                    'status' => false,
                    'message' => 'Invalid Credentials',
                    'data' => []
                );
                return $this->response($responseData);
            }
        } else {
            $responseData = array(
                'status' => false,
                'message' => 'Email and password are required!!!',
                'data' => []
            );
            return $this->response($responseData);
        }
    }
}
