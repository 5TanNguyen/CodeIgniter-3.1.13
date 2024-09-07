<?php
class Todo_model extends CI_Model
{
    public function add($todo)
    {
        $this->db->insert('todo', $todo);
    }

    public function getAll()
    {
        $this->db->select('*');
        $this->db->from('todo');
        return $this->db->get()->result();
    }

    public function getAllExcel()
    {
        $this->db->select('*');
        $this->db->from('todo');
        return $this->db->get()->result_array();
    }

    public function getTodoPriority($priority = null)
    {
        return $priority ? $this->db->from('todo')->select('*')->where('priority', $priority)->join('todo_meta', 'todo_meta.todo_id = todo.id', 'left')->get()->result() :
            $this->db->select('*')->from('todo')->join('todo_meta', 'todo_meta.todo_id = todo.id', 'left')->get()->result_array();
    }

    public function delete($todo_id)
    {
        $this->db->where('id', $todo_id);
        $this->db->delete('todo');
    }

    public function edit($todo, $todo_id)
    {
        $this->db->where('id', $todo_id);
        $this->db->update('todo', $todo);
    }

    public function findByName($name)
    {
        $this->db->like('name', $name);
        $this->db->from('todo');
        return $this->db->get()->result();
    }

    public function getAllTodo($user_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('users.id', $user_id);
        $this->db->join('todo', 'todo.user_id = users.id', 'left');
        return $this->db->get()->result();
    }
}
