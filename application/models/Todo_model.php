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

    public function getTodoPriority($priority)
    {
        // $this->db->where('priority', $priority);
        // $this->db->from('todo');
        // return $this->db->get('todo')->result();
        return $this->db->get_where('todo', ['priority' => $priority])->result();
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
}
