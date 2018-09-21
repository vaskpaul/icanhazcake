
<div id="normal_screen">
    <div id="filter_buttons">
        <button class="btn btn-default" id="open_status">Status</button>
        <button class="btn btn-default" id="open_categories">Cats</button>
        <button class="btn btn-default" id="open_tags">Tags</button>
        <button class="btn btn-default" id="hide_completed_btn">100%</button>
        <button class="footable-add"></button>
    </div>

    <table id="table" class="table footable">
        <thead>
            <!--<tr>
                <td  name="id" visible="false" breakpoints ='xs sm' title='id'>id</td>
                <td  name="route" visible="false" breakpoints ='xs sm'>route</td>
                <td  data-type="html" name="check"></td>
                <td  name="title">Title</td>
                <td  name="color" breakpoints ='xs sm' visible='false'>color</td>
                <td  name="priority" breakpoints ='xs sm'>Priority</td>
                <td  name='percentage' breakpoints ='xs sm'>%</td>
                <td  name='date' breakpoints ='xs sm'>Due date</td>
                <td  name='recurrence' breakpoints ='xs sm'>Recurrence</td>
                <td  name='status' breakpoints ='xs sm'>Status</td>
                <td  name='category' breakpoints ='xs sm'>Category</td>
                <td  name='tags' breakpoints ='xs sm'>Tags</td>
                <td  data-type="html" name='subtask' data-breakpoints="xs sm">Subtask</td>
            </tr>-->
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<div id="mobile_form" style="display:none">

    <form id="form">

        <h2 id="form_header">New task</h2>

        <input type="hidden" id="form_id" name="form_id"/> 
        <input type="hidden" id="form_route" name="form_route"/> 

        <div class="form-group">
            <label>Title</label>
            <input type="text" id="form_title" class="form-control" name="title" required>
        </div>

        <div class="form-group">
            <label>Color</label>
            <select name='colorpicker-fontawesome' id="form_colorpicker"class="form-control">
                <option value='#000'>Black</option>
                <option value='#ff0000'>Red</option>
                <option value='#0000ff'>Blue</option>
                <option value='#00ff00'>Green</option>
            </select>
        </div>

        <div class="form-group">
            <label>Priority</label>
            <select id="form_priority" required="" class="form-control">
                <option value="0" class="option0">0</option>
                <option value="1" class="option1">1</option>
                <option value="2" class="option2">2</option>
                <option value="3" class="option3">3</option>
                <option value="4" class="option4">4</option>
                <option value="5" class="option5" selected>5</option>
                <option value="6" class="option6">6</option>
                <option value="7" class="option7">7</option>
                <option value="8" class="option8">8</option>
                <option value="9" class="option9">9</option>
                <option value="10"class="option10">10</option>
            </select>
        </div>

        <div class="form-group">
            <label>Percentage done</label>
            <input type="number" class="form-control" step="5" min="0" max="100" title="percentage" name="percentage" id="form_percentage" value="0" required="">
        </div>

        <div class="form-group">
            <label>Due date</label>
            <input type="text" class="form-control"  id="form_datepicker" name="datepicker" readonly="true">
        </div>

        <div class="form-group" id="status_form_group">
            <label>Status</label>
            <span class="form-control" id="form_status"></span>
        </div>

        <div class="form-group">
            <label>Category</label>
            <input type="text" class="form-control" id="autocomplete_cats">
        </div>

        <div class="form-group">
            <label>Tags</label>
            <input type="text" class="form-control" id="autocomplete_tags">
        </div>

        <input type="button" value="Create new task" id="form_submit" name="form_submit" class="form-btn btn btn-success">
        <br>
        <input type="button" value="Cancel" id="cancel" name="cancel" class="form-btn btn btn-warning" style="margin-bottom:2em">

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

                <table id="list_of_status" class="table">
                    <thead>
                        <tr>
                            <td>Check for filter</td>
                            <td id="status_first_head">Status name</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
                
                <button id="filter_status" class="filter_btn btn btn-primary">Filter status</button>
                <button class="reset_filter btn btn-warning" id="reset_filter_status">Reset status filter</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" tabindex="-1" role="dialog" id="categories_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Categories</h4>
            </div>
            <div class="modal-body">

                <table id="list_of_categories" class="table">
                    <thead>
                        <tr>
                            <td>Check for filtering</td>
                            <td id="settings_first_head">Category name</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
                
                <button id="filter_categories" class="filter_btn btn btn-primary">Filter categories</button>
                <button class="reset_filter btn btn-warning" id="reset_filter_cats">Reset categories filter</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" tabindex="-1" role="dialog" id="tag_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tags</h4>
            </div>
            <div class="modal-body">

                <table id="list_of_tag" class="table">
                    <thead>
                        <tr>
                            <td>Check for filtering</td>
                            <td id="settings_first_head">Tag name</td>
                            <td>Delete</td>
                        </tr>    

                    </thead>
                    <tbody>

                    </tbody>
                </table>
                
                <button id="filter_tags" class=" filter_btn btn btn-primary">Filter tags</button>
                <button class="reset_filter btn btn-warning" id="reset_filter_tags">Reset tags filter</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>



        var json = JSON.parse(<?php echo $rows ?>);
        var filename = <?php echo $fileName ?>;

</script>

