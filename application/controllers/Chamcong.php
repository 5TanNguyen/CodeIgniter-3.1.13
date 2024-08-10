<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chamcong extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('form_validation');
    }
    public function hoptac()
    {
        $header['css'] = [];
        $header['js'] = [];
        $data = [];
        $this->form_validation->set_rules(
            'thang',
            'Tháng',
            'required|max_length[50]',
            [
                'required' => "%s không được bỏ trống",
                'max_length' => "%s không được dài hơn hơn 50 ký tự",

            ]
        );
        $this->form_validation->set_rules(
            'nam',
            'Năm ',
            'required|max_length[50]',
            [
                'required' => "%s không được bỏ trống",
                'max_length' => "%s không được dài hơn hơn 50 ký tự",

            ]
        );
        if ($this->form_validation->run() == FALSE) {
        } else {
        }
        $this->load->view('templates/header-page', $header);
        $this->load->view('chamcong/hoptac', $data);
        $this->load->view('templates/footer-page', $header);
    }
    public function cohuu()
    {
        $header['css'] = [];
        $header['js'] = [];
        $data = [];
        $this->form_validation->set_rules(
            'thang',
            'Tháng',
            'required|max_length[50]',
            [
                'required' => "%s không được bỏ trống",
                'max_length' => "%s không được dài hơn hơn 50 ký tự",

            ]
        );
        $this->form_validation->set_rules(
            'nam',
            'Năm ',
            'required|max_length[50]',
            [
                'required' => "%s không được bỏ trống",
                'max_length' => "%s không được dài hơn hơn 50 ký tự",

            ]
        );
        if ($this->form_validation->run() == FALSE) {
        } else {
        }
        $this->load->view('templates/header-page', $header);
        $this->load->view('chamcong/cohuu', $data);
        $this->load->view('templates/footer-page', $header);
    }
    public function hoptac_export()
    {
        $m = $this->input->post("thang");
        $y = $this->input->post("nam");
        $d = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        $start_day = $y . $m . "010000";
        $end_day = $y . $m . $d . "2359";
        $sql = "SELECT DISTINCT nv.HO_TEN,nv.MACCHN,xm.MA_NHOM,xm.TEN_DICH_VU,xm.NGAY_YL,xm.NGAY_KQ FROM `xml3` xm inner join nhanvien nv on find_in_set(nv.MACCHN,REPLACE(xm.MA_BAC_SI,';',',')) != 0 WHERE nv.HOP_TAC = 1 AND CONVERT(xm.NGAY_YL,UNSIGNED) BETWEEN " . intval($start_day) . " AND " . intval($end_day); // . " ORDER BY nv.MACCHN ASC, xm.NGAY_YL ASC";
        $sql .= " UNION SELECT DISTINCT nv.HO_TEN,nv.MACCHN,xm.MA_NHOM,xm.TEN_THUOC as TEN_DICH_VU,xm.NGAY_YL,xm.NGAY_YL as NGAY_KQ FROM `xml2` xm inner join nhanvien nv on find_in_set(nv.MACCHN,REPLACE(xm.MA_BAC_SI,';',',')) != 0 WHERE nv.HOP_TAC = 1 AND CONVERT(xm.NGAY_YL,UNSIGNED) BETWEEN " . intval($start_day) . " AND " . intval($end_day) . " ORDER BY MACCHN ASC, NGAY_YL ASC";
        $query = $this->db->query($sql);
        $list = $query->result_array();
        /**
         * Xử lí mãng chấm công
         */
        $max = 0;
        $min = 999999999999999;
        $cchn = $list[0]["MACCHN"];
        $ten = $list[0]["HO_TEN"];
        $ngay = substr($list[0]["NGAY_KQ"], 6, 2);
        $data = [];
        $gio_vao = '';
        $gio_ra = '';
        foreach ($list as $key => $l) {
            $ngay_yl =  substr($l["NGAY_KQ"], 6, 2);
            if ($cchn == $l["MACCHN"]) {
                if ($ngay_yl == $ngay) {
                    $data[$cchn][$ngay] = $l;
                } else {
                    $ngay = substr($l["NGAY_KQ"], 6, 2);
                    $data[$cchn][$ngay] = $l;
                }
            } else {
                $cchn = $l["MACCHN"];
                $data[$cchn][$ngay] = $l;
            }
        }

        $this->load->library("pxl");
        //Create a new Object
        $objPHPExcel = new PHPExcel();
        // Set the active Excel worksheet to sheet 0
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 0;
        $heading = array_keys($list[0]); //set title in excel sheet


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

        $rowCount = 2; // set the starting row from which the data should be printed
        foreach ($list as $i => $excel) {


            //  $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $rowCount, $i + 1);
            $colH = 'A';
            foreach ($excel as $key => $val) {

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $val);
                // echo json_encode($excel);
                $colH++;
            }
            $rowCount++;
        }
        // Instantiate a Writer 

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="chamconghoptac' . $m . "-" . $y . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }
    function cohuu_export()
    {
        $m = $this->input->post("thang");
        $y = $this->input->post("nam");
        $d = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        $start_day = $y . $m . "010000";
        $end_day = $y . $m . $d . "2359";
        $sql = "SELECT DISTINCT nv.HO_TEN,nv.MACCHN,xm.MA_NHOM,xm.TEN_DICH_VU,xm.NGAY_YL,xm.NGAY_KQ,nv.TEN_KHOA 
        FROM `xml3` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0 
        
        WHERE  CONVERT(xm.NGAY_YL,UNSIGNED) BETWEEN " . intval($start_day) . " AND " . intval($end_day) . " AND xm.MA_NHOM != 15 "; //. " ORDER BY ORDER BY TEN_KHOA ASC, nv.MACCHN ASC, xm.NGAY_YL ASC";
        $sql .= " UNION SELECT DISTINCT nv.HO_TEN,nv.MACCHN,xm.MA_NHOM,xm.TEN_THUOC as TEN_DICH_VU,xm.NGAY_YL,xm.NGAY_YL as NGAY_KQ,nv.TEN_KHOA 
        FROM `xml2` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0 
       
        WHERE  CONVERT(xm.NGAY_YL,UNSIGNED) BETWEEN " . intval($start_day) . " AND " . intval($end_day) . " AND xm.MA_NHOM != 15  ORDER BY TEN_KHOA ASC, MACCHN ASC, NGAY_YL ASC ";
        $query = $this->db->query($sql);
        $list = $query->result_array();
        //echo $sql;

        $cchn = $list[0]["MACCHN"];
        $ten = $list[0]["HO_TEN"];
        $ten_khoa = $list[0]["TEN_KHOA"];
        $ngay = intval(substr($list[0]["NGAY_YL"], 6, 2));
        $gio_min = 9999999999999999;
        $gio_max = 0;
        $data = [];
        $data_row = [];
        foreach ($list as $key => $l) {
            // echo $cchn ."=".$l["MACCHN"]."<br/>";

            if ($cchn != $l['MACCHN']) {
                //    echo "Khac" ."<br/>";
                $data[] = [
                    "MACCHN" => $cchn,
                    "HO_TEN" => $ten,
                    "TEN_KHOA" => $ten_khoa,
                    "CHAM_CONG" => $data_row
                ];
                $data_row = [];
                $cchn = $l["MACCHN"];
                $ten = $l["HO_TEN"];
                $ten_khoa = $l["TEN_KHOA"];
                $ngay = intval(substr($l["NGAY_YL"], 6, 2));
            }
            if ($cchn == $l['MACCHN']) {
                $ngay_yl = intval(substr($l["NGAY_YL"], 6, 2));
                $gio_yl = intval(substr($l["NGAY_YL"], 8, 2));
                $val = '';
                //  echo "nga = ".$ngay.",ngay yl = ".$ngay_yl."<br/>";
                if ($ngay == $ngay_yl) {
                    $gio_min = $gio_yl < $gio_min ? $gio_yl : $gio_min;
                    $gio_max = $gio_yl > $gio_max ? $gio_yl : $gio_max;
                    /** Giờ hành chính */
                    if ($gio_max <= 18) {
                        $val = "+";
                    }
                    /* Trực 24 */
                    if ($gio_max > 18) {
                        $val = "T24";
                    }
                    /*Ngoài giờ */
                    /*Xét trường hợp ngay hôm trước có trực T24 */
                    if (intval($ngay) > 1) {

                        if (isset($data_row[intval($ngay) - 1])) {
                            if ($data_row[intval($ngay) - 1] == 'T24') {
                                if ($gio_max >= 6 && $gio_max <= 12) {
                                    $val = "+/RT";
                                }
                                if ($gio_max > 12 && $gio_max <= 18) {
                                    $val = "+";
                                }
                            }
                            if ($data_row[intval($ngay) - 1] != 'T24') {
                                if ($gio_min >= 0 && $gio_min < 6) {
                                    $data_row[intval($ngay) - 1] = 'T24';
                                }
                            }
                        }
                    }
                } else {
                    /** Giờ hành chính */
                    if ($gio_max <= 18) {
                        $val = "+";
                    }
                    /* Trực 24 */
                    if ($gio_max > 18) {
                        $val = "T24";
                    }
                    /*Ngoài giờ */
                    /*Xét trường hợp ngay hôm trước có trực T24 */
                    if (intval($ngay) > 1) {

                        if (isset($data_row[intval($ngay) - 1])) {


                            if ($data_row[intval($ngay) - 1] == 'T24') {
                                if ($gio_max >= 6 && $gio_max <= 12) {
                                    $val = "+/RT";
                                }
                                if ($gio_max > 12 && $gio_max <= 18) {
                                    $val = "+";
                                }
                            }
                            if ($data_row[intval($ngay) - 1] != 'T24') {
                                if ($gio_min >= 0 && $gio_min < 6) {
                                    $data_row[intval($ngay) - 1] = 'T24';
                                }
                            }
                        }
                    }

                    $ngay = $ngay_yl;
                    $gio_min = 9999999999999999;
                    $gio_max = 0;
                }
                $data_row[intval($ngay)] = $val;
                $gio_min = 9999999999999999;
                $gio_max = 0;
                // echo "min = ".$gio_min .","."max = ".$gio_max."</br/>";
                // echo var_dump($data_row)."<br/>";
            }


            if (count($list) - 1 == $key) {
                $data[] = [
                    "MACCHN" => $l['MACCHN'],
                    "HO_TEN" => $l['HO_TEN'],
                    "TEN_KHOA" => $l['TEN_KHOA'],
                    "CHAM_CONG" => $data_row
                ];
            }
        }
        //echo json_encode($data);

        $this->load->library("pxl");
        //Create a new Object
        $objPHPExcel = new PHPExcel();
        // Set the active Excel worksheet to sheet 0
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 0;
        $heading = [
            "HỌ TÊN",
            "CCHN"
        ];
        for ($i = 1; $i <= $d; $i++) {
            array_push($heading, $i);
        }


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

        $rowCount = 2; // set the starting row from which the data should be printed
        $khoa = "";
        foreach ($data as $i => $excel) {
            if ($excel["TEN_KHOA"] != $khoa) {
                $colH = 'A';
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $excel["TEN_KHOA"]);
                $khoa = $excel["TEN_KHOA"];
                $objPHPExcel->getActiveSheet()->getStyle($colH . $rowCount . ':' . $colH . $rowCount)->getFont()->setBold(true);
                $rowCount++;
            }


            //  $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $rowCount, $i + 1);
            $colH = 'A';
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $excel["HO_TEN"]);
            $colH++;
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $excel["MACCHN"]);
            $colH++;
            if (array_key_exists("CHAM_CONG", $excel)) :
                for ($i = 1; $i <= $d; $i++) {
                    $val = array_key_exists($i, $excel["CHAM_CONG"]) ? $excel["CHAM_CONG"][$i] : '';
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $val);
                    $colH++;
                }
            endif;



            $rowCount++;
        }
        // Instantiate a Writer 

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="chamcongcohuu' . $m . "-" . $y . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }
    public function ylenh()
    {
        $header['js'] = ['js/ylenh.js'];
        $header['css'] = [];
        $data = [];

        $this->load->view('templates/header-page', $header);
        $this->load->view('chamcong/ylenh', $data);
        $this->load->view('templates/footer-page', $header);
        echo $this->db->last_query();
    }
    public function chayylenh()
    {

        $mabacsi = $this->input->get("mabacsi");
        $mabacsi = explode(",", $mabacsi);
        $mabacsi = "'" . implode("','", $mabacsi) . "'";
        $tungay = $this->input->get("tungay");
        $denngay = $this->input->get("denngay");
        $filter = $this->input->get("filter");
        $lenght =  $this->input->get("length");
        $limit =  $this->input->get("limit");
        $draw =  $this->input->get("draw");

        $sql = '';
        if ($filter == '') {
            $sql .= " SELECT * FROM (";
            $sql .= " SELECT DISTINCT xm.MA_LK,'THUỐC'  as LOAI,xm.TEN_THUOC as TEN,nv.HO_TEN,nv.MACCHN as MA_BAC_SI,xm.NGAY_YL,xm.MA_NHOM FROM `xml2` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0";

            //   $sql .= " SELECT * FROM (( SELECT xml2.MA_LK,'THUỐC' as LOAI,xml2.TEN_THUOC as TEN,nhanvien.HO_TEN,xml2.MA_BAC_SI,xml2.NGAY_YL,xml2.MA_NHOM FROM xml2";
            //   $sql .= " inner join nhanvien on find_in_set(nhanvien.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xml2.MA_BAC_SI,';',',')) != 0 ";
            //  $sql .= " LEFT JOIN nhanvien on xml2.MA_BAC_SI collate utf8mb4_unicode_ci = nhanvien.MACCHN collate utf8mb4_unicode_ci";
            //   $sql .= " WHERE xml2.MA_BAC_SI in ($mabacsi))";
            $sql .= " UNION ";
            // $sql .= " SELECT xml3.MA_LK, IF(xml3.MA_VAT_TU != '','VẬT TƯ','DỊCH VỤ')  as LOAI,IF(xml3.TEN_VAT_TU != '',xml3.TEN_VAT_TU,xml3.TEN_DICH_VU) as TEN,nhanvien.HO_TEN,xml3.MA_BAC_SI,xml3.NGAY_YL,xml3.MA_NHOM FROM xml3";
            $sql .= " SELECT DISTINCT xm.MA_LK,IF(xm.MA_VAT_TU != '','VẬT TƯ','DỊCH VỤ')  as LOAI,IF(xm.TEN_VAT_TU != '',xm.TEN_VAT_TU,xm.TEN_DICH_VU) as TEN,nv.HO_TEN,nv.MACCHN as MA_BAC_SI,xm.NGAY_YL,xm.MA_NHOM FROM `xml3` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0";
            // $sql .= " inner join nhanvien on find_in_set(nhanvien.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xml3.MA_BAC_SI,';',',')) != 0 ";
            // $sql .= " WHERE xml3.MA_BAC_SI in ($mabacsi) ";


            $sql .= " ) as b WHERE 1 ";
            if ($tungay != '' && $denngay != '') {
                if ($tungay < $denngay) {
                    $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($denngay)) . "2359");
                }
                if ($tungay == $denngay) {
                    $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($tungay)) . "2359");
                }
            } else if ($tungay != '') {
                $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($denngay)) . "2359");
            } else if ($denngay != '') {
                $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) <= " . intval(date("Ymd", strtotime($tungay)) . "0000");
            }
            $sql .= " AND b.MA_NHOM != 15 AND b.MA_BAC_SI in($mabacsi) ORDER BY b.MA_BAC_SI,b.NGAY_YL ASC";
        }
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        // echo $this->db->last_query();
        // die();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(
                [
                    "draw" => $draw,
                    "recordsTotal" => count($rows),
                    "recordsFiltered" => count($rows),
                    "data" => $rows
                ]
            ));
    }
    function chayylenh_excel()
    {
        $mabacsi = $this->input->get("mabacsi");
        $mabacsi = explode(",", $mabacsi);
        $mabacsi = "'" . implode("','", $mabacsi) . "'";
        $tungay = $this->input->get("tungay");
        $denngay = $this->input->get("denngay");
        $filter = $this->input->get("filter");
        $lenght =  $this->input->get("length");
        $limit =  $this->input->get("limit");
        $draw =  $this->input->get("draw");
        $sql = '';
        if ($filter == '') {
            $sql .= " SELECT * FROM (";
            $sql .= " SELECT DISTINCT xm.MA_LK,'THUỐC'  as LOAI,xm.TEN_THUOC as TEN,nv.HO_TEN,nv.MACCHN as MA_BAC_SI,xm.NGAY_YL,xm.MA_NHOM FROM `xml2` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0";

            //   $sql .= " SELECT * FROM (( SELECT xml2.MA_LK,'THUỐC' as LOAI,xml2.TEN_THUOC as TEN,nhanvien.HO_TEN,xml2.MA_BAC_SI,xml2.NGAY_YL,xml2.MA_NHOM FROM xml2";
            //   $sql .= " inner join nhanvien on find_in_set(nhanvien.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xml2.MA_BAC_SI,';',',')) != 0 ";
            //  $sql .= " LEFT JOIN nhanvien on xml2.MA_BAC_SI collate utf8mb4_unicode_ci = nhanvien.MACCHN collate utf8mb4_unicode_ci";
            //   $sql .= " WHERE xml2.MA_BAC_SI in ($mabacsi))";
            $sql .= " UNION ";
            // $sql .= " SELECT xml3.MA_LK, IF(xml3.MA_VAT_TU != '','VẬT TƯ','DỊCH VỤ')  as LOAI,IF(xml3.TEN_VAT_TU != '',xml3.TEN_VAT_TU,xml3.TEN_DICH_VU) as TEN,nhanvien.HO_TEN,xml3.MA_BAC_SI,xml3.NGAY_YL,xml3.MA_NHOM FROM xml3";
            $sql .= " SELECT DISTINCT xm.MA_LK,IF(xm.MA_VAT_TU != '','VẬT TƯ','DỊCH VỤ')  as LOAI,IF(xm.TEN_VAT_TU != '',xm.TEN_VAT_TU,xm.TEN_DICH_VU) as TEN,nv.HO_TEN,nv.MACCHN as MA_BAC_SI,xm.NGAY_YL,xm.MA_NHOM FROM `xml3` xm inner join nhanvien nv on find_in_set(nv.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xm.MA_BAC_SI,';',',')) != 0";
            // $sql .= " inner join nhanvien on find_in_set(nhanvien.MACCHN COLLATE utf8mb4_general_ci,REPLACE(xml3.MA_BAC_SI,';',',')) != 0 ";
            // $sql .= " WHERE xml3.MA_BAC_SI in ($mabacsi) ";


            $sql .= " ) as b WHERE 1 ";
            if ($tungay != '' && $denngay != '') {
                if ($tungay < $denngay) {
                    $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($denngay)) . "2359");
                }
                if ($tungay == $denngay) {
                    $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($tungay)) . "2359");
                }
            } else if ($tungay != '') {
                $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) BETWEEN " . intval(date("Ymd", strtotime($tungay)) . "0000") . " AND " . intval(date("Ymd", strtotime($denngay)) . "2359");
            } else if ($denngay != '') {
                $sql .= " AND CONVERT(b.NGAY_YL,UNSIGNED) <= " . intval(date("Ymd", strtotime($tungay)) . "0000");
            }
            $sql .= " AND b.MA_NHOM != 15 AND b.MA_BAC_SI in($mabacsi) ORDER BY b.MA_BAC_SI,b.NGAY_YL ASC";
        }
        $query = $this->db->query($sql);
        $data = $query->result_array();


        $this->load->library("pxl");
        //Create a new Object
        $objPHPExcel = new PHPExcel();
        // Set the active Excel worksheet to sheet 0
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 0;
        $heading = [
            "MA_LK",
            "LOAI",
            'TEN',
            'HO_TEN',
            'MA_BAC_SI',
            'NGAY_YL'
        ];
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

        $rowCount = 2; // set the starting row from which the data should be printed
        $khoa = "";
        foreach ($data as $i => $excel) {
            //  $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $rowCount, $i + 1);
            $colH = 'A';
            foreach ($excel as $key => $val) {
                if ($key == "NGAY_YL") $val = $this->date_fomat($val);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($colH . $rowCount, $val);
                $colH++;
            }
            $rowCount++;
        }
        // Instantiate a Writer 
        //die();

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ylenh.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }
    function date_fomat($value)
    {
        $nam = substr($value, 0, 4);
        $thang = substr($value, 4, 2);
        $ngay = substr($value, 6, 2);
        $gio = substr($value, 8, 2);
        $phut = substr($value, 10, 2);
        return $ngay . "/" . $thang . "/" . $nam . " " . $gio . ":" . $phut;
    }
}
