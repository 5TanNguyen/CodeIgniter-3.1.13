<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
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
            <div class="card">
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
                <div class="card-header">
                    <h4 class="text-center">ToDo Table</h4>
                </div>
                <div class="card-body">
                    <table class="table" id="todo-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Priority</th>
                                <th scope="col" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
    </script>
</body>

</html>