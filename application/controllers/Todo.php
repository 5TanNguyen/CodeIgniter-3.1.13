<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Todo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Todo_model');
        $this->load->model('User_model');
        $this->load->library('ImageEncryption');
        $this->load->library('session');
    }

    public function index()
    {
        // $todo['todo'] = $this->Todo_model->getAll();
        // $this->load->view('todo', $todo);

        if (!isset($_SESSION['email'])) {
            $this->load->view('login');
        }

        $this->load->view('todo');
    }

    public function validate()
    {
        $email = $this->input->post('email');

        $user = $this->User_model->findByEmail($email);

        // var_dump($user[0]->email);
        // die();
        // if (!empty($user)) {
        //     $_SESSION['email'] = $user[0]->email;
        //     $_SESSION['firstname'] = $user[0]->firstname;
        //     $_SESSION['lastname'] = $user[0]->lastname;
        // }

        $_SESSION['user_id'] = $user[0]->id;
        $_SESSION['email'] = $user[0]->email;
        $_SESSION['firstname'] = $user[0]->firstname;
        $_SESSION['lastname'] = $user[0]->lastname;
        $_SESSION['image'] = $user[0]->image;

        $this->load->view('todo');
    }

    public function logout()
    {
        // remove all session variables
        session_unset();

        // destroy the session
        session_destroy();

        $this->load->view('login');
    }

    public function list()
    {
        $todo['todo'] = $this->Todo_model->getAll();
        $this->load->view('todo', $todo);
    }

    public function add()
    {
        $todo['user_id'] = 1;
        // $key = $this->input->post("key");
        $key = 'my-key';

        // $item_id = $this->input->post("item_id");
        $item_id = 'my-item_id';

        // $key_code = $this->input->post("key_code");
        $key_code = 'my-key_code';

        // $meta_id = $this->input->post("meta_id");
        $meta_id = 'my-meta_id';

        // // Check key code exists
        // if (empty($key_code) || !isset($key_code)) {
        //     // Handle update key code for group
        //     $key_code = time() . $group_id;
        //     $this->Items_model->update(['key_code' => $key_code], $group_id);
        // }

        if (!empty($_FILES['image']['name'])) {
            $_FILES['file']['name'] = $_FILES['image']['name'];
            $_FILES['file']['type'] = $_FILES['image']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
            $_FILES['file']['error'] = $_FILES['image']['error'];
            $_FILES['file']['size'] = $_FILES['image']['size'];

            $originalFileName = $_FILES['image']['name'];
            $hashedFileName = hash('sha256', $originalFileName . time());

            $config['file_name'] = $hashedFileName;
            $config['upload_path'] = "uploads/";

            // Check folder exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path']);
            }

            $config['allowed_types'] = explode("|", 'jpg|jpeg|png|gif|pdf|svg|doc|docx|xls|xlsx|ppt|pptx|sheet|rar|zip');
            $config['max_size'] = 50000;

            $filename_explode =  explode(".", $originalFileName);
            $fileType = $filename_explode[count($filename_explode) - 1];

            if (!in_array($fileType, $config['allowed_types'])) {
                echo json_encode(array('success' =>  false, 'data' => "Tệp tin không đúng định dạng!"));
                die();
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];

                $data['filename'] = $filename;
                $data['key'] = $key;
                $data['title'] = $originalFileName;
                $data['type'] = $fileType;
                $data['meta_id'] = $meta_id;
                $data['item_id'] = $item_id;
                $data['content_type'] = $_FILES['file']['type'];

                // Handle encrypt file
                $file_name_enc = $hashedFileName . '.enc';
                $upload_path = $config['upload_path'];

                $this->imageencryption->encryptImage(
                    $uploadData['full_path'],
                    "$upload_path/$file_name_enc",
                    $this->config->item("image_key") //. $key_code
                );

                unlink($this->upload->data('full_path'));

                $data['path'] = $config['upload_path'] . $file_name_enc;

                $todo['image'] = $config['upload_path'] . $file_name_enc;
            }
        }
        $todo['name'] = $this->input->post('name');
        $todo['description'] = $this->input->post('description');
        $todo['priority'] = $this->input->post('priority');

        $this->Todo_model->add($todo);
        redirect('todo');
    }

    public function getImage()
    {
        $this->load->library('imageencryption');

        $file_name = $_GET['image'];
        $content = $this->imageencryption->decryptImage(
            $file_name,
            $this->config->item("image_key")
        );

        header("Content-Type: jpg");
        echo $content;
    }

    public function adminview($user_id, $file_name)
    {
        $this->load->library('imageencryption');

        $file_name = urldecode($file_name);

        $filename_dec = $this->stringencryption->decryptString($file_name, $this->config->item("image_key"));



        $file = $this->db->get_where("file", ['file_name' => $filename_dec])->row_object();

        // $application_no = $this->db->get_where("application", ["user_id" => $user_id])->row_object()["application_no"];

        // $application_no = $this->Application_model->find_by_user_id($user_id)->application_no;

        //$filePath = "uploads/" . $application_no . "/" . $filename_dec;

        $content = $this->imageencryption->decryptImage(
            $file->path,
            $this->config->item("image_key") . $user_id
        );

        header("Content-Type: $file->content_type");
        echo $content;
    }

    public function edit($todo_id)
    {
        $todo['user_id'] = 1;
        // $key = $this->input->post("key");
        $key = 'my-key';

        // $item_id = $this->input->post("item_id");
        $item_id = 'my-item_id';

        // $key_code = $this->input->post("key_code");
        $key_code = 'my-key_code';

        // $meta_id = $this->input->post("meta_id");
        $meta_id = 'my-meta_id';

        // // Check key code exists
        // if (empty($key_code) || !isset($key_code)) {
        //     // Handle update key code for group
        //     $key_code = time() . $group_id;
        //     $this->Items_model->update(['key_code' => $key_code], $group_id);
        // }

        if (!empty($_FILES['image']['name'])) {
            $_FILES['file']['name'] = $_FILES['image']['name'];
            $_FILES['file']['type'] = $_FILES['image']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
            $_FILES['file']['error'] = $_FILES['image']['error'];
            $_FILES['file']['size'] = $_FILES['image']['size'];

            $originalFileName = $_FILES['image']['name'];
            $hashedFileName = hash('sha256', $originalFileName . time());

            $config['file_name'] = $hashedFileName;
            $config['upload_path'] = "uploads/";

            // Check folder exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path']);
            }

            $config['allowed_types'] = explode("|", 'jpg|jpeg|png|gif|pdf|svg|doc|docx|xls|xlsx|ppt|pptx|sheet|rar|zip');
            $config['max_size'] = 50000;

            $filename_explode =  explode(".", $originalFileName);
            $fileType = $filename_explode[count($filename_explode) - 1];

            if (!in_array($fileType, $config['allowed_types'])) {
                echo json_encode(array('success' =>  false, 'data' => "Tệp tin không đúng định dạng!"));
                die();
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];

                $data['filename'] = $filename;
                $data['key'] = $key;
                $data['title'] = $originalFileName;
                $data['type'] = $fileType;
                $data['meta_id'] = $meta_id;
                $data['item_id'] = $item_id;
                $data['content_type'] = $_FILES['file']['type'];

                // Handle encrypt file
                $file_name_enc = $hashedFileName . '.enc';
                $upload_path = $config['upload_path'];

                $this->imageencryption->encryptImage(
                    $uploadData['full_path'],
                    "$upload_path/$file_name_enc",
                    $this->config->item("image_key") //. $key_code
                );

                unlink($this->upload->data('full_path'));

                $data['path'] = $config['upload_path'] . $file_name_enc;

                $todo['image'] = $config['upload_path'] . $file_name_enc;
            }
        }
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

    public function findByName()
    {
        $name = $this->input->post('name');
        $todo = $this->Todo_model->findByName($name);

        return $this->load->view('todo', $todo);
    }

    public function userUpdate()
    {
        $user_id = $_SESSION['user_id'];
        // $key = $this->input->post("key");
        $key = 'my-key';

        // $item_id = $this->input->post("item_id");
        $item_id = 'my-item_id';

        // $key_code = $this->input->post("key_code");
        $key_code = 'my-key_code';

        // $meta_id = $this->input->post("meta_id");
        $meta_id = 'my-meta_id';

        // // Check key code exists
        // if (empty($key_code) || !isset($key_code)) {
        //     // Handle update key code for group
        //     $key_code = time() . $group_id;
        //     $this->Items_model->update(['key_code' => $key_code], $group_id);
        // }

        if (!empty($_FILES['image']['name'])) {
            $_FILES['file']['name'] = $_FILES['image']['name'];
            $_FILES['file']['type'] = $_FILES['image']['type'];
            $_FILES['file']['tmp_name'] = $_FILES['image']['tmp_name'];
            $_FILES['file']['error'] = $_FILES['image']['error'];
            $_FILES['file']['size'] = $_FILES['image']['size'];

            $originalFileName = $_FILES['image']['name'];
            $hashedFileName = hash('sha256', $originalFileName . time());

            $config['file_name'] = $hashedFileName;
            $config['upload_path'] = "uploads/";

            // Check folder exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path']);
            }

            $config['allowed_types'] = explode("|", 'jpg|jpeg|png|gif|pdf|svg|doc|docx|xls|xlsx|ppt|pptx|sheet|rar|zip');
            $config['max_size'] = 50000;

            $filename_explode =  explode(".", $originalFileName);
            $fileType = $filename_explode[count($filename_explode) - 1];

            if (!in_array($fileType, $config['allowed_types'])) {
                echo json_encode(array('success' =>  false, 'data' => "Tệp tin không đúng định dạng!"));
                die();
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];

                $data['filename'] = $filename;
                $data['key'] = $key;
                $data['title'] = $originalFileName;
                $data['type'] = $fileType;
                $data['meta_id'] = $meta_id;
                $data['item_id'] = $item_id;
                $data['content_type'] = $_FILES['file']['type'];

                // Handle encrypt file
                $file_name_enc = $hashedFileName . '.enc';
                $upload_path = $config['upload_path'];

                $this->imageencryption->encryptImage(
                    $uploadData['full_path'],
                    "$upload_path/$file_name_enc",
                    $this->config->item("image_key") //. $key_code
                );

                unlink($this->upload->data('full_path'));

                $data['path'] = $config['upload_path'] . $file_name_enc;

                $user['image'] = $config['upload_path'] . $file_name_enc;
            }
        }
        $user['firstname'] = $this->input->post('firstname');

        $user['lastname'] = $this->input->post('lastname');

        $this->User_model->edit($user, $user_id);

        $_SESSION['image'] = $config['upload_path'] . $file_name_enc;
        $_SESSION['lastname'] = $this->input->post('lastname');
        $_SESSION['firstname'] = $this->input->post('firstname');

        redirect('todo');
    }

    public function getTodoByPriority()
    {
        $name = $this->input->post('name');
        $priority_id = $_GET['priority'];

        // if (!empty($name)) {
        //     $todo = $this->Todo_model->findByName($name);
        // } else {
        //     $todo = $this->Todo_model->getTodoPriority($priority_id);
        // }
        $todo = $this->Todo_model->getTodoPriority($priority_id);

        if (!empty($todo)) {
            $no = 1;
            foreach ($todo as $item): ?>
                <tr>
                    <td><?php echo $no++;
                        ?></td>
                    <td><img src="<?php echo base_url('todo/getImage?image=') . $item->image; ?>" alt="" width="100" height="100"></td>
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

                        public function loadTodo()
                        {
                            $todo = $this->Todo_model->getAll();
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
