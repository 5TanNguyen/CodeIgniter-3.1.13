<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EmailController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load thư viện email
        $this->load->library('email');
    }

    public function index()
    {
        // Load view để hiển thị form
        $this->load->view('send_email_view');
    }

    public function send_email()
    {
        // Lấy dữ liệu từ form
        // $from_email = $this->input->post('from_email');
        $from_email = 'tonnguyenhuu24316@gmail.com';
        // $to_email = $this->input->post('to_email');
        $to_email = 'jakal.510t@gmail.com';
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        // Thiết lập cấu hình email
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com', // Đổi smtp host phù hợp
            'smtp_port' => 587,
            'smtp_user' => $from_email,       // Sử dụng email từ form
            'smtp_pass' => 'zpcf djhk fqqo zbik',   // Mật khẩu của email
            'smtp_crypto' => 'tls',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE,
            'newline'   => "\r\n"
        );

        // Khởi tạo email với cấu hình
        $this->email->initialize($config);

        // Đặt thông tin email
        $this->email->from($from_email, 'Your Name'); // Người gửi
        $this->email->to($to_email);                  // Người nhận
        $this->email->subject($subject);              // Tiêu đề email
        $this->email->message($message);              // Nội dung email

        // Gửi email và kiểm tra kết quả
        if ($this->email->send()) {
            echo 'Email sent successfully!';
        } else {
            echo 'Failed to send email!';
            echo $this->email->print_debugger(); // Hiển thị lỗi nếu có
        }
    }
}
