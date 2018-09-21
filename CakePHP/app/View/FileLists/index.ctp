<div>
    <h4>Modify default settings that are included in new task lists</h4>
    <button class="btn btn-default" id="open_settings">Default settings</button>

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
<div>
    <form id="create_form" class="form-inline" class="col-lg-4">
        <div class="form-group">
            <div class="input-group">
                <label class="sr-only" for="name_input">File name</label>
                <input type="text" id="name_input" class="form-control" placeholder="File name" required />
                <div class="input-group-addon">.xml</div>
            </div>
        </div>
        <div class="form-group">
            <label class="sr-only" for="listname_input">Task list title</label>
            <input type="text" id="listname_input" class="form-control" placeholder="Tasklist title" required/>
        </div>
        <input type="submit" id="submit_input" class="btn btn-default" value="Create new task list" />

    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="settings_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal_title">Default settings</h4>
            </div>
            <div class="modal-body">
                <form class="form-inline" id="new_status">
                    <input type="text" class="form-control" id="new_status_input" placeholder="Status name" required>
                    <input class="btn btn-default" type="submit" value="Add new status" />
                </form>
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
                <hr>
                <form class="form-inline" id="new_cat">
                    <input type="text" class="form-control" id="new_cat_input" placeholder="Status name" required>
                    <button class="btn btn-default" id="new_cat">Add new category</button>
                </form>
                <table id="list_of_cat" class="table">
                    <thead>
                        <tr>
                            <td>Category</td>
                            <td>Delete</td>
                        </tr>

                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <hr>
                <form class="form-inline" id="new_tag">
                    <input type="text" class="form-control" id="new_tag_input" placeholder="Status name" required>
                    <button class="btn btn-default" id="new_tag">Add new tag</button>
                </form>
                <table id="list_of_tag" class="table">
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
    var categories = <?php echo $categories ?>;
    var tagList = <?php echo $tags ?>;

    $(document).ready(function () {

        $ft = FooTable.init(
            "#list_table", {
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
            }).done(function (data) {
                var json_data = JSON.parse(data);
                var name = "<td>" + json_data.project_name + "</td>";
                var filename = "<td class='" + json_data.file_name.options.classes + "'>" + json_data.file_name.value + "</td>"
                var button = "<td>" + json_data.actions + "</td>";
                var row = "<tr>" + name + filename + button + "</tr>";

                $("#list_table > tbody").append(row);
                $('#create_form')[0].reset();

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

            }).fail(function () {
                alert("File already exists");
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

        $("#open_settings").on("click", function () {
            setTableSettings(statusList, 'status');
            setTableSettings(categories, 'cat');
            setTableSettings(tagList, 'tag');

            $("#settings_modal").modal("show");
        })

        $("#settings_modal").on('hidden.bs.modal', function (e) {
            var sett = {
                "status": statusList,
                "categories": categories,
                "tags": tagList
            };

            $("#settings_modal > input[type=text]").val();

            $.ajax({
                method: "POST",
                url: "/list/settings",
                data: {
                    settings: JSON.stringify(sett)
                }
            }).done(function () {
                console.log('Mod');
            }).fail(function () {
                console.log('Error')
            });


        })

        /**
         * 
         * @param {type} type
         * @param {type} name
         * 
         * @returns {undefined}
         */
        function setTableSettings(type, name) {
            $("#list_of_" + name + " > tbody").empty();
            $.each(type, function (key, value) {
                $("#list_of_" + name + " > tbody").append("<tr><td class='" + name + "_name'>" + value + "</td><td><button class='delete_" + name + " btn btn-danger btn-xs'>Delete</button></td></tr>");
            })

            $('.delete_' + name).on("click", function (e) {
                e.preventDefault();
                var parent = $(this).parents("tr");
                var i = type.indexOf(parent.find('.' + name + '_name').text());
                if (i !== -1) {
                    type.splice(i, 1);
                }

                parent.remove();
            });
        }



        $("#new_status").on("submit", function (e) {
            e.preventDefault();
            add_setting(statusList, "status", $("#new_status_input").val())
        });

        $("#new_cat").on("submit", function (e) {
            e.preventDefault();
            add_setting(categories, "cat", $("#new_cat_input").val())
        });
        $("#new_tag").on("submit", function (e) {
            e.preventDefault();
            add_setting(tagList, "tag", $("#new_tag_input").val())
        });



        function add_setting(type, name, value) {

            var i = type.indexOf(value);
            if (i === -1) {
                type.push(value)
            }

            $("#list_of_" + name + " > tbody").append("<tr><td class='" + name + "_name'>" + value + "</td><td><button class='delete_" + name + " btn btn-danger btn-xs'>Delete</button></td></tr>")

            $('.delete_' + name).on("click", function (e) {
                e.preventDefault();
                var parent = $(this).parents("tr");
                var i = type.indexOf(parent.find('.' + name + '_name').text());
                if (i !== -1) {
                    type.splice(i, 1);
                }

                parent.remove();
            });
        }
    });
</script>