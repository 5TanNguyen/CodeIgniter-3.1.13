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
                <table class="table" id="todo-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Test</th>
                            <th scope="col" colspan="2">Action</th>
                            <th colspan="10"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- </div> -->
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
    </style>
    </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            loadTodoPriority();
            $("#priority_id").change(function() {
                // let a = $(this).val();
                // console.log(a);
                loadTodoPriority();
            });
        });

        function loadTodoPriority() {
            var priority = $("#priority_id").val();
            $.ajax({
                url: "<?php echo base_url('todo/getTodoByPriority') ?>",
                data: "priority=" + priority,
                success: function(data) {
                    $("#todo-table tbody").html(data);
                }
            });
        }

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


        // Close modal if user clicks outside of modal content
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModalSmoothly();
                errorMessage.style.display = "none"; // Hide error message if it's visible
            }
        }
    </script>
</body>

</html>