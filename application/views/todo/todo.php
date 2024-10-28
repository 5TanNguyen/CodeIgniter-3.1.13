<!-- <?php
        // Start the session
        // session_start();
        ?> -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
    .overplay {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.8);
        transition: opacity 500ms;
        visibility: hidden;
        opacity: 0;
    }

    .overplay:target {
        visibility: visible;
        opacity: 1;
    }

    .wrapper {
        margin: 70px auto;
        padding: 20px;
        background: #e7e7e7e7;
        border-radius: 5px;
        width: 50%;
        position: relative;
        transition: all 5s ease-in-out;
    }

    .wrapper .close {
        position: absolute;
        top: 20px;
        right: 30px;
        transition: all 200ms;
        font-weight: bold;
        text-decoration: none;
        color: #333;
    }

    .containerr {
        border-radius: 5px;
        background-color: #e7e7e7e7;
        padding: 20px 0;
    }

    /* Responsive Mobile */
    @media screen and (max-width: 800px) {
        .containerr {
            width: 100%;
            padding: 0px;
        }

        .overplay {
            flex-direction: column;
        }

        .wrapper {
            width: 80%;
        }
    }
</style>

<body>

    <?php $this->load->view('nav'); ?>

    <div class="container">
        <br>
        <div class="row">
            <h2 class="text-center">ToDo Test</h2>
            <input type="text" name="" id="searchValue">
            <button id="searchBtn" class="btn btn-light-custom position-relative">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="mb-5 border border-primary" style="padding: 5px;">
            <?php echo form_open_multipart('todo/add', ['id' => 'form-todo']); ?>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Name">
                </div>

                <div class="form-group col-sm-3">
                    <label for="">Description</label>
                    <input type="text" name="description" id="description" class="form-control" required placeholder="Description">
                </div>

                <div class="form-group col-sm-3">
                    <label for="">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="">Image</label>
                    <input type="file" name="image" id="image" class="form-control" placeholder="Name">
                </div>
            </div>
            <br>
            <button type="submit" style="width: 100%" class="btn btn-primary btn-block">Submit</button>
            <?php echo form_close(); ?>
        </div>

        <div class="row">
            <!-- <div class="row"> -->
            <br>
            <div class="col-md-3">
                <b>Priority</b>
                <select name="priority_id" id="priority_id" class="form-control">
                    <option value="0">Show All</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            <div class="col-md-3">
                <b>Import Excel</b>
                <?php echo form_open_multipart('todo/import_excel', ['class' => 'form-control']); ?>
                <input type="file" name="upload_excel" required class="form-control">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary">

                <?php if ($this->session->flashdata('success')) { ?>
                    <p><?php echo $this->session->flashdata('success'); ?></p>
                <?php } ?>

                <?php if ($this->session->flashdata('error')) { ?>
                    <p><?php echo $this->session->flashdata('error'); ?></p>
                <?php } ?>

                <?php echo form_close(); ?>
            </div>

            <div class="col-md-3">
                <b>Export Excel</b>
                <br>
                <a class="btn btn-primary" href="<?php echo base_url('todo/excel'); ?>">===></a>
            </div>
            <div class="card-header">
                <h4 class="text-center">ToDo Table</h4>
            </div>
            <div class="card-body">
                <div id="tableContainer">
                    <!-- Bảng sẽ được render tại đây -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <select name="" id="priorityId" onchange="searchData()">
                    <option value="">All</option>
                </select>
                <div class="table-responsive">
                    <table id="diemTable" class="table table-bordered table-striped datatables">
                        <thead>
                            <tr>
                                <!-- <th>STT</th> -->
                                <?php if (1 == 1) { //(canAccess('todo.deletes')) { 
                                ?>
                                    <th><input type="checkbox" class="checkAll form-check-input"></th>
                                <?php } ?>
                                <!-- <th>Mã tôn giáo</th> -->
                                <!-- <th>Tên tiếng Việt</th> -->
                                <!-- <th>Tên tiếng Anh</th> -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .scrollable-section {
            height: 600px;
            overflow-y: scroll;
            background-color: #fff;
        }

        .bg-color {
            background-color: #dec018;
            padding: 2px;
            border-radius: 5px;
        }

        .no-border {
            border: none;
            /* Không có viền khi bình thường */
            outline: none;
            /* Bỏ đường viền bên ngoài khi nhấn */
        }

        .no-border:focus {
            border: 1px solid #4b69c1;
            /* Hiển thị viền khi input được focus */
            outline: none;
            /* Không hiển thị viền ngoài */
        }
    </style>
    </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // EDIT
        let url = "<?php echo base_url('todo/edit/'); ?>";
        const fillData = (id, name, description, priority) => {
            let path = url + id;
            // console.log(path);
            document.getElementById('form-todo').setAttribute('action', path);
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('priority').value = priority;
        };
        // END EDIT

        function loadTodoPriority() {
            var priority = $("#priority_id").val();
            $.ajax({
                url: "<?php echo base_url('todo/getTodoByPriority') ?>",
                data: "priority=" + priority,
                success: function(data) {
                    $('#tableContainer').html(data);
                }
            });
        }

        // Close modal if user clicks outside of modal content
        // window.onclick = function(event) {
        //     if (event.target == modal) {
        //         closeModalSmoothly();
        //         errorMessage.style.display = "none"; // Hide error message if it's visible
        //     }
        // }
        ////////////////////////////////////// Update Code /////////////////////////////////////
        // Datatables Ajax
        var dataFilter = true;
        var searchButton = $('#searchBtn');

        function datatableCallAjax() {
            let searchValue = $('#searchValue').val();
            let priorityId = $('#priorityId').val();
            // let created_at = $('#created_at').val();

            let created_at = '';
            dataFilter = dataFilter;


            let urlAppend =
                '?searchValue=' + searchValue +
                '&priorityId=' + priorityId +
                '&created_at=' + created_at +
                '&dataFilter=' + dataFilter;

            // Khởi tạo cấu trúc cột cơ bản
            var columns = [
                <?php if (1 == 1) { ?> { //(canAccess('todo.deletes')) { ?> {
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return `<input type="checkbox" class="checkboxItem form-check-input" value="${data}">`;
                        },
                        orderable: false, // Không sắp xếp cột này
                        searchable: false, // Không tìm kiếm trong cột này
                        className: 'text-center'
                    },
                <?php } ?> {
                    data: null,
                    title: 'STT',
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // STT bắt đầu từ 1
                    },
                },
                {
                    data: 'name',
                    title: 'Tên công việc',
                },
                {
                    data: 'description',
                    title: 'Mô tả',
                },
                {
                    data: 'priority',
                    title: 'Ưu tiên',
                },
                // {
                // 	data: 'dm_ton_giao_id',
                // 	render: (data, type, row, meta) => {
                // 		return `<button type="button" class="btn edit-can btn-edit" data-diem-id="${data}"><i class="fas fa-edit"></i></button>`
                // 	},
                // 	title: 'Thao tác',
                // }
            ];

            // Kiểm tra xem DataTable đã được khởi tạo chưa
            if (!$.fn.dataTable.isDataTable('#diemTable')) {
                table = $('#diemTable').DataTable({
                    ajax: {
                        url: '<?= base_url('todo/ajaxDataTable') ?>' + urlAppend,
                        type: 'GET',
                        data: function(d) {
                            // Kiểm tra nếu không có sắp xếp nào được áp dụng
                            if (d.order.length === 0) {
                                // Áp dụng sắp xếp mặc định
                                d.order = [{
                                    column: 1, // Chỉ số cột sắp xếp mặc định
                                    dir: 'asc' // Hướng sắp xếp mặc định
                                }];
                            }
                        },
                        dataSrc: function(json) {
                            // $('#curentPage').text('Danh mục tôn giáo');
                            // alert('Success');
                            console.log(json);
                            if (dataFilter && json.dataFilter) {
                                let todo = json.dataFilter.todo;
                                todo.forEach(element => {
                                    $('#priorityId').append(`<option value="${element.priority}">${element.priority}</option>`);
                                });

                                dataFilter = false;
                            }

                            // Kiểm tra xem có yêu cầu chuyển hướng không
                            if (json.redirect) {
                                window.location.href = json.redirect;
                                return [];
                            }
                            // Nếu không, trả về dữ liệu bảng
                            return json.data;
                        },
                        complete: function() {
                            // Kích hoạt lại nút tìm kiếm sau khi AJAX hoàn thành
                            setTimeout(function() {
                                searchButton.prop('disabled', false);
                            }, 2000)
                        },
                        error: function() {
                            // Kích hoạt lại nút tìm kiếm trong trường hợp gặp lỗi
                            setTimeout(function() {
                                searchButton.prop('disabled', false);
                            }, 2000)
                        }
                    },
                    order: [
                        // [<= canAccess('todo.deletes') ? 1 : 0 ?>, 'asc']
                        [<?= (1 == 1) ? 1 : 0 ?>, 'asc']
                    ],
                    dom: '<"top">rt<"bottom d-flex justify-content-between"lpi><"clear">',
                    columns: columns,
                    columnDefs: [{
                        // "targets": [<= canAccess('todo.deletes') ?? 1 ?>, <= canAccess('todo.deletes') ? 5 : 4 ?>],
                        "targets": [0, 1],
                        "orderable": false
                    }],
                    processing: true,
                    serverSide: true,
                    lengthMenu: [
                        [10, 20, 50, -1],
                        [10, 20, 50, "All"]
                    ],
                    pagingType: "simple_numbers",
                    language: {
                        "sLengthMenu": "Hiển thị _MENU_ dòng",
                        "sZeroRecords": "Không tìm thấy dữ liệu phù hợp",
                        "sInfo": "Tổng số: _TOTAL_ dòng",
                        "sInfoEmpty": "Tổng số: 0 dòng",
                        "sInfoFiltered": "(lọc từ _MAX_ dòng)",
                        "sSearch": "Tìm kiếm:",
                        "oPaginate": {
                            "sFirst": "Trang đầu",
                            "sPrevious": "Trang trước",
                            "sNext": "Trang sau",
                            "sLast": "Trang cuối"
                        }
                    }
                });
            } else {
                table.ajax.url('<?= base_url('todo/ajaxDataTable') ?>' + urlAppend).load(function() {
                    // Kích hoạt lại nút tìm kiếm sau khi tải dữ liệu xong
                    setTimeout(function() {
                        searchButton.prop('disabled', false);
                    }, 2000)
                });
            }
        }

        // Fetch Data by Ajax
        $(document).ready(function() {
            datatableCallAjax();

            loadTodoPriority();
            $("#priority_id").change(function() {
                // let a = $(this).val();
                // console.log(a);
                loadTodoPriority();
            });
        });

        function searchData() {
            // Vô hiệu hóa nút tìm kiếm
            searchButton.prop('disabled', true);

            datatableCallAjax();
        }
    </script>
    <script src="<?= base_url('assets/js/DataTablesCallAjax.js') ?>"></script>
</body>

</html>