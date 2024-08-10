<?php

class Auth_Controller extends RestApi_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->model('User_model');
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

    public function login() {}
}
