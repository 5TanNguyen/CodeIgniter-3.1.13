<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Vanbang extends CI_Controller
{

        public $module = 'vanbang';

        public function __construct()
        {
                parent::__construct();
                $this->load->model('Cntt_van_bang_model');
                $this->load->model('Dot_import_van_bang_model');
                $this->load->model('Phan_quyen_model');
                $this->load->model('Cntt_menu_model');
                $this->load->model('Users_logs_model');
                $this->load->helper('url_helper');
                $this->load->helper(array('form', 'url'));
                $this->load->library('session');
                $this->load->library('form_validation');
                $this->module = "vanbang";
        }

        public function index()
        {
                /****
                 * Kiểm tra user đăng nhập
                 */
                /*if (!$this->session->userdata('user')) {
                        redirect('login');
                }*/
                $header['header_js'] = ['assets/DataTables/datatables.min.js', 'assets/script/dotthi_list_vanbang.js', 'assets/script/remove_dotthi.js', 'assets/script/dowload_dotthi.js'];
                $header['header_css'] = ['assets/style/tracuu.css'];

                $this->load->view('templates/header-admin', $header);
                $this->load->view('admin/vanbang/index');
                $this->load->view('templates/footer-admin', $header);
        }
        public function tracuu()
        {
                $header['header_js'] = ['assets/script/tracuuvb.js'];
                $header['header_css'] = ['assets/style/tracuu.css', 'assets/style/details-table.css'];
                $data['title'] = 'Tra cứu vanbang';

                if ($this->input->server('REQUEST_METHOD') === 'POST') {
                        $so_hieu_phoi = $this->input->post('so_hieu_phoi', TRUE);
                        $ho_ten = $this->input->post('ho_ten', TRUE);
                        $so_vao_so = $this->input->post('so_vao_so', TRUE);
                        $loai_dao_tao = $this->input->post('loai_dao_tao', TRUE);

                       // $recaptcha = $this->input->post('g-recaptcha-response', TRUE);

                        /*$this->form_validation->set_rules(
                                'so_hieu_phoi',
                                'Số Hiệu Phôi',
                                'required|min_length[3]|max_length[20]|regex_match[/^[0-9a-zA-Z.]+$/]',
                                [
                                        'required' => "%s không được bỏ trống",
                                        'min_length' => "%s không được ngắn hơn 3 ký tự",
                                        'max_length' => "%s không được dài hơn hơn 20 ký tự",
                                        'regex_match' => "%s chỉ chấp nhận số ký tự a-z và dấu chấm (.) "
                                ]
                        );*/
                        /*$this->form_validation->set_rules('g-recaptcha-response', 'Recaptcha', 'required', [
                                'required' => 'Không được bỏ qua %s'
                        ]);*/

                       // if ($this->form_validation->run() == TRUE) {
                                
                                $result = $this->Cntt_van_bang_model->find_vanbang($loai_dao_tao,$ho_ten,$so_hieu_phoi,$so_vao_so);
                                 echo var_dump($result);

                                if ($result != NULL) {
                                        $data['results'] = $result;
                                } else {
                                        $data['error'] = 'Không tìm thấy kết quả phù hợp';
                                }
                                $data['cccd'] = $so_hieu_phoi;
                                $data['hoten'] = $ho_ten;
                                $data['sovaoso'] =  $so_vao_so;
                        //}
                }
                $this->load->view('templates/header', $header);
                $this->load->view('vanbang/tracuu', $data);
                $this->load->view('templates/footer', $header);
        }

        public function view($slug = NULL)
        {
                $data['vanbang_item'] = $this->Cntt_van_bang_model->get_vanbang($slug);

                if (empty($data['vanbang_item'])) {
                        show_404();
                }

                $data['title'] = $data['vanbang_item']['title'];

                $this->load->view('templates/header', $data);
                $this->load->view('vanbang/view', $data);
                $this->load->view('templates/footer');
        }

        public function create()
        {
                $header['site_js'] = ['jquery-3.7.1.min.js'];
                $header['header_css'] = ['assets/style/tracuu.css'];
                $title = $this->input->post('title');

                $this->form_validation->set_rules(
                        'title',
                        'tên đợt import',
                        'required|min_length[3]|max_length[50]',
                        [
                                'required' => "%s không được bỏ trống",
                                'min_length' => "%s không được ngắn hơn 3 ký tự",
                                'max_length' => "%s không được dài hơn hơn 50 ký tự",

                        ]
                );
                if ($this->form_validation->run() == FALSE) {

                        $this->load->view('templates/header-admin', $header);
                        $this->load->view('admin/vanbang/create');
                        $this->load->view('templates/footer-admin', $header);
                } else {



                        if ($this->Dot_import_van_bang_model->save_data(array('ten_dot' => $title))) {
                                $this->session->set_flashdata('toast_message', ['title' => "Thông báo", 'content' => "Thêm đợt import thành công", 'show' => true]);
                                $this->Users_logs_model->add($_SESSION['uid'], 'add_vanbang', 'dot_thi_vanbang',$_SESSION['ip']);
                                redirect('admin/vanbang');
                        } else {
                                $data['error'][] = "Thêm đợt import bị lỗi, vui lòng kiểm tra lại" . $title;
                        }

                        $this->load->view('templates/header-admin', $header);
                        $this->load->view('admin/vanbang/create', $data);
                        $this->load->view('templates/footer-admin', $header);
                }
        }

        function import()
        {
                $this->load->helper('form');
                $data['dotthi'] = $this->Dot_import_van_bang_model->get_all();
                $data['dotthi_chon'] = $this->input->get('dotthi_chon');
                $header['header_js'] = ['assets/select2/js/select2.js', 'assets/script/cntt_vanbang_import.js', 'assets/script/select2-chooser.js'];
                $header['header_css'] = ['assets/select2/css/select2.css', 'assets/style/tracuu.css'];
                $data['error'] = [];

                if ($this->input->server('REQUEST_METHOD') === 'POST') {

                        $dotthi = $this->input->post('dotthi');
                        $data['dotthi_chon'] = $dotthi;
                        if (isset($dotthi)) {
                                $config['upload_path']          = './uploads/';
                                $config['allowed_types']        = 'xls|xlsx';
                                $config['max_size']             = 6000;


                                $this->load->library('upload', $config);

                                if (!$this->upload->do_upload('userfile')) {
                                        $error_file = $this->upload->display_errors();
                                        if ($error_file) {
                                                $data['error'][] = "Lỗi file hoặc chưa chọn file";
                                        }
                                } else {
                                        //  $dotthi =  $this->Dot_import_van_bang_model->save_data(array('ten_dot' => $dotthi));
                                        $data_file = array('upload_data' => $this->upload->data());


                                        $this->load->library("pxl");

                                        $inputFileName = $data_file['upload_data']['full_path'];

                                        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                                        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//echo var_dump($sheetData);
//die();
                                        $key_arr = [
                                                'stt',
                                                'ho_ten',
                                                'gioi_tinh',
                                                'ngay_sinh',
                                                'nghanh_dao_tao',
                                                'xep_loai_tot_nghiep',
                                                'hinh_thuc_dao_tao',
                                                'so_vao_so',
                                                'so_hieu',
                                                'so_quyet_dinh',
                                                'ngay_ban_hanh',
                                                
                                        ];
                                        $objPHPExcel->setActiveSheetIndex(0);
                                        $count_import_success = 0;
                                        $count_import_faild = 0;
                                        $this->db->trans_start();
                                        for ($row = 2; $row < count($sheetData) + 1; $row++) {
                                                $data_import = array();

                                                for ($col = 0; $col < count($key_arr); $col++) {

                                                        $data_import[$key_arr[$col]] = html_entity_decode($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue(), ENT_COMPAT, 'UTF-8');
                                                }

                                                $data_import['id_dot_import'] = $dotthi;
                                                try {

                                                        //echo json_encode($data_import);
                                                        if (!$this->Cntt_van_bang_model->save_data($data_import)) {

                                                                $error_db = $this->db->error(); // Has keys 'code' and 'message'   
                                                                if ($error_db) {
                                                                        if ($error_db['code'] == 1062) $data['error'][] = 'Dữ liệu bị trùng lập, Dữ liệu có trường STT : ' . $data_import['stt'];
                                                                        else
                                                                                $data['error'][] = 'Dữ liệu đầu vào bị lỗi, xin vui lòng kiểm tra lại';
                                                                }
                                                                $count_import_success = 0;
                                                        } else {
                                                                $count_import_success++;
                                                        }
                                                } catch (Exception $e) {
                                                        $data['error'][] = $e;
                                                        unlink($inputFileName);
                                                        $count_import_success = 0;
                                                }
                                        }

                                        $this->db->trans_complete();
                                        if ($this->db->trans_status() === TRUE) {
                                                $soluong = $this->Dot_import_van_bang_model->find_by_id($dotthi)['so_luong'];
                                                $soluong = $count_import_success + (int)($soluong);
                                                $this->Dot_import_van_bang_model->do_update($dotthi, array('so_luong' => $soluong));
                                                $this->session->set_flashdata('toast_message', ['title' => "Thông báo", 'content' => "Nhập đợt import thành công, " . $count_import_success . " dòng đã được thêm.", 'show' => true]);
                                                $this->Users_logs_model->add($_SESSION['uid'], 'import_vanbang', 'vanbang',$_SESSION['ip']);
                                        }

                                        $data['count_import_success'] = $count_import_success;
                                        $data['count_import_faild'] = $count_import_faild;
                                        unlink($inputFileName);
                                }
                        } else {
                                $data['error'][] = 'Chưa chọn đợt import';
                        }
                }

                //echo $data['error'];
                $this->load->view('templates/header-admin', $header);
                $this->load->view('admin/vanbang/import', $data);
                $this->load->view('templates/footer-admin', $header);
        }
        public function export()
        {
                $this->load->helper('form');
                $dotthi = $this->input->post('dotthi');
                $data['dotthi'] = $this->Dot_import_van_bang_model->get_all();
                $data['dotthi_chon'] = $dotthi;
                $data['error'] = [];
                $header['header_css'] = [];
                if ($dotthi) {
                        $this->do_export();
                        $this->Users_logs_model->add($_SESSION['uid'], 'export_vanbang', 'vanbang',$_SESSION['ip']);
                }
                $this->load->view('templates/header-admin', $header);
                $this->load->view('admin/vanbang/export', $data);
                $this->load->view('templates/footer-admin', $header);
        }
        public function delete()
        {
                $dotthi = $this->input->post('dti_id');
                if ($this->Dot_import_van_bang_model->remove_by_id($dotthi))
                        $this->session->set_flashdata('toast_message', ['title' => "Thông báo", 'content' => "Xóa đợt import thành công", 'show' => true]);
                $this->Users_logs_model->add($_SESSION['uid'], 'delete_vanbang', 'vanbang,dot_thi_vanbang',$_SESSION['ip']);
                redirect('admin/vanbang');
        }
        public function do_export()
        {
                $dotthi = $this->input->post('dotthi');
                $this->load->library("pxl");
                //Create a new Object
                $objPHPExcel = new PHPExcel();
                // Set the active Excel worksheet to sheet 0
                $objPHPExcel->setActiveSheetIndex(0);

                $i = 0;
                $heading = array(
                        'STT',
                        'Họ và tên',
                        'Giới tính',
                        'Ngày sinh',
                        'Nghành đào tạo',
                        'Xếp loại tốt nghiệp',
                        'Hình thức đào tạo',
                        'Số vào sổ cấp bằng',
                        'Số hiệu văn bằng',
                        'Số QĐ tốt nghiệp',
                        'Ngày ban hành',
                ); //set title in excel sheet


                for ($col = 'A'; $col <= 'Z'; $col++) {
                        if ($i == 48) {
                                break;
                        }

                        $objPHPExcel->getActiveSheet()
                                ->getColumnDimension($col)
                                ->setAutoSize(true);
                        $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('A1:AV1')->getFont()->setBold(true);


                $rowNumberH = 1; //set in which row title is to be printed
                $colH = 'A'; //set in which column title is to be printed
                foreach ($heading as $h) {
                        $objPHPExcel->getActiveSheet()->setCellValue($colH . $rowNumberH, $h);

                        $colH++;
                }



                $term = ['id_dot_import' => $dotthi];
                $export_excel =  $this->Cntt_van_bang_model->get_vanbang($term);

                $tendotthi = $this->Dot_import_van_bang_model->find_by_id($dotthi)['ten_dot'];
               
                $rowCount = 2; // set the starting row from which the data should be printed
                foreach ($export_excel as $i => $excel) {


                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $rowCount, $i + 1);
                        $colH = 'B';
                        foreach ($excel as $key => $val) {
                                if (!in_array($key, ['id', 'id_dot_import', 'stt'])) {
                                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $val);
                                        // echo json_encode($excel);
                                        $colH++;
                                }
                        }
                        $rowCount++;
                }
                // Instantiate a Writer 

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $tendotthi . '.xls"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit();
        }
        public function do_delete_data()
        {
                $dotthi = $this->input->post('dti_id');
                $this->Cntt_van_bang_model->delete_vanbang_by_dotthi($dotthi);
                $this->Dot_import_van_bang_model->do_update($dotthi, array('so_luong' => 0));
                $this->session->set_flashdata('toast_message', ['title' => "Thông báo", 'content' => "Xóa dữ liệu đợt import thành công", 'show' => true]);
                $this->Users_logs_model->add($_SESSION['uid'], 'delete_data_vanbang', 'vanbang',$_SESSION['ip']);
                redirect('admin/vanbang');
        }
        public function delete_any()
        {
                $ids = $this->input->post('ids');
                $count = $this->Dot_import_van_bang_model->delete_any($ids);
                $this->session->set_flashdata('toast_message', ['title' => "Thông báo", 'content' => "Xóa nhiều đợt import thành công", 'show' => true]);
                $this->Users_logs_model->add($_SESSION['uid'], 'delete_any_vanbang', 'vanbang,dot_thi_vanbang',$_SESSION['ip']);
                // redirect('admin/vanbang');
        }

        function get_list()
        {
                $draw = $this->input->get('draw', TRUE);
                $start = $this->input->get('start', TRUE);
                $length = $this->input->get('length', TRUE);
                $length = $this->input->get('length', TRUE);
                $search = $this->input->get('search[value]', TRUE);

                $data['all'] = $this->Dot_import_van_bang_model->get_all();

                if ($search) {
                        $data['dot_thi'] = $this->Dot_import_van_bang_model->find($search, (int) $start, (int) $length);
                } else {
                        $data['dot_thi'] = $this->Dot_import_van_bang_model->get_list((int) $start, (int) $length);
                }

                $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(
                                [
                                        "draw" => $draw,
                                        "recordsTotal" => count($data['all']),
                                        "recordsFiltered" => count($data['all']),
                                        "data" => $data['dot_thi']
                                ]
                        ));
        }

        function do_update()
        {
                $id = $this->input->post("id");
                $congkhai = $this->input->post("congkhai");
                $this->Users_logs_model->add($_SESSION['uid'], 'update_cong_khai_vanbang', 'dot_import_van_bang',$_SESSION['ip']);
                return $this->Dot_import_van_bang_model->do_update($id, array("cong_khai" => $congkhai));
        }
}
