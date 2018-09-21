<table id="tasklist_table" class="table">
    <thead>
        <tr>
            <td></td>
            <td>Title
                <i class="fa fa-sort-alpha-asc sort-icon sort-title" id="sort-title-asc" aria-hidden="true"></i>
            </td>
            <td name="color"></td>
            <td name="priority">Prio. <i class="fa fa-sort-amount-asc sort-icon sort-priority" aria-hidden="true" id="sort-priority-asc"></i></td>
            <td>% <i class="fa fa-sort-amount-asc sort-icon sort-percentage" aria-hidden="true" id="sort-percentage-asc"></i></td>
            <td>Due date <i class="fa fa-sort-amount-asc sort-icon sort-date" aria-hidden="true" id="sort-date-asc"></i></td>
            <td>Status <i class="fa fa-sort-alpha-asc sort-icon sort-status" aria-hidden="true" id="sort-status-asc"></i></td>
            <td>Category <i class="fa fa-sort-alpha-asc sort-icon sort-category" aria-hidden="true" id="sort-category-asc"></i></td>
            <td>Tags <i class="fa fa-sort-alpha-asc sort-icon sort-tag" aria-hidden="true" id="sort-tag-asc"></i></td>
            <td><i class="fa fa-commenting-o" aria-hidden="true"></i></td>
            <td><i class="fa fa-link" aria-hidden="true"></i></td>
            <td>Delete</td>
            <td>Subtask</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<div class="modal fade" tabindex="-1" role="dialog" id="settings_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Settings</h4>
            </div>
            <div class="modal-body">

                <table id="list_of_settings" class="table">
                    <thead>
                        <tr>
                            <td id="settings_first_head">Category name</td>
                            <td>Delete</td>
                        </tr>

                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="tags_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tags</h4>
            </div>
            <div class="modal-body">

                <table id="list_of_tags" class="table">
                    <thead>
                        <tr>
                            <td>Tag name</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="comments_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Comment</h4>
            </div>
            <div class="modal-body">
                <textarea id='task_comment'></textarea>

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Save comment</button>
                <button class="btn btn-warning" data-dismiss="modal">Cancel</button>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="links_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Link</h4>
            </div>
            <div class="modal-body">
                <input type="text" id="task_link">

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Activate link</button>
                <button class="btn btn-warning" data-dismiss="modal">Save edited link</button>

            </div>
        </div>
    </div>
</div>



<!--
<div class="input-group dropdown">
    <input type="text" class="form-control dropdown-toggle" value="(+47)">
    <ul class="dropdown-menu">
        <li><a href="#" data-value="+47">Norway (+47)</a></li>
        <li><a href="#" data-value="+1">USA (+1)</a></li>
        <li><a href="#" data-value="+55">Japan (+55)</a></li>
    </ul>
    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
</div>

-->

<?php

$this->Js->set('tasks', $tasks);
$this->Js->set('categories', $categories);
$this->Js->set('statusList', $status);
$this->Js->set('tagList', $tags);
$this->Js->set('filters', $filters);
echo $this->Js->writeBuffer(array('onDomReady' => false));

?>