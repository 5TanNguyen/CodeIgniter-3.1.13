<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Todo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Todo_model');
    }

    public function index()
    {
        $todo['todo'] = $this->Todo_model->getAll();
        $this->load->view('todo', $todo);
    }

    public function add()
    {
        $todo['user_id'] = 1;
        $todo['name'] = $this->input->post('name');
        $todo['description'] = $this->input->post('description');
        $todo['priority'] = $this->input->post('priority');

        $this->Todo_model->add($todo);
        redirect('todo');
    }

    public function edit($todo_id)
    {
        // $todo['user_id'] = 1;
        $todo['name'] = $this->input->post('name');
        $todo['description'] = $this->input->post('description');
        $todo['priority'] = $this->input->post('priority');

        $this->Todo_model->edit($todo, $todo_id);
        redirect('todo');
    }

    public function delete($todo_id)
    {
        $this->Todo_model->delete($todo_id);
        redirect('todo');
    }
}
