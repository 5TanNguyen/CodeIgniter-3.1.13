<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @property Todo_model $Todo_model
 * @property User_model $User_model
 * @property Calendar_model $Calendar_model
 * 
 */
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
        $this->load->model('Calendar_model');
        $this->load->helper('base_helper');

        // Update Code

        if (class_exists('Format')) {
            $this->format = new Format();
        } else {
            $this->load->library('Format', NULL, 'libraryFormat');
            $this->format = $this->libraryFormat;
        }
    }

    function dd($data)
    {
        if (is_array($data)) {
            print_r('<pre>');
            print_r($data);
            die();
        } else {
            var_dump($data);
            die();
        }
    }

    public function index()
    {
        if (!isset($_SESSION['email'])) {
            $this->load->view('login');
        } else {
            $this->load->view('todo/todo');
        }
    }

    public function chart()
    {
        $this->load->view('todo/todo_chart');
    }

    public function calendar()
    {
        $calendar['calendar'] = $this->Calendar_model->getAll();

        $this->load->view('calendar/calendar', $calendar);
    }

    public function calendar_add()
    {
        $calendar['title'] = $this->input->post('title');
        $calendar['description'] = $this->input->post('description');
        $calendar['start'] = $this->input->post('start');
        $calendar['end'] = $this->input->post('end');

        $this->Calendar_model->add($calendar);
        redirect('todo/calendar');
    }

    public function import_excel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $upload_status = $this->uploadDoc();
            if ($upload_status != false) {
                $inputFileName = 'assets/uploads/imports/' . $upload_status;

                $inputTileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputTileType);
                $spreadsheet = $reader->load($inputFileName);
                $sheet = $spreadsheet->getSheet(0);

                $count_Rows = 0;

                foreach ($sheet->getRowIterator() as $row) {
                    $user_id = $spreadsheet->getActiveSheet()->getCell('A' . $row->getRowIndex());
                    $name = $spreadsheet->getActiveSheet()->getCell('B' . $row->getRowIndex());
                    $description = $spreadsheet->getActiveSheet()->getCell('C' . $row->getRowIndex());
                    $priority = $spreadsheet->getActiveSheet()->getCell('D' . $row->getRowIndex());

                    $data = array(
                        'user_id' => $user_id,
                        'name' => $name,
                        'description' => $description,
                        'priority' => $priority,
                    );

                    $this->Todo_model->add($data);
                }
                $this->session->set_flashdata('success', 'Successfully Data Imported!!!');
                redirect('todo');
            } else {
                $this->session->set_flashdata('error', 'File is not uploaded!!');
                redirect('todo');
            }
        } else {
            $this->load->view('todo');
        }
    }

    public function uploadDoc()
    {
        $uploadPath = 'assets/uploads/imports/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, TRUE); // FOR CREATING DIRECTORY IF ITS NOT EXIST
        }

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'csv|xlsx|xls';
        $config['max-size'] = 100000;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload('upload_excel')) {
            $fileData = $this->upload->data();
            return $fileData['file_name'];
        } else {
            return false;
        }
    }

    public function excel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (range('A', 'F') as $columID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutoSize(true);
        }
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'UserID');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'Description');
        $sheet->setCellValue('E1', 'Priority');

        $user_id = $_SESSION['user_id'];
        $x = 2;
        $todos = $this->Todo_model->getAllExcel();

        foreach ($todos as $row) {
            $sheet->setCellValue('A' . $x, $row['id']);
            $sheet->setCellValue('B' . $x, $row['user_id']);
            $sheet->setCellValue('C' . $x, $row['name']);
            $sheet->setCellValue('D' . $x, $row['description']);
            $sheet->setCellValue('E' . $x, $row['priority']);
            $x++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'todo_list.xlsx';

        $date = date('Y-m-d H:i:s');
        $path = 'exports/' . $fileName;
        $writer->save($path);

        $this->load->view('todo');
    }

    public function validate()
    {
        $email = $this->input->post('email');

        $user = $this->User_model->findByEmail($email);

        $_SESSION['user_id'] = $user[0]->id;
        $_SESSION['email'] = $user[0]->email;
        $_SESSION['firstname'] = $user[0]->firstname;
        $_SESSION['lastname'] = $user[0]->lastname;
        $_SESSION['user_image'] = $user[0]->user_image;

        $this->load->view('todo/todo');
    }

    public function logout()
    {
        // remove all session variables
        session_unset();

        // destroy the session
        session_destroy();

        $this->load->view('login');
    }

    public function add()
    {
        $todo['user_id'] = 1;
        $key = 'my-key';

        $item_id = 'my-item_id';

        $key_code = 'my-key_code';

        $meta_id = 'my-meta_id';

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
            // $config['max_size'] = 50000;

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
        $key = 'my-key';
        $item_id = 'my-item_id';
        $key_code = 'my-key_code';
        $meta_id = 'my-meta_id';

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

                $file_name_enc = $hashedFileName . '.enc';
                $upload_path = $config['upload_path'];

                $this->imageencryption->encryptImage(
                    $uploadData['full_path'],
                    "$upload_path/$file_name_enc",
                    $this->config->item("image_key") //. $key_code
                );

                unlink($this->upload->data('full_path'));

                $data['path'] = $config['upload_path'] . $file_name_enc;

                $user['user_image'] = $config['upload_path'] . $file_name_enc;
            }
        }
        $user['firstname'] = $this->input->post('firstname');

        $user['lastname'] = $this->input->post('lastname');

        $this->User_model->edit($user, 1);

        $_SESSION['user_image'] = $config['upload_path'] . $file_name_enc;
        $_SESSION['image'] = $originalFileName;
        $_SESSION['lastname'] = $this->input->post('lastname');
        $_SESSION['firstname'] = $this->input->post('firstname');

        redirect('todo');
    }

    public function getTodoByPriority()
    {

        $code = $this->Todo_model->getAllCodeTodoMeta();
        $todo_empty = [];

        // Cài đặt index code, tên cột cho todometa
        $todo_metas = [];
        $columnName = [];
        foreach ($code as $key => $item) {
            $todo_metas[$key] = $item['code'];
            $columnName[$key] = $item['fieldname'];
        }

        $priority_id = $_GET['priority'];
        $todo = $this->Todo_model->getTodoPriority($priority_id);

        $todo_check = [];
        $statusArray = [
            1 => 'Chưa bắt đầu',
            2 => 'Đang làm',
            3 => 'Hoàn thành',
        ];

        $statusArrayColor = [
            1 => '#48dbfb',
            2 => '#f6e58d',
            3 => '#2ecc71',
        ];
        foreach ($todo as $key => $item) {
            // Tạo dừng dòng dữ liệu todo
            $temp_id = $item['id'];
            if (!isset($todo_check[$temp_id])) {
                $todo_check[$temp_id] = [
                    'id' => $item['id'],
                    'image' => $item['image'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'priority' => $item['priority'],
                    'status' => $item['status'] ?? 1,
                    'statusText' => $statusArray[$item['status'] ?? 1],
                    'date' => $item['date'],
                ];
            }

            // Set empty các todometa cho todo
            if (!in_array($temp_id, $todo_empty)) {
                foreach ($code as $key => $cod) {
                    $todo_check[$temp_id]['fields'][$key] = [
                        'todo_meta_id' => '',
                        'code' => '',
                        'fieldname' => '',
                        'fieldvalue' => ''
                    ];
                }
                array_push($todo_empty, $temp_id);
            }

            // Lấy chỉ số code của todometa
            $code_index = array_search($item['code'], $todo_metas);
            // Set các giá trị của todometa trong cột field
            $todo_check[$temp_id]['fields'][$code_index] = [
                'todo_meta_id' => $item['todo_meta_id'],
                'code' => $item['code'],
                'fieldname' => $item['fieldname'],
                'fieldvalue' => $item['fieldvalue']
            ];
        }

        // dd($todo_check);
        if (!empty($todo_check)) { ?>

            <!-- Mở thẻ table -->
            <div style="overflow-x: auto; max-width: 100%; border: 1px solid #ccc;">
                <table border="1" cellpadding="10" cellspacing="0" class="table" id="todo-table" style="width: 800px;">
                    <!-- Tạo phần tiêu đề của bảng -->
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Image</th>
                            <th style="position: sticky; left: 0; background-color: white; z-index: 2;">Name</th>
                            <th>Description</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            <?php foreach ($columnName as $todo_meta): ?>
                                <th><?= $todo_meta ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todo_check as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td style="position: relative; text-align: center;">
                                    <img src="<?php echo base_url('todo/getImage?image=') . $item['image']; ?>" alt="" width="100" height="100">
                                    <div style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px;">
                                        <a type="button" class="btn btn-warning"
                                            style="width: 48%; opacity: 0.7; transition: opacity 0.3s;"
                                            onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0.7';"
                                            onclick="fillData(`<?php echo $item['id']; ?>`, `<?php echo $item['name']; ?>`, `<?php echo $item['description']; ?>`, `<?php echo $item['priority']; ?>`)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a type="button" href="<?php echo base_url(); ?>todo/delete/<?php echo $item['id'] ?>"
                                            class="btn btn-danger"
                                            style="width: 48%; opacity: 0.7; transition: opacity 0.3s;"
                                            onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0.7';"
                                            onclick="return confirm('You want to delete this todo?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>

                                <td style="position: sticky; left: 0; background-color: white; z-index: 2;"><input type="text" value="<?= $item['name']; ?>" class="no-border todo_input" data-id="<?= $item['id'] ?>" data-field="name"></td>
                                <td><input type="text" value="<?= $item['description']; ?>" class="no-border todo_input" data-id="<?= $item['id'] ?>" data-field="description"></td>
                                <td>
                                    <select name="todo_input" class="todo_input form-control" data-id="<?= $item['id'] ?>" data-field="priority" data-status="<?= $item['id'] ?>">
                                        <option value="<?= $item['priority']; ?>"><?= $item['priority']; ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="<?= 'status' . $item['id'] ?>" style="background-color: <?= $statusArrayColor[$item['status']] ?>;" class="no-border todo_input" data-id="<?= $item['id'] ?>" data-field="status">
                                        <option style="background-color: <?= $statusArrayColor[$item['status']] ?>;" value="<?= $item['status']; ?>"><?= $item['statusText']; ?></option>
                                        <option style="background-color: #48dbfb;" value="1">Chưa bắt đầu</option>
                                        <option style="background-color: #f6e58d;" value="2">Đang làm</option>
                                        <option style="background-color: #2ecc71;" value="3">Hoàn thành</option>
                                    </select>
                                </td>
                                <td><input type="date" value="<?= $item['date']; ?>" class="no-border todo_input" data-id="<?= $item['id'] ?>" data-field="date"></td>

                                <!-- Hiển thị các fields của todo_meta -->
                                <?php foreach ($item['fields'] as $val): ?>
                                    <!-- <php if (!$val['fieldvalue']): ?> -->
                                    <td><input type="text" value="<?= $val['fieldvalue'] ?>" class="no-border todo_meta_input" data-id="<?= $val['todo_meta_id'] ?>" data-field="fieldvalue" style="width: 100px;"></td>
                                    <!-- <php endif; ?> -->
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
                $(document).ready(function() {
                    $(".todo_input").change(function() {
                        var todo_id = $(this).data('id');
                        var todo_field = $(this).data('field');
                        var value = $(this).val(); // Lấy giá trị từ input

                        // Thao tác status
                        var statusArrayColor = {
                            1: '#48dbfb',
                            2: '#f6e58d',
                            3: '#2ecc71',
                        }
                        // Thao tác status

                        // Gửi dữ liệu qua Ajax
                        $.ajax({
                            url: '<?php echo base_url('todo/updateTodoOnChange') ?>',
                            type: 'POST',
                            data: {
                                todoId: todo_id,
                                todoValue: value,
                                todoField: todo_field
                            },
                            success: function(response) {
                                // loadTodoPriority();
                                datatableCallAjax();

                                // Thao tác status
                                if (todo_field === 'status') {
                                    // let select = $(`[data-id="${todo_id}"][data-field="status"]`);
                                    let select = $(`#status${todo_id}`);

                                    // Đặt màu nền ban đầu theo giá trị hiện tại của select
                                    const initialColor = statusArrayColor[select.val()] || '#fff';
                                    select.css('background-color', initialColor);

                                    select.on('change', function() {
                                        const newColor = $(this).val();
                                        $(this).css('background-color', statusArrayColor[newColor] || '#fff');
                                    });
                                }
                                // Thao tác status
                            },
                            error: function() {
                                alert('Lỗi rồi');
                            }
                        });
                    });

                    $(".todo_meta_input").change(function() {
                        var todo_meta_id = $(this).data('id');
                        var todo_meta_field = $(this).data('field');
                        var todo_meta_value = $(this).val(); // Lấy giá trị từ input

                        $.ajax({
                            url: '<?php echo base_url('todo/updateTodoMetaOnChange') ?>',
                            type: 'POST',
                            data: {
                                todoMetaId: todo_meta_id,
                                todoMetaField: todo_meta_field,
                                todoMetaValue: todo_meta_value,
                            },
                            success: function(response) {
                                // loadTodoPriority();
                                datatableCallAjax();
                            },
                            error: function() {
                                alert('Lỗi rồi');
                            }
                        });
                    });
                });
            </script>
            <!-- Đóng thẻ table -->

        <?php } else { ?>
            <tr>
                <td>There is no data</td>
            </tr>
<?php }
    }

    public function updateTodoOnChange()
    {
        $id = $this->input->post('todoId');
        $field = $this->input->post('todoField');
        $value = $this->input->post('todoValue');
        $succcess = $this->Todo_model->setValueField($id, $field, $value);
        return $succcess;
    }

    public function updateTodoMetaOnChange()
    {
        $id = $this->input->post('todoMetaId');
        $field = $this->input->post('todoMetaField');
        $value = $this->input->post('todoMetaValue');
        $succcess = $this->Todo_model->setValueFieldTodoMeta($id, $field, $value);
        return $succcess;
    }

    // Code Update
    // Hàm response để trả về phản hồi JSON
    public function responseGPT($data = [], $http_code = 200)
    {
        // Thiết lập tiêu đề cho phản hồi JSON
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($http_code) // Thiết lập mã HTTP
            ->set_output(json_encode($data)); // Encode dữ liệu phản hồi dưới dạng JSON
    }

    public function ajaxDataTable()
    {
        // canAccessMiddleware('tongiao');

        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');

        // $order = json_encode($_GET['order']);
        $searchValue = $this->input->get('searchValue');
        // $dm_ton_giao_ma = $this->input->get('dm_ton_giao_ma');
        // $created_at = $this->input->get('created_at');

        //  Replace API
        $todo_metas = [];
        // lấy các mã code trong todo_meta đã distinct
        $code = $this->Todo_model->getAllCodeTodoMeta();
        $todo_empty = [];
        foreach ($code as $key => $item) {
            $todo_metas[$key] = $item['code'];
        }

        // lấy danh sách todo đã join với todo_meta
        $data = $this->Todo_model->getTodo();

        $todo_check = [];
        $feildName = [];
        foreach ($data['data'] as $key => $item) {
            $temp_id = $item['id'];
            if (!isset($todo_check[$temp_id])) {
                $todo_check[$temp_id] = [
                    'id' => $item['id'],
                    'image' => $item['image'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'priority' => $item['priority']
                ];
            }

            if (!in_array($temp_id, $todo_empty)) {
                foreach ($code as $key => $cod) {
                    $todo_check[$temp_id]['fields'][$key] = [
                        'todo_meta_id' => '',
                        'code' => '',
                        'fieldname' => '',
                        'fieldvalue' => ''
                    ];
                }
            }
            if (!in_array($temp_id, $todo_empty)) {
                array_push($todo_empty, $temp_id);
            }


            $code_index = array_search($item['code'], $todo_metas);

            $todo_check[$temp_id]['fields'][$code_index] = [
                'todo_meta_id' => $item['todo_meta_id'],
                'code' => $item['code'],
                'fieldname' => $item['fieldname'],
                'fieldvalue' => $item['fieldvalue']
            ];

            if (!in_array($item['fieldname'], $feildName)) {
                array_push($feildName, $item['fieldname']);
            }
        }

        unset($data['data']);
        $data['data'] = array_values($todo_check);

        echo json_encode($data);
        die();

        $response = $this->responseGPT([
            'status' => 200,
            'message' => 'Success',
            'success' => true,
            'data' => $data
        ], 200);

        //  End Replace API

        // if ($response && $response['success'] == true) {
        //     $data = $response['data'];
        //     echo json_encode($data);
        // }
    }

    // API để lấy thông tin sinh viên
    public function get_student_info() //($id)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, DHNCT-Authorization, DHNCT-API-KEY");

        $student_info = $this->User_model->getProfile(1);

        if ($student_info) {
            // Trả về dữ liệu JSON
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($student_info));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Student not found']));
        }
    }

    public function update_profile_picture()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // $user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session
        $user_id = 1; // Lấy ID người dùng từ session

        if (!empty($_FILES['image']['name'])) {
            $originalFileName = $_FILES['image']['name'];

            // Cấu hình upload
            $config['upload_path'] = './uploads/profile_pictures/'; // Đường dẫn lưu file
            $config['allowed_types'] = 'jpg|jpeg|png|gif'; // Các định dạng file cho phép
            $config['max_size'] = 2048; // Kích thước tối đa 2MB

            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                // File đã được upload thành công
                $uploadData = $this->upload->data();
                $filePath = $config['upload_path'] . $uploadData['file_name']; // Lưu đường dẫn file

                // Cập nhật đường dẫn ảnh vào database
                $this->User_model->update_user_image($user_id, $filePath);

                echo json_encode(['success' => true, 'data' => $filePath]);
            } else {
                // Xử lý lỗi upload
                echo json_encode(['success' => false, 'error' => $this->upload->display_errors()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No file was uploaded.']);
        }
    }

    public function getAllTodoCalendar()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $todo = $this->Todo_model->getAll();

        $todo_rn = [];
        foreach ($todo as $td) {
            $todo_rn[$td['date']] = [
                'title' => $td['name'],
                'description' => $td['description']
            ];
        }

        if ($todo) {
            echo json_encode(['success' => true, 'data' => $todo_rn]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No todo.']);
        }
    }

    public function getAllTodoName()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $todo = $this->Todo_model->getAll();

        $todo_rn = [];
        foreach ($todo as $key => $td) {
            $todo_rn[] = [
                'id' => $key + 1,
                'task' => $td['name'],
            ];
        }

        if ($todo) {
            echo json_encode(['success' => true, 'data' => $todo_rn]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No todo.']);
        }
    }
}
