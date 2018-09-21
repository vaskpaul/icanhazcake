        /*
         * ToDo List
         *
         * @author Marcos Lopez
         * 
         * ToDo mobile
         */

        /* ================================ 
         * VAR DECLARATION
         * ================================ */

        var $ft, //FooTable
                categoryList,
                statusList,
                tagList,
                filters,
                isSubtask;
        /* ================================ */

        $(document).ready(function () {

                categoryList = JSON.parse(window.app.categories);
                statusList = JSON.parse(window.app.statusList);
                tagList = JSON.parse(window.app.tagList);
                filters = JSON.parse(window.app.filters);

                /**
                 * Load settings
                 */

                generateSettingsModal(statusList, "status", "Status");
                generateSettingsModal(categoryList, "categories", "Categories");
                generateSettingsModal(tagList, "tag", "Tags");

                //Initialize table

                initializeFootable();

                /**
                 * @Funcionality: Click the checkbox will cross out the row and disable
                 * all his elements to not allow future changes.
                 * 
                 * Click again will revert to the original state.
                 */
                $("#mobile_table").on("click", "input:checkbox", function () {

                        contractAllRows();

                        var row = $(this).parents("tr");
                        row.addClass("changed");

                        if ($(this).prop("checked")) {
                                completeSubtasksRecursion(row.find('.id').text());
                                strikeout(row);

                        } else {
                                row.find('*').removeClass("strikeout").prop("disabled", false);
                        }

                        var today = new Date();


                        $(".changed").each(function () {
                                var tr_id = $(this).find("td.id").text();

                                var arr = $.grep($ft.rows.all, function (v) {
                                        return tr_id === v.value.id
                                });

                                var values = arr[0].value;

                                $tasks.push(values.id);


                        })

                        $.ajax({
                                method: "POST",
                                url: "/taskListMobile/saveXml/",
                                data: {
                                        tasks: JSON.stringify(json_object)
                                }
                        }).done(function () {
                                console.log('Updated (percentage)');

                                $(".changed").removeClass(".changed");

                        }).fail(function () {
                                console.log('There have been one problem trying to update the rows.')
                        });

                });

                /**
                 * Cross out a row an disable its cells
                 * 
                 * @param {tr} row
                 * 
                 * @returns {undefined}
                 */
                function strikeout(row) {
                        row.find(':not(.row-checkbox, .row-checkbox > *)')
                                .addClass("strikeout")
                                .prop("disabled", true);
                        row.find(".row-checkbox > input").prop("checked", true);
                        row.find("td.percentage").text(100).trigger("change");

                        var arr = $.grep($ft.rows.all, function (v) {
                                return row.find('.id').text() === v.value.id
                        });

                        arr[0].value.percentage = "100";
                }

                /**
                 * 
                 * @param {type} parentId
                 * @returns {undefined}
                 */
                function completeSubtasksRecursion(parentId) {
                        $.each($('.' + parentId), function () {
                                if ($('.' + $(this).find('.id').text()).length) {
                                        completeSubtasksRecursion($(this).find('.id').text());
                                }

                                strikeout($(this));
                        });
                }

                /**
                 * Update parents row percentage depending on their children percentage
                 */
                $("#mobile_table").on("change", "td.percentage", function (event) {
                        recalculatePercentage($(this).parents("tr"));
                });

                /**
                 * Updates parent's rows percentage with recursion
                 *
                 * @param jQuery Object element row whose percentage has been modified
                 *
                 */
                function recalculatePercentage(element) {
                        var classes = element.attr('class').split(/\s+/);
                        var sum = 0;

                        $("." + classes[1]).addClass("changed").find("td.percentage").each(function () {
                                sum += parseInt($(this).text());
                        });


                        var total = Math.round(sum / $("." + classes[1]).length);
                        $("tr").filter(function () {
                                return $(this).find("td.id").text() === classes[1];
                        }).find("td.percentage").text(total);


                        if ($("#" + classes[1]).length)
                                recalculatePercentage($("#" + classes[1]));
                }


                /**
                 * Reset settings modal (Bootstrap) and load with the pertinent data
                 * 
                 * @param var variable to take data
                 * @param String name for classes
                 * @param String title Modal Title
                 * 
                 */
                function generateSettingsModal(variable, name, title) {

                        $("#list_of_" + name + " > tbody").empty();

                        $.each(variable, function (key, value) {

                                var checkbox;

                                /**
                                 * Load saved filters into modal by checking them
                                 */

                                if ($.inArray(value.trim(), filters[name]) !== -1 && (filters[name].length > 0)) {
                                        checkbox = "<input type='checkbox' checked />";
                                } else {
                                        checkbox = "<input type='checkbox' />";
                                }


                                $("#list_of_" + name + " > tbody")
                                        .append("<tr><td>" + checkbox +
                                                "<td class='" + name + "_name'>" + value + "</td>" +
                                                "<td><button class='delete_" + name + " btn btn-danger btn-xs'>Delete</button></td></tr>");
                        });

                        $("#" + name + "_modal h4.modal-title").text(title);
                        $("#" + name + "_first_head").text(title + " name");

                        $('.delete_' + name).on("click", function (e) {
                                e.preventDefault();

                                var parent = $(this).parents("tr");
                                var i = variable.indexOf(parent.find('.' + name + '_name').text());

                                if (i !== -1) {
                                        variable.splice(i, 1);
                                }

                                parent.remove();
                        });


                }

                /* ================================ 
                 * FILTERING
                 * ================================ */

                //Load pre-filter settings



                /**
                 * Click any filter button (filter modals) will
                 * filter the whole table and close the modal windows
                 */
                $("button.filter_btn").on("click", function () {
                        filterTable();
                        $(".modal").modal("hide");
                });

                $("button#hide_completed_btn").on("click", function () {

                        if ($("#hide_completed_btn").hasClass("btn-warning")) {

                                $("#hide_completed_btn").removeClass("btn-warning").addClass("btn-default");

                        } else {

                                $("#hide_completed_btn").removeClass("btn-default").addClass("btn-warning");
                        }

                        filterTable();
                });




                $(".reset_filter").hide(); //Reset buttons starts as hidden

                $(".modal").on('hidden.bs.modal', function (e) {

                })


                //Open modal window with all the statuses

                $("#open_status").on("click", function () {
                        $("#status_modal").modal("show");
                });

                //Open modal window with all the categories

                $("#open_categories").on("click", function () {
                        $("#categories_modal").modal("show");
                });

                //Open modal window with all the tags

                $("#open_tags").on("click", function () {
                        $("#tag_modal").modal("show");
                });

                //Remove all filters affecting status column

                $("#reset_filter_status").on("click", function () {
                        $.each($("#list_of_status > tbody > tr input[type=checkbox]:checked"), function () {
                                $(this).prop("checked", false);
                        });
                        $("#open_status").removeClass("btn-warning").addClass("btn-default");
                        filterTable();
                        $(this).hide();
                        $(".modal").modal("hide");
                });

                //Remove all filters affecting category column

                $("#reset_filter_cats").on("click", function () {
                        $.each($("#list_of_categories > tbody > tr input[type=checkbox]:checked"), function () {
                                $(this).prop("checked", false);
                        });
                        $("#open_categories").removeClass("btn-warning").addClass("btn-default");
                        filterTable();

                        $(this).hide();
                        $(".modal").modal("hide");
                });

                //Remove all filters affecting tag column

                $("#reset_filter_tags").on("click", function () {
                        $.each($("#list_of_tag > tbody > tr input[type=checkbox]:checked"), function () {
                                $(this).prop("checked", false);
                        });
                        $("#open_tags").removeClass("btn-warning").addClass("btn-default");
                        filterTable();

                        $(this).hide();
                        $(".modal").modal("hide");
                });

                /* ================================ */


                /* ================================ 
                 *      TASK FORM
                 * ================================ */

                //Colorpicker

                $('#form_colorpicker').simplecolorpicker({
                        theme: 'fontawesome'
                });

                //Form cancel btn

                $("#cancel").on("click", function () {
                        formToTable();

                });

                //Personalized ComboBox

                $("#form_status").ComboBox();

                //Bootstrap datepicker

                $("#form_datepicker").datepicker({
                        format: "dd/mm/yyyy",
                        'minDate': '01/01/2010',
                        todayHighlight: true
                }).on('changeDate', function (e) {
                        $(this).datepicker("hide");
                });


                //Form fades out and table fade in again

                function formToTable() {
                        $("#mobile_form").fadeOut(500, function () {
                                $("#normal_screen").fadeIn(500);
                        });
                }

                //Form on submitting

                $("#form_submit").on("click", function (e) {

                        e.preventDefault();
                        var row = $("#mobile_form").data('row');

                        $("#autocomplete_cats").val($("#autocomplete_cats").val().replace(/\s*[,]\s*/g, ",").replace(/,+/g, ",").replace(/[,]$/g, ""));
                        $("#autocomplete_tags").val($("#autocomplete_tags").val().replace(/\s*[,]\s*/g, ",").replace(/,+/g, ",").replace(/[,]$/g, ""));

                        var tasks = [];
                        tasks.push({
                                "title": $("#form_title").val(),
                                "color": $("#form_colorpicker").val(),
                                "priority": $("#form_priority").val(),
                                "percentage": $("#form_percentage").val(),
                                "due_date": $("#form_datepicker").val(),
                                "status": $("#status_form_group .combobox").val(),
                                "category": $("#autocomplete_cats").val(),
                                "tag": $("#autocomplete_tags").val(),
                                "comment": $("#form_comment").val()
                        });

                        var tzoffset = (new Date()).getTimezoneOffset() * 60000; //offset in milliseconds
                        var now = (new Date(Date.now() - tzoffset)).toISOString().replace("T", " ").replace("Z", " ");

                        if (row instanceof FooTable.Row) { //edit
                                tasks[0].id = $("#form_id").val();

                                $.ajax({
                                        method: "POST",
                                        url: (location.href).replace("#", "") + "/savetasks",
                                        data: {
                                                tasks: JSON.stringify(tasks),
                                                lastMod: now
                                        }
                                }).done(function () {

                                        $('#mobile_table').removeClass().addClass('table footable');
                                        $('#mobile_table tbody, #mobile_table thead').empty();
                                        $('#mobile_table tfoot').remove();

                                        initializeFootable();

                                        console.log("XML saved successfully");

                                }).fail(function () {
                                        console.log("There was an error saving.");
                                });

                        } else { //add

                                if (isSubtask) {
                                        tasks[0].parent_task_id = $("#form_parent_id").val();
                                        tasks[0].task_lvl = $("#form_task_lvl").val();
                                }

                                //add something to check if it's a subtask
                                $.ajax({
                                        method: "POST",
                                        url: (location.href).replace("#", "") + "/add",
                                        data: {
                                                tasks: JSON.stringify(tasks),
                                        }
                                }).done(function () {

                                        $('#mobile_table').removeClass().addClass('table footable');
                                        $('tbody, thead').empty();
                                        $('tfoot').remove();

                                        initializeFootable();

                                        console.log("XML saved successfully");

                                }).fail(function () {
                                        console.log("There was an error saving.");
                                });

                                isSubtask = false;
                        }

                        categoryList = updateElements(categoryList, $("#autocomplete_cats").val());
                        tagList = updateElements(tagList, $("#autocomplete_tags").val());

                        $("#autocomplete_cats").autocomplete("option", "source", categoryList);
                        $("#autocomplete_tags").autocomplete("option", "source", tagList);



                        formToTable();

                });

                $("#autocomplete_cats")
                        // don't navigate away from the field on tab when selecting an item
                        .bind("keydown", function (event) {
                                if (event.keyCode === $.ui.keyCode.TAB &&
                                        $(this).autocomplete("instance").menu.active) {
                                        event.preventDefault();
                                }
                        })
                        .autocomplete({
                                minLength: 0,
                                source: function (request, response) {
                                        // delegate back to autocomplete, but extract the last term
                                        response($.ui.autocomplete.filter(
                                                categoryList, extractLast(request.term)));
                                },
                                focus: function () {
                                        // prevent value inserted on focus
                                        return false;
                                },
                                select: function (event, ui) {
                                        var terms = split(this.value);
                                        // remove the current input
                                        terms.pop();
                                        // add the selected item
                                        terms.push(ui.item.value);
                                        // add placeholder to get the comma-and-space at the end
                                        terms.push("");
                                        this.value = terms.join(", ");
                                        return false;
                                }
                        }).focus(function () {
                        $(this).autocomplete("search");
                });



                $("#autocomplete_tags")
                        // don't navigate away from the field on tab when selecting an item
                        .bind("keydown", function (event) {
                                if (event.keyCode === $.ui.keyCode.TAB &&
                                        $(this).autocomplete("instance").menu.active) {
                                        event.preventDefault();
                                }
                        })
                        .autocomplete({
                                minLength: 0,
                                source: function (request, response) {
                                        // delegate back to autocomplete, but extract the last term
                                        response($.ui.autocomplete.filter(
                                                tagList, extractLast(request.term)));
                                },
                                focus: function () {
                                        // prevent value inserted on focus
                                        return false;
                                },
                                select: function (event, ui) {
                                        var terms = split(this.value);
                                        // remove the current input
                                        terms.pop();
                                        // add the selected item
                                        terms.push(ui.item.value);
                                        // add placeholder to get the comma-and-space at the end
                                        terms.push("");
                                        this.value = terms.join(", ");
                                        return false;
                                }
                        }).focus(function () {
                        $(this).autocomplete("search");
                });

                /* ================================ 
                 *     END TASK FORM
                 * ================================ */

        });

        /**
         * Update settings (status, categories, tags)
         * 
         * */
        function updateElements(array, value) {
                value = value.replace(/\s/g, "").replace(/,,/, ",").replace(/[,]$/, "");

                var elements = value.split(",");
                for (var i = 0, long = elements.length; i < long; i++) {
                        if ($.inArray(elements[i], array) === -1)
                                array.push(elements[i]);
                }

                return array;
        }

        /**
         * jQueryUI autocomplete required function
         **/
        function split(val) {
                return val.split(/,\s*/);
        }

        /**
         * jQueryUI autocomplete required function
         **/
        function extractLast(term) {
                return split(term).pop();
        }

        /**
         * Trigger change on a element after modifying its value
         * @param {type} v
         * @param {type} event
         * @returns {jQuery}
         */
        $.fn.changeVal = function (v, event) {
                return $(this).val(v).trigger("change");
        }

        /**
         * Contract all footable rows
         * 
         */
        function contractAllRows() {
                for (var i = 0, long = $ft.rows.all.length; i < long; i++) {
                        if ($ft.rows.all[i].expanded)
                                $ft.rows.all[i].toggle();
                }
        }

        /**
         * Filter table 
         * 
         * @returns {undefined}
         */
        function filterTable() {

                contractAllRows()

                $("#mobile_table > tbody>tr").css("display", "table-row");

                if ($("#hide_completed_btn").hasClass("btn-warning")) {

                        $("#mobile_table > tbody >tr").filter(function () {
                                return $(this).find(".percentage").text() === '100'
                        }).css("display", "none");

                }

                var status_checks = [];

                $.each($("#list_of_status > tbody > tr"), function () {
                        if ($(this).find("input:checkbox").prop("checked")) {
                                status_checks.push($(this).find(".status_name").text());
                        }
                });

                if (status_checks.length > 0) {

                        $("#open_status").removeClass("btn-default").addClass("btn-warning");
                        $("#reset_filter_status").show();

                        $.each($("#mobile_table > tbody >tr").filter(function () {
                                return $(this).css('display') !== 'none';
                        }), function (key, value) {

                                var statusValue = ($(this).find(".status").text());
                                var boolean = false;

                                if ($.inArray(statusValue, status_checks) == -1) {
                                        $(this).css("display", "none");
                                } else {
                                        if (!$(this).hasClass("task")) {
                                                filterDisplayRecursion($(this))
                                        }
                                }
                        });
                }


                var category_checks = [];

                $.each($("#list_of_categories > tbody > tr"), function () {
                        if ($(this).find("input:checkbox").prop("checked")) {
                                category_checks.push($(this).find(".categories_name").text());
                        }
                });


                if (category_checks.length > 0) {

                        $("#open_categories").removeClass("btn-default").addClass("btn-warning");
                        $("#reset_filter_cats").show();

                        $.each($("#mobile_table > tbody>tr").filter(function () {
                                return $(this).css('display') != 'none';
                        }), function () {

                                var cat_split = ($(this).find(".categories").text()).split(",");
                                var boolean = false;

                                $.each(category_checks, function (key, value) {

                                        if ($.inArray(value, cat_split) !== -1) {
                                                boolean = true;
                                        }
                                });


                                if (!boolean) {
                                        $(this).css("display", "none");
                                } else {
                                        if (!$(this).hasClass("task")) {
                                                filterDisplayRecursion($(this))
                                        }
                                }

                        });

                }

                var tag_checks = [];

                $.each($("#list_of_tag > tbody > tr"), function () {
                        if ($(this).find("input:checkbox").prop("checked")) {
                                tag_checks.push($(this).find(".tag_name").text());
                        }
                });

                if (tag_checks.length > 0) {

                        $("#open_tags").removeClass("btn-default").addClass("btn-warning");
                        $("#reset_filter_tags").show();

                        $.each($("#mobile_table > tbody>tr").filter(function () {
                                return $(this).css('display') != 'none';
                        }), function () {

                                var tags_split = ($(this).find(".tags").text()).split(",");

                                var boolean = false;

                                $.each(tag_checks, function (key, value) {

                                        if ($.inArray(value, tags_split) !== -1) {
                                                boolean = true;
                                        }
                                });

                                if (!boolean) {
                                        $(this).css("display", "none");
                                } else {
                                        if (!$(this).hasClass("task")) {
                                                filterDisplayRecursion($(this))
                                        }
                                }
                        });
                }

                $.ajax({
                        method: "POST",
                        url: location.href.replace("mobile", "tasklist") + "/" + "filter",
                        data: {
                                'filter_status': status_checks.join(","),
                                'filter_categories': category_checks.join(","),
                                'filter_tags': tag_checks.join(","),
                                'filter_complete': $("#hide_completed_btn").hasClass("btn-warning") ? 1 : 0
                        }
                });


        }


        /**
         * Make visible parents of a subtask that fullfil filter parameters
         * 
         * @param {type} element
         */
        function filterDisplayRecursion(element) {
                while (element.length) {
                        classes = element.attr('class').split(/\s+/);
                        element = $("#" + classes[1]);
                        element.css("display", "table-row");
                }
        }


        /**
         * Transform a span into a combobox
         * Needs jQuery and Bootstrap
         * 
         * @returns 
         */
        $.fn.ComboBox = function () {
                $div = $("<div>", {
                        class: "input-group dropdown"
                });

                $input = $("<input>", {
                        type: "text",
                        class: "form-control dropdown-toggle combobox",
                        value: $(this).text()
                }).on("blur", function () {
                        $(this).closest("tr").addClass("changed");
                        var status = $(this).val();

                        if ($.inArray(status, statusList) === -1) {
                                statusList.push(status);
                                $("#filter_status").append("<option value='" + status + "'>" + status + "</option>");
                                $("#filter_status").multiselect('rebuild');
                        }
                });

                $ul = $("<ul>", {
                        class: "dropdown-menu"
                });

                $.each(statusList, function (key, value) {

                        $li = $("<li>").on("click", function () {
                                console.log($(this).text())
                        });

                        $a = $("<a>", {
                                href: "#",
                                text: value
                        }).on("click", function (e) {
                                e.preventDefault();
                                $(this).closest('.dropdown').find('input.combobox')
                                        .val($(this).text());
                        });


                        $ul.append($li.append($a));
                });

                $span = $("<span>", {
                        role: "button",
                        class: "input-group-addon dropdown-toggle",
                        "data-toggle": "dropdown",
                        "aria-haspopup": true,
                        "aria-expanded": false
                }).append("<span class=caret></span>");

                $div.append($input).append($ul).append($span);

                $(this).replaceWith($div);

        }

       /**
        * Instanciate a FooTable object and assign it to $ft
        * 
        * After initialization make first row changes (cross out, color...)
        */
        function initializeFootable() {
                $ft = FooTable.init('#mobile_table', {
                        'editing': {
                                'enabled': true,
                                'alwaysShow': true,
                                "column": {
                                        "classes": "footable-editing",
                                        "name": "editing",
                                        "title": "Actions",
                                        "filterable": false,
                                        "sortable": false
                                },
                                'addText': "+",
                                'editText': '<span class="fa fa-pencil" aria-hidden="true"></span>',
                                'deleteText': '<span class="fa fa-trash-o" " aria-hidden="true"></span>',
                                'addRow': function () {

                                        isSubtask = false;
                                        $("#mobile_form").removeData('row');
                                        $("#form")[0].reset();
                                        $("#form_header").text("Add new task");
                                        $("#form_submit").val("Create new task");

                                        $("#normal_screen").fadeOut(500, function () {
                                                $("#mobile_form").fadeIn(500);

                                        });

                                },
                                'editRow': function (row) {

                                        isSubtask = false;
                                        $("#form")[0].reset();

                                        var values = row.val();

                                        $("#form_id").val(values.id);
                                        $('#form_colorpicker').simplecolorpicker('selectColor', values.color);
                                        $("#form_title").val(values.title);
                                        $("#form_priority").val(values.priority);
                                        $("#form_percentage").val(values.percentage);
                                        $("#form_datepicker").val(values.date);
                                        $("#status_form_group .combobox").val(values.status);
                                        $("#autocomplete_cats").val(values.category);
                                        $("#autocomplete_tags").val(values.tags);
                                        $("#form_comment").val(values.comment);

                                        $("#mobile_form").data('row', row);
                                        $("#form_header").text("Edit task");
                                        $("#form_submit").val("Save task");

                                        $("#normal_screen").fadeOut(500, function () {
                                                $("#mobile_form").fadeIn(500);

                                        })

                                },
                                'deleteRow': function (row) {
                                        function childRecursionRemoving(id, idsToRemove) {
                                                var arr = $.grep($ft.rows.all, function (v) {
                                                        return $.inArray(id, v.classes) !== -1
                                                });

                                                $.each(arr, function (key, value) {
                                                        childRecursionRemoving(value.value.id, idsToRemove);

                                                });

                                                idsToRemove.push(id);

                                                return idsToRemove;
                                        }

                                        var confirm = window.confirm("Are you sure you want to delete this task? It'll delete all its subtasks.");
                                        if (!confirm) {
                                                return false;
                                        }

                                        idsToRemove = childRecursionRemoving(row.val().id, []);

                                        $.ajax({
                                                method: "POST",
                                                url: "/TaskList/deleteTasks/",
                                                data: {
                                                        deleted: JSON.stringify(idsToRemove)
                                                }
                                        }).done(function () {
                                                console.log("Tasks deleted successfully");

                                                row.delete(); //delete with recursion too


                                        }).fail(function () {
                                                console.log("An error was encountered while trying to delete the tasks");
                                        });
                                }
                        },
                        "columns": $.get("/js/json/mobile_cols.json"),
                        "rows": $.ajax(location.href + "/loadrows", {
                                dataType: 'json'
                        })
                });

                checkInitialize();
        }

        function checkInitialize() {
                if (!$ft.initialized) {
                        setTimeout(checkInitialize, 500); // setTimeout(func, timeMS, params...)
                } else {
                        initializingRowChanges();
                }
        }

        function initializingRowChanges() {

                $('tr').each(function (index, value) {
                        if (index !== 0 || index !== $('tr').length - 1) {

                                var row = $(this);

                                if (row.find('.percentage').text() === '100') {

                                        row.find(':not(.row-checkbox, .row-checkbox >*)').addClass("strikeout").prop("disabled", true);
                                        row.find("input:checkbox").prop("checked", true);
                                }

                                row.find('.title').css("color", row.find('.color').text());

                        }

                });

                /**
                 * 
                 */
                $("button.subtask").on("click", function () {

                        isSubtask = true;

                        var row = $(this).parents("tr").prev();

                        if (!row.hasClass("subtask3")) {
                                $("#mobile_form").removeData('row');

                                $("#form")[0].reset();
                                $("#form_header").text("Add new subtask");
                                $('#submit').val("New subtask");


                                $("#form_parent_id").val(row.find(".id").text());
                                $('#form_colorpicker').simplecolorpicker('selectColor', row.find(".color").text());

                                var task_lvl = 0;

                                if (row.hasClass("task"))
                                        task_lvl = 1;
                                else if (row.hasClass("subtask"))
                                        task_lvl = 2;
                                else if (row.hasClass("subtask2"))
                                        task_lvl = 3;

                                $("#form_task_lvl").val(task_lvl);


                                $("#normal_screen").fadeOut(500, function () {
                                        $("#mobile_form").fadeIn(500);

                                });

                        } else {
                                alert("Sorry. There can't be level 4 child tasks.")
                        }
                });

                if (parseInt(filters.completed) === 1) {
                        $("#hide_completed_btn").removeClass("btn-default").addClass("btn-warning");
                }

                filterTable();
        }