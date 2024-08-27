<?php
class Calendar_model extends CI_Model
{
    public function add($calendar)
    {
        $this->db->insert('calendar', $calendar);
    }

    public function getAll()
    {
        // $this->db->select('title, start, end');
        $this->db->select('*');
        $this->db->from('calendar');
        return $this->db->get()->result_array();
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

    // API
    public function registerUser($user)
    {
        $this->db->insert('users', $user);
    }

    public function checkLogin($data)
    {
        $this->db->where($data);
        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getProfile($userId)
    {
        $this->db->select('*');
        $this->db->where(['id' => $userId]);
        $query = $this->db->get('users');
        return $query->row();
    }
}