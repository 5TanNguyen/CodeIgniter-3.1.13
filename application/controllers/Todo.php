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

    public function getTodoByPriority()
    {
        $priority_id = $_GET['priority'];

        if ($priority_id == 0) {
            $todo = $this->Todo_model->getAll();
        } else {
            $todo = $this->Todo_model->getTodoPriority($priority_id);
        }

        if (!empty($todo)) {
            $no = 1;
            foreach ($todo as $item): ?>
                <tr>
                    <td><?php echo $no;
                        ?></td>
                    <td><?php echo $item->name
                        ?></td>
                    <td><?php echo $item->description
                        ?></td>
                    <td><?php echo $item->priority
                        ?></td>
                    <td><a type="button" class="btn btn-warning" onclick="fillData(`<?php echo $item->id;
                                                                                    ?>`, `<?php echo $item->name; ?>`,`<?php echo $item->description; ?>`,`<?php echo $item->priority; ?>`,)">Edit</a></td>
                    <td><a type="button" href="<?php echo base_url();
                                                ?>todo/delete/<?php echo $item->id
                                                                ?>" class="btn btn-danger" onclick="return confirm('You want to delete this todo ?')">Delete</a></td>
                </tr>
            <?php endforeach ?> <?php
                            } else {
                                ?>
            <tr>
                <td>There is do data</td>
            </tr>
<?php
                            }
                        }
                    }
