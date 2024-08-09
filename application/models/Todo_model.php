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

    public function getTodoPriority($priority = null)
    {
        return $priority ? $this->db->get_where('todo', ['priority' => $priority])->result() :
            $this->db->select('*')->from('todo')->get()->result();
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
}
