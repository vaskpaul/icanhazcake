<?php ?> 

<table id="read_table" class="table">
    <thead>
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

<?php
//$this->Js->set('tasks', $tasks);
$this->Js->set('categories', $categories);
$this->Js->set('statusList', $status);
$this->Js->set('tagList', $tags);
$this->Js->set('filters', $filters);
echo $this->Js->writeBuffer(array('onDomReady' => false));
?>