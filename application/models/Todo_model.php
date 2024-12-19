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
        return $this->db->get()->result_array();
    }

    public function getAllExcel()
    {
        $this->db->select('*');
        $this->db->from('todo');
        return $this->db->get()->result_array();
    }

    public function getAllCodeTodoMeta()
    {
        $todo_meta = $this->db
            ->distinct()
            ->from('todo_meta')
            ->select('code, fieldname')
            ->get()
            ->result_array();

        return $todo_meta ? $todo_meta : [];
    }

    public function getTodoPriority($priority = null)
    {
        return $priority ?
            $this->db
            ->from('todo')
            ->select('*')
            ->join('todo_meta', 'todo_meta.todo_id = todo.id', 'left')
            ->where('priority', $priority)
            ->get()->result_array()
            :
            $this->db
            ->select('*')
            ->from('todo')
            ->join('todo_meta', 'todo_meta.todo_id = todo.id', 'left')
            ->get()->result_array();
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


    // Code Update

    function commonRequest($key)
    {
        if (isset($_GET[$key])) return $_GET[$key];
        if (isset($_POST[$key])) return $_POST[$key];
        if (isset($_FILES[$key])) return $_FILES[$key];


        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        return isset($obj->$key) ? $obj->$key : null;
    }

    public function getTodo()
    {
        $draw = $this->commonRequest('draw');

        // $searchValue = commonRequest('searchValue');
        $priorityId = $this->commonRequest('priorityId');
        $dataFilter = $this->commonRequest('dataFilter');

        // $created_at = commonRequest('created_at');

        $start = $this->input->get('start') ?? 0;
        $length = $this->input->get('length') ?? 10;

        $select_fields = "
             id,
             user_id,
             name,
             description,
             priority,
             image,
             todo_meta_id,
             code,
             fieldname,
             fieldvalue
         ";

        // Các cột order
        $columns = [
            'id',
            '',
            'image',
            'name',
            'description',
        ];

        $this->db
            ->select($select_fields)
            ->join('todo_meta', 'todo_meta.todo_id = todo.id', 'left')
            ->group_by('id, todo_meta_id');

        // Điếm tổng số lượng trước khi lọc
        $totalRecordsQuery = clone $this->db;
        $recordsTotal = $totalRecordsQuery->count_all_results('todo');

        // if ($searchValue) {
        //     $this->db->group_start(); // Bắt đầu nhóm các điều kiện LIKE
        //     $this->db->like('dm_ton_giao_ma', $searchValue);
        //     $this->db->or_like('dm_ton_giao_ten_tieng_viet', $searchValue);
        //     $this->db->or_like('dm_ten_ton_giao_tieng_anh', $searchValue);
        //     $this->db->group_end(); // Kết thúc nhóm các điều kiện LIKE
        // }

        if ($priorityId) $this->db->where('todo.priority', $priorityId);

        // if ($created_at) {
        //     $this->db->where('dm_ton_giao.created_at >=', "{$created_at} 00:00:00");
        //     $this->db->where('dm_ton_giao.created_at <=', "{$created_at} 23:59:59");
        // }

        // Điếm tổng số lượng sau khi lọc
        $filteredQuery = clone $this->db;
        $recordsFiltered = $filteredQuery->count_all_results('todo');

        $order = json_encode($_GET['order'], true);

        // if (commonRequest('order')) {
        if ($order) {
            // $order = json_decode(commonRequest('order'), true);
            $order = json_decode($order, true);

            $orderColumnIndex = $order[0]['column'];
            $orderColumn = $columns[$orderColumnIndex];
            $orderDir = $order[0]['dir'];

            if (!empty($orderColumn) && !empty($orderDir)) {
                $this->db->order_by($orderColumn, $orderDir);
            }
        }

        if ($length != -1) $this->db->limit($length, $start);

        $data = $this->db
            ->get('todo')
            ->result_array();

        if ($dataFilter) {
            $filter = [
                'todo' => $this->db
                    ->from('todo')
                    ->select('priority')
                    ->get()
                    ->result_array()
            ];

            return [
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'dataFilter' => $filter
            ];
        }

        return [
            'draw' => $this->input->get('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function setValueField($id, $field, $value)
    {
        $data = array(
            $field => $value
        );

        return $this->db
            ->where('id', $id)
            ->update('todo', $data);
    }

    public function setValueFieldTodoMeta($id, $field, $value)
    {
        $data = array(
            $field => $value
        );

        return $this->db
            ->where('todo_meta_id', $id)
            ->update('todo_meta', $data);
    }

    public function getChart()
    {
        $status1 = $this->db
            ->from('todo')
            ->where('status', 1)
            ->count_all_results();

        $status2 = $this->db
            ->from('todo')
            ->where('status', 2)
            ->count_all_results();

        $status3 = $this->db
            ->from('todo')
            ->where('status', 3)
            ->count_all_results();

        return [
            'status_1' => $status1,
            'status_2' => $status2,
            'status_3' => $status3,
        ];
    }
}
