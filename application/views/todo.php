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
                <div class="card-header">
                    <h4 class="text-center">ToDo Table</h4>
                </div>
                <div class="card-body">
                    <table class="table">
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
                            <?php
                            $count = 0;
                            foreach ($todo as $item) { ?>
                                <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $item->name ?></td>
                                    <td><?php echo $item->description ?></td>
                                    <td><?php echo $item->priority ?></td>
                                    <td><a type="button" class="btn btn-warning" onclick="fillData(`<?php echo $item->id; ?>`, `<?php echo $item->name; ?>`,`<?php echo $item->description; ?>`,`<?php echo $item->priority; ?>`,)">Edit</a></td>
                                    <td><a type="button" href="<?php echo base_url(); ?>todo/delete/<?php echo $item->id ?>" class="btn btn-danger" onclick="return confirm('You want to delete this todo ?')">Delete</a></td>
                                </tr>



                            <?php $count++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        let url = "<?php echo base_url('todo/edit/'); ?>";
        const fillData = (id, name, description, priority) => {
            let path = url + id;
            console.log(path);

            // console.log(id, name, description, priority);
            document.getElementById('form-todo').setAttribute('action', path);
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('priority').value = priority;
        };
    </script>
</body>

</html>