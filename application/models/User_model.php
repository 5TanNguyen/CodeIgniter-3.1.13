<?php
class User_model extends CI_Model
{
    public function findByEmail($email)
    {
        // return $this->db->get_where('users', ['email' => $email])->result();
        $this->db->where('email', $email);
        $this->db->from('users');
        return $this->db->get()->result();
    }

    public function add($todo)
    {
        $this->db->insert('users', $todo);
    }

    public function getAll()
    {
        $this->db->select('*');
        $this->db->from('users');
        return $this->db->get()->result();
    }

    public function delete($todo_id)
    {
        $this->db->where('id', $todo_id);
        $this->db->delete('users');
    }

    public function edit($user, $user_id)
    {
        $this->db->where('id', $user_id);
        $this->db->update('users', $user);
    }

    public function findByName($name)
    {
        $this->db->like('name', $name);
        $this->db->from('users');
        return $this->db->get()->result();
    }
}