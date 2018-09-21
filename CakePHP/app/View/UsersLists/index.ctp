<style>
    td {
        vertical-align: middle !important;
    }

    .create_list_form {
        background-color: rgb(249, 246, 246);
        border-radius: 5px;
        padding-left: 1.5em;
        padding-right: 1.5em;
        padding-top: 1em;
        padding-bottom: 2em;
        margin: 0.5em;
        margin-top: 2em;
        border: 1px solid #efefef;
    }

    .create_list_form h3 {
        font-weight: 600;
    }

    .btn-file {
        position: relative;
        overflow: hidden;
    }

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>

<h3>Your ToDo Lists</h3>
<br>

<table id="owned_lists" class="table footable">
    <thead>
    </thead>
    <tbody>
    </tbody>
</table>

<h3>Shared lists with you</h3>
<br>

<table id="shared_lists" class="table footable">
    <thead>
    </thead>
    <tbody>
    </tbody>
</table>


<div class="row">

    <div class="col-lg-4 col-sm-12 col-md-12 create_list_form">
        <h3>Create a new list</h3>
        <br>

        <?php
        echo $this->Form->create('TaskList', array(
                'url' => array(
                        'controller' => 'UsersLists',
                        'action' => 'createList'
                ),
                'inputDefaults' => array(
                        'div' => 'form-group',
                ),
                'class' => 'form'
                )
        );

        echo $this->Form->input("title", array(
                'class' => 'form-control',
                'placeholder' => 'Introduce the title'
        ));

        echo $this->Form->submit('Create new ToDoList', array(
                'class' => 'btn btn-success btn-block'
        ));

        echo $this->Form->end();
        ?>
    </div>
    <div class="col-lg-4 col-sm-12 col-md-12 create_list_form">
        <h3>Upload a .xml file</h3>
        <br>
        <?php
        echo $this->Form->create('Document', array(
                'url' => array(
                        'controller' => 'UsersLists',
                        'action' => 'parsexml'
                ),
                'inputDefaults' => array(
                        'div' => 'form-group',
                ),
                'type' => 'file',
                "class" => 'form')
        );

        echo $this->Form->label("Document.fileLabel", 'Upload a xml file');

        echo $this->Form->input('Document.file', array(
                'type' => 'file',
                'before' => '<div class="input-group"><span class="input-group-btn"><span class="btn btn-default btn-file">Browse',
                'after' => '</span></span><input type=text class="form-control" readonly id="file_name" autocomplete="off"></div>',
                'between' => '',
                'label' => false,
                'class' => 'form-control',
        ));

        echo $this->Form->submit(__('Upload', true), array(
                'class' => 'btn btn-success btn-block'
        ));
        echo $this->Form->end();
        ?>
    </div>

</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_deleting">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal_title">Delete list</h4>
            </div>
            <div class="modal-body">
                <h4>Are you sure you want to delete this list?

                    Information won't be recoverable.

                    Introduce your account password to proceed:</h4>

                <form action="" id="delete_form" class="form">
                    <input type="hidden" id="delete_id">
                    <input type="password" class="form-control" id="delete_password" /> 
                    <input type="submit" class="btn btn-danger" value="Delete">
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal_sharing">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal_title">Share list</h4>
            </div>
            <div class="modal-body">

                <h4>Shared with</h4>

                <table id="sharing_table" class="table">
                    <thead>
                        <tr>
                            <td>Shared with</td>
                            <td>Permissions</td>
                            <td>Stop sharing</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <button class="btn btn-default" id="addUserToShare"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>  Add someone to share the list with</button>

                <form action="" id="sharingForm" class="form">

                    <input type="hidden" id="sharing_list_id" /> 

                    <div id="peopleToShare" class="form-group">

                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success btn-block" value="Share"/>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
                var owned_lists = <?php echo $owned_lists ?>;
                var shared_lists = <?php echo $shared_lists ?>;

                $(document).ready(function () {

                        $ft_owned = FooTable.init(
                                "#owned_lists", {
                                        "columns": [
                                                {
                                                        'name': 'id',
                                                        'title': 'id',
                                                        'breakpoints': "",
                                                        visible: false
                                                },
                                                {
                                                        'name': 'title',
                                                        'title': 'List name',
                                                        'breakpoints': ""
                                                },
                                                {
                                                        'name': 'actions',
                                                        'title': 'Actions',
                                                        'breakpoints': ""
                                                }
                                        ],
                                        "rows": owned_lists
                                });

                        $ft_shared = FooTable.init(
                                "#shared_lists", {
                                        "columns": [
                                                {
                                                        'name': 'title',
                                                        'title': 'List name',
                                                        'breakpoints': ""
                                                },
                                                {
                                                        'name': 'sharedBy',
                                                        'title': 'Shared by',
                                                        'breakpoints': ""
                                                },
                                                {
                                                        'name': 'permissions',
                                                        'title': 'You can',
                                                        'breakpoints': ""
                                                },
                                                {
                                                        'name': 'export',
                                                        'title': 'Export',
                                                        'breakpoints': ""
                                                }
                                        ],
                                        "rows": shared_lists
                                });


                        /**
                         * Open modal window for password entering and try to 
                         * delete list
                         */
                        $('button.delete').on("click", function (e) {

                                e.preventDefault();
                                var row = $(this).parents("tr");
                                var split = row.find("a").attr("href").split("/");
                                var tasklist_id = split[split.length - 1];

                                $("#delete_id").val(tasklist_id);
                                $("#modal_deleting").modal("show");


                        });

                        /**
                         * Delete list on right password
                         */
                        $("#delete_form").on("submit", function (e) {

                                e.preventDefault();

                                tasklist_id = $("#delete_id").val();

                                $.ajax({
                                        method: "POST",
                                        url: "/list/delete/" + tasklist_id,
                                        data: {
                                                pass: $("#delete_password").val()
                                        }
                                }).done(function () {
                                        console.log('List deleted successfully');

                                        $("#owned_lists > tbody > tr").filter(function () {
                                                return $(this).find("td:first").text() == tasklist_id
                                        }).remove();


                                        $("#modal_deleting").modal("hide");
                                }).fail(function () {
                                        console.log('One problem was encountered trying to delete the list.')
                                });
                        })

                        $("#owned_lists").on("click", "button.share", function () {

                                var row = $(this).parents("tr");
                                var split = row.find("a").attr("href").split("/");
                                var tasklist_id = split[split.length - 1];

                                $("#sharing_list_id").val(tasklist_id);
                                $("#modal_sharing").modal("show");
                        });

                        $("#addUserToShare").on("click", function () {

                                $div = $("<div>", {
                                        class: 'form-group toDeleteDiv'
                                });

                                $input = $("<input>", {
                                        type: 'text',
                                        class: 'form-control',
                                        placeholder: 'Write the username you would like to share this list with',
                                        required: "true"
                                });

                                $select = $("<select>", {
                                        class: 'form-control'
                                });

                                $select.append("<option value='0'> Only read </option>");
                                $select.append("<option value='1'> Read & Write </option>");

                                $div.append($("<div>", {class: 'col-lg-5 form-group'}).append("<label>User #1</label>").append($input));
                                $div.append($("<div>", {class: 'col-lg-5 form-group'}).append("<label>Permissions</label>").append($select));
                                $div.append($("<div>", {class: 'col-lg-1 form-group'}).append("<label>Remove</label>").append("<span class='btn btn-danger btn-sm glyphicon glyphicon-remove shared_dlt'></span>"));

                                $("#sharingForm > #peopleToShare").append($div);
                        });

                        $("#sharingForm").on("click", ".shared_dlt", function () {
                                $(this).parents(".toDeleteDiv").remove();
                        });

                        $("#sharingForm").on("submit", function (e) {

                                e.preventDefault();
                                var user_cmod = [];

                                $.each($(this).find("div.toDeleteDiv"), function () {
                                        user_cmod.push({
                                                user: $(this).find("input[type=text]").val(),
                                                permissions: $(this).find("select").val()
                                        });
                                });

                                $.ajax({
                                        method: "POST",
                                        url: location.href.replace("#", "") + "/share",
                                        data: {
                                                tasklist_id: $("#sharing_list_id").val(),
                                                users_lists: JSON.stringify(user_cmod)
                                        }
                                })
                                        .done(function () {
                                                $("#modal_sharing").modal("hide");
                                        })

                        });

                        $("button.export").on("click", function () {

                                var row = $(this).parents("tr");
                                var split = row.find("a").attr("href").split("/");
                                var tasklist_id = split[split.length - 1];

                                $.ajax({
                                        'method': 'GET',
                                        url: "/list/export/" + tasklist_id,
                                }).
                                        done(function (data) {
                                        });
                        });

                        $(document).on('change', '.btn-file :file', function () {
                                var input = $(this),
                                        numFiles = input.get(0).files ? input.get(0).files.length : 1,
                                        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                                input.trigger('fileselect', [numFiles, label]);
                        });

                        $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
                                console.log(numFiles);
                                console.log(label);
                                $("#file_name").val(label);
                        });


                });
</script>