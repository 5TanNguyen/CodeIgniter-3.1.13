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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="overplay" id="divOne">
            <div class="wrapper">
                <h2 class="text-center">Personal Information</h2>
                <a href="#" class="close">&times;</a>
                <div class="content">
                    <div class="containerr">
                        <?php echo form_open_multipart("todo/userUpdate", ['class' => 'row']) ?>
                        <div class="form-group col-sm-12 d-flex justify-content-center">
                            <img src="<?php echo base_url('todo/getImage?image=') . $_SESSION['image']; ?>" alt="" width="100" height="100">
                            <!-- <input class="form-control" type="file" name="image" id=""> -->
                            <br>
                        </div>

                        <div class="form-group col-sm-12 d-flex justify-content-center">
                            <input class="form-control" type="file" name="image" id="">
                            <br>
                        </div>

                        <div class="form-group col-sm-12">
                            <br>
                            <label for="">Email</label>
                            <input class="form-control" name="email" readonly type="text" value="<?php echo $_SESSION['email']; ?>">
                            <br>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="">Firstname</label>
                            <input class="form-control" name="firstname" type="text" value="<?php echo $_SESSION['firstname']; ?>">
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="">Lastname</label>
                            <input class="form-control" name="lastname" type="text" value="<?php echo $_SESSION['lastname']; ?>">
                        </div>
                        <div class="form-group col-sm-12">
                            <br>
                            <input type="submit" class="btn btn-outline-danger" value="Submit" style="width: 100%;">
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a style="font-weight: bold" class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (isset($_SESSION['email'])) {
                            ?>
                                <?php echo $_SESSION['email']; ?>
                            <?php } else { ?>
                                Bruh
                            <?php } ?>

                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#divOne">Profile</a>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo base_url('todo/logout'); ?>">Log out</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <!-- <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                        </ul>
                    </li>
                    <!--
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
                <b>Export Excel</b>
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
                            <th scope="col" colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- </div> -->
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