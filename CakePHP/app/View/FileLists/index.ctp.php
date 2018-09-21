<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<div>
    <h4>Modify default settings that are included in new task lists</h4>
    <button class="btn btn-default" id="open_status">Default status</button> 
    <button class="btn btn-default">Default categories</button>    
    <button class="btn btn-default">Default tags</button>    

</div>

<h2>Xml files allocated in the server</h2>
<br>

<table id="list_table" class="table footable">
    <thead>
    </thead>
    <tbody>
    </tbody>
</table>

<h3>Create a new TaskList</h3>
<br>
<form id="create_form" class="form-inline" class="col-lg-4">
    <div class="form-group">
        <div class="input-group">
            <label class="sr-only" for="name_input">File name</label>
            <input type="text" id="name_input" class="form-control" placeholder="File name"/>
            <div class="input-group-addon">.xml</div>
        </div>
    </div>
    <div class="form-group">
        <label class="sr-only" for="listname_input">Task list title</label>
        <input type="text" id="listname_input" class="form-control" placeholder="Tasklist title"/>
    </div>
    <input type="submit" id="submit_input" class="btn btn-default" value="Create new task list" />

</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="status_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body">

                <button class="btn btn-default">Add new status</button>
                <table id="list_of_status" class="table">
                    <thead>
                        <tr>
                            <td>Status</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="categories_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Categories</h4>
            </div>
            <div class="modal-body">

                <button class="btn btn-default">Add new categories</button>
                <table id="list_of_status" class="table">
                    <thead>
                        <tr>
                            <td>Category name</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="tags_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tags</h4>
            </div>
            <div class="modal-body">

                <button class="btn btn-default">Add new tag</button>
                <table id="list_of_status" class="table">
                    <thead>
                        <tr>
                            <td>Tag</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
        var rows = <?php echo $files ?>;
        var $ft;
        var statusList = <?php echo $status ?>;

        $(document).ready(function () {

                $ft = FooTable.init(
                        "#list_table",
                        {
                                "columns": [
                                        {
                                                'name': 'project_name',
                                                'title': 'List name',
                                                'breakpoints': ""
                                        },
                                        {
                                                'name': 'file_name',
                                                'title': 'File name',
                                                'breakpoints': ""
                                        },
                                        {
                                                'name': 'actions',
                                                'title': 'Actions',
                                                'breakpoints': ""
                                        }
                                ],
                                "rows": rows
                        });

                $('#create_form').on('submit', function (e) {
                        e.preventDefault();

                        $.ajax({
                                method: "POST",
                                url: "/list/create",
                                data: {
                                        "fileName": $('#name_input').val(),
                                        "listTitle": $('#listname_input').val(),
                                }
                        }).done(function (row) {
                                $ft.rows.add(JSON.parse(row));
                                $('#create_form')[0].reset();

                        }).fail(function () {

                        });

                })

                $('button.delete').on("click", function (e) {

                        e.preventDefault();

                        var del = prompt("Are you sure you want to delete this list?\n\nInformation won't be recoverable.\n\nIntroduce the right key to delete:\n");
                        if (del === 'abraxas') {

                                var row = $(this).parents("tr");

                                var filename = row.find('.filename').text();

                                $.ajax({
                                        method: "POST",
                                        url: "/list/delete",
                                        data: {
                                                "fileName": filename
                                        }
                                }).done(function () {
                                        row.remove();
                                        console.log('Row deleted successfully');
                                }).fail(function () {
                                        console.log('There have been one problem trying to delete the row.')
                                });
                        }
                });

                $("#open_status").on("click", function () {
                        $("#list_of_status > tbody").empty();

                        $.each(statusList, function (key, value) {
                                $("#list_of_status > tbody").append("<tr><td class='status_name'>" + value + "</td><td><button class='delete_status btn btn-danger btn-xs'>Delete</button></td></tr>");
                        })

                        $("#status_modal").modal("show");


                        $('.delete_status').on("click", function (e) {
                                e.preventDefault();

                                var parent = $(this).parents("tr");
                                var i = statusList.indexOf(parent.find('.status_name').text());

                                if (i !== -1) {
                                        statusList.splice(i, 1);
                                }

                                parent.remove();
                        })
                })

        });

</script>