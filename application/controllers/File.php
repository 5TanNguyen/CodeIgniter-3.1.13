<?php
defined('BASEPATH') or exit('No direct script access allowed');

class File extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->authenticate->is_authenticated()) {
            redirect('/login');
        }
        $this->load->model("File_model");
        $this->load->model("Items_model");
        $this->load->library('ImageEncryption');
    }
    public function upload()
    {
        $key = $this->input->post("key");
        $item_id = $this->input->post("item_id");
        $project_id = $this->input->post("project_id");
        $key_code = $this->input->post("key_code");
        $group_id = $this->input->post("group_id");
        $meta_id = $this->input->post("meta_id");

        try {
            if (!$meta_id) {
                $meta_id = $this->Items_model->get_meta($item_id, $key)->id;
            }
        } catch (\Throwable $th) {
            echo json_encode(array('error' => 'Có lỗi xảy ra, vui lòng thử lại!'));
            die();
        }

        $data = array();
        $files_id = array();

        $count = count($_FILES['files']['name']);

        $fileType = null;

        // Check key code exists
        if (empty($key_code) || !isset($key_code)) {
            // Handle update key code for group
            $key_code = time() . $group_id;
            $this->Items_model->update(['key_code' => $key_code], $group_id);
        }

        for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES['files']['name'][$i])) {
                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                $originalFileName = $_FILES['files']['name'][$i];
                $hashedFileName = hash('sha256', $originalFileName . time());

                $config['file_name'] = $hashedFileName;
                $config['upload_path'] = "uploads/$project_id/";

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
                        $this->config->item("image_key") . $key_code
                    );

                    unlink($this->upload->data('full_path'));

                    $data['path'] = $config['upload_path'] . $file_name_enc;

                    $file_id = $this->File_model->add($data);
                    $files_id[] = $file_id;
                }
            }
        }

        $result = $this->File_model->update_meta($files_id, $meta_id, "add_file");

        $meta_updated = $this->Items_model->get_meta_by_id($meta_id);

        $project = $this->Items_model->find_by_id($project_id);
        $group = $this->Items_model->find_by_id($group_id);

        $file_html = $this->load->view("admin/views/components/file-meta", ['value' => $meta_updated->value, 'meta_id' => $meta_id, 'group' => $group, 'project' => $project], true);

        echo json_encode(array('success' =>  $result ? true : false, "file_html" => $file_html, "file_type" => $fileType));
    }

    public function update_meta()
    {
        $file_id = $this->input->post("file_id");
        $meta_id = $this->input->post("meta_id");
        $group_id = $this->input->post("group_id");
        $project_id = $this->input->post("project_id");

        $result = $this->File_model->update_meta([$file_id], $meta_id, "clear_file");

        $meta_updated = $this->Items_model->get_meta_by_id($meta_id);


        $project = $this->Items_model->find_by_id($project_id);
        $group = $this->Items_model->find_by_id($group_id);

        $data['value'] = $meta_updated->value;
        $data['meta_id'] = $meta_id;
        $data['project'] = $project;
        $data['group'] = $group;

        $file_html = $this->load->view("admin/views/components/file-meta", $data, true);

        echo json_encode(array('success' => $result ? true : false, "file_html" => $file_html));
    }
    public function view($folder_id, $id)
    {
        if ($this->check_is_owner($id)) {

            $data['project_item'] = $this->Items_model->find_by_id($id);

            $data['files'] = $this->File_model->get_files_by_item_id($id);

            $this->load->admin("admin/views/files", $data);
        } else {
            return redirect('/items');
        }
    }
    public function update($id)
    {
        if ($id) {

            $result = $this->File_model->update($this->input->post(), $id);

            echo json_encode(array("success" => $result ? true : false, "data" => $result));
        }
    }

    function check_is_owner($item_id)
    {
        if ($this->session->userdata('user_id')) {
            $item = $this->Items_model->find_by_id($item_id);
            if (!$item) {
                return false;
            }

            $groups = $this->Items_model->get_groups($item->id);

            foreach ($groups as $group) {
                $owners = explode(',', $group->owners);

                if (in_array($this->session->userdata('user_id'), $owners)) {
                    return true;
                }
            }

            $owners = explode(',', $item->owners);

            return in_array($this->session->userdata('user_id'), $owners) ? true : false;
        } else {
            return false;
        }
    }
    public function get_file()
    {
        $file_id = $this->input->post("file_id");

        try {
            $result = $this->File_model->get_file(['id' => $file_id]);

            echo json_encode(array("success" => $result ? true : false, "data" => $result));
        } catch (\Throwable $th) {
            show_error($th->getMessage());
        }
    }
    public function filter()
    {
        try {
            $project_id = $this->input->post("project_id");
            $filter = $this->input->post("filter");

            $result = $this->File_model->get_files_by_item_id($project_id, $filter);

            echo json_encode(array("success" => true, "data" => $result));
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
