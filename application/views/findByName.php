<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li> -->
                </ul>
                <?php echo form_open_multipart('todo/findByName', ['class' => 'd-flex']); ?>
                <input name="name" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </nav>
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