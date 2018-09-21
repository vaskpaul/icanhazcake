        /*
         * ToDo List
         *
         * @author Marcos Lopez
         */

/* TODOLIST VARIABLE DEFINITION
 * ================================ */

var tasks;
var table;
var nextUniqueId;
var categoryList;
var statusList;
var tagList;
var json;

/* ================================ */

$(document).ready(function () {

    tasks = JSON.parse(window.app.tasks);
    categoryList = JSON.parse(window.app.categories);
    statusList = JSON.parse(window.app.statusList);
    tagList = JSON.parse(window.app.tagList);


    /* TABLE RENDERING
     * ================================ */

    $.each(tasks, function () {
        firstLoadRenderRows($(this)[0]);
    });


    $("#tasklist_table").stickyTableHeaders({
        fixedOffset: parseInt($(".fixed").css("height").replace("px", ""))
    });

    $("tr").each(function () {
        if ($(this).find(".percentage").val() === "100") {
            $(this).find(':not(.row-checkbox, .row-checkbox > *)').addClass("strikeout").prop("disabled", true);
            $(this).find("input:checkbox").prop("checked", true);
        }
    });

    $("span.comment").filter(function () {
        return $(this).text() !== ""
    }).each(function () {
        $(this).next().removeClass("btn-default").addClass("btn-warning");
    })

    $("span.link").filter(function () {
        return $(this).text() !== ""
    }).each(function () {
        $(this).next().removeClass("btn-default").addClass("btn-warning");
    })

    blockPercentages();

    $(".strikeout").prop("disabled", true);
    $('.changed').removeClass("changed");

    /**
     * Create a row and append it to the table
     *
     * @param {json} data
     * @param {String} classes
     */
    function firstLoadRenderRows(data) {

        var classes = "";

        switch (parseInt(data.task_lvl)) {
        case 0:
            classes = "task";
            break;
        case 1:
            classes = "subtask " + data.parent_task_id;
            break;
        case 2:
            classes = "subtask2 " + data.parent_task_id;
            break;
        case 3:
            classes = "subtask3 " + data.parent_task_id;
            break;

        }


        var row = createRow(data, classes);

        if ($(data.percentage).val() === "100") {
            row.find(':not(.row-checkbox, .row-checkbox > *)').addClass("strikeout");
            row.find("input:checkbox").prop("checked", true);
        }

        if (!row.hasClass("task")) {
            var classes = row.attr('class').split(/\s+/);
            $("#" + classes[1]).after(row);
            recalculatePercentage(row);
        } else {
            $("#tasklist_table tbody").append(row);
        }

        $('select[name="colorpicker"]').simplecolorpicker({
            picker: true
        }).on('change', function () {
            $(this).parents("tr").find(".title").css("color", $(this).val());
        });

        $('.status').ComboBox();

        row.find("span.title").css("color", data.color).click();
    }

    /**
     * Generate a table row from task data
     *
     * @param {array} data
     * @param {String} classes
     * 
     */
    function renderRow(data, classes) {

        var row = createRow(data, classes);

        if ($(data.percentage).val() === "100") {
            row.find(':not(.row-checkbox, .row-checkbox > *)').addClass("strikeout").prop("disabled", true);
            row.find("input:checkbox").prop("checked", true);
        }

        if (!row.hasClass("task")) {
            var classes = row.attr('class').split(/\s+/);
            $("#" + classes[1]).after(row);
            recalculatePercentage(row);
        } else {
            $("#tasklist_table tbody").prepend(row);
        }

        $('select[name="colorpicker"]').simplecolorpicker({
            picker: true
        }).on('change', function () {
            $(this).parents("tr").find(".title").css("color", $(this).val());
        });

        $(".status").ComboBox();
        row.find("span.title").click();

        updateShowingRows();
    }

    /**
     * Create a new task row
     * 
     * @param js object data
     * @param string classes
     * 
     * @returns $ jQuery tr object
     */
    function createRow(data, classes) {

        var row = $("<tr id='" + data.id + "'/>");

        row.renderCell("<input type=checkbox />", 'row-checkbox cols-fit', null, null);
        row.renderCell(data.title, 'td-title', 'span', 'title');
        row.renderCell(renderColor(data.color), 'td-color cols-fit', 'a', null);
        row.renderCell(renderPriority(data.priority), 'cols-fit', 'a', null);
        row.renderCell('<input type=number min=0 max=100 step=5 class=percentage value="' + data.percentage + '"/>', 'cols-fit', 'a', null);
        row.renderCell(data.due_date, 'cols-date', 'span', 'date');
        row.renderCell(data.status, 'status_td', 'span', 'status');
        row.renderCell(data.category, null, 'span', 'categories');
        row.renderCell(data.tag, null, 'span', 'tags');
        row.renderCell('<span class="comment">' + data.comment + '</span><button class="fa fa-commenting btn btn-default" aria-hidden="true"></button>', 'cols-fit', null, null);
        row.renderCell('<span class="link">' + data.link + '</span><button class="fa fa-link btn btn-default" aria-hidden="true"></button>', 'cols-fit', null, null);
        row.renderCell('<button class="fa fa-trash deleteRow btn btn-danger " aria-hidden="true"></button>', 'cols-fit', null, null);
        row.renderCell('<button class="fa fa-level-down subtask btn btn-default " aria-hidden="true"></button>', 'cols-fit', null, null);

        row.addClass(classes);

        return row;
    }

    /**
     * Render a select tag with lvl 0-10 of priority
     * and select the pertinent option
     *
     * @param String data priority level to be selected
     *
     * @returns String containing a select tag
     */
    function renderPriority(priority) {
        var aux = "<select class='priority priority-lvl-" + priority + "'>";
        for (var i = 1; i <= 5; i++) {
            if (i === parseInt(priority)) {
                aux += "<option class='priority-lvl-" + i + "' selected value='" + i + "'>" + i + "</option>";
            } else {
                aux += "<option class='priority-lvl-" + i + "' value='" + i + "'>" + i + "</option>";
            }
        }
        return aux;
    }

    /**
     * Render a select tag with lvl 0-10 of priority
     * and select the pertinent option
     *
     * @param {String} data priority level to be selected
     *
     * @returns {String} containing a select tag
     */
    function renderStatus(status, id) {
        var $input = $("<select>", {
            id: 'status_' + id,
            class: "autocomplete_status",
        });
        $.each(statusList, function (key, value) {
            if (status !== value) {
                $input.append("<option value='" + value + "'>" + value);
            } else {
                $input.append("<option value='" + value + "' selected>" + value);
            }
        });
        return $input;
    }

    /**
     * Render colorpicker plugin
     *
     * @param {String} color HEX format
     * 
     * @returns {jQuery Object} select 
     */
    function renderColor(color) {

        $select = $('<select>', {
            'name': 'colorpicker'
        });

        var colours = {
            'black': "#000",
            'blue': "#2F8594",
            'yellow': "#DDBC21",
            'orange': "#FF7802",
            'purple': '#A41291',
            'red': '#BA1331'
        }

        $.each(colours, function (key, value) {
            if (color === value)
                $select.append('<option value="' + value + '" selected>' + key + '</option>');
            else
                $select.append('<option value="' + value + '" >' + key + '</option>');
        });

        return $select;
    }

    /* ================================ */

    /**
     * Save modified rows into server
     * 
     * @param {Function} callback function to call at ajax success
     * 
     */
    function saveList(callback) {

        var tasks = [],
            deleted = [];

        $("tr.changed").each(function (trIndex) {

            //Need to remove disabled property (completed tasks)
            //so we can read values

            var percentage;

            if ((percentage = $(this).find(".percentage").val()) == "100") {
                $(this).find("*").removeAttr("disabled");
            }

            tasks.push({
                "id": $(this).attr("id"),
                "title": $(this).find(".title").text(),
                "color": $(this).find("select[name=colorpicker]").val(),
                "priority": $(this).find(".priority").val(),
                "percentage": percentage,
                "due_date": $(this).find(".date").text().length > 0 ? $(this).find(".date").text() : "NULL",
                "status": $(this).find(".combobox").val(),
                "category": $(this).find(".categories").text(),
                "tag": $(this).find(".tags").text(),
                'comment': $(this).find(".comment").text(),
                'link': $(this).find('.link').text()
            });

            if (percentage == "100") {
                $(this).find("*").prop("disabled", true);
            }
        });

        var tzoffset = (new Date()).getTimezoneOffset() * 60000; //offset in milliseconds
        var now = (new Date(Date.now() - tzoffset)).toISOString().replace("T", " ").replace("Z", " ");

        $.ajax({
            method: "POST",
            url: (location.href).replace("#", "") + "/savetasks",
            data: {
                tasks: JSON.stringify(tasks),
                lastMod: now
            }
        }).done(function () {

            $("#lastmod").text(now.split(".")[0]);
            $(".deleted").remove();
            $(".changed").removeClass("changed");

            callback();

            console.log("TaskList saved successfully");

        }).fail(function () {

            console.log("An error was encountered while trying to save the TaskList");
        });
    }

    /**
     * Automatic saving each 15 segs
     * 
     */
    setInterval(function () {
        saveList(function () {})
    }, 15000);

    /**
     * Save table before going back
     * 
     */
    $('#saveGoBack').on("click", function (e) {

        e.preventDefault();
        saveList(function () {
            window.location = '/list';
        });
    });

    /**
     * Add the class changed to the rows that aren't new ones
     * and has suffered any modification in one of their cells
     */
    $("#tasklist_table").on("change", "tr", function () {
        $(this).addClass("changed");
    });

    /**
     * Add a new row to the table
     * 1. Add a row to the database and return the new id
     * 2. Render a row into the table
     */
    $("#addRow").on("click", function () {

        $.ajax({
            'url': location.href + "/add",
            'method': "GET"

        }).done(function (id) {

            var rowData = {
                "id": id,
                "title": 'Task',
                "priority": 3,
                "percentage": 0,
                'color': "#000",
                'comment': "",
                'link': ""
            };

            renderRow(rowData, 'task');
        });
    });

    /**
     * Add a subrow/subtask to the table
     * 1. Add a row to the database and return the new id
     * 2. Render a row into the table
     */
    $("#tasklist_table").on("click", "button.subtask", function () {

        var parent = $(this).parents("tr"),
            childclass,
            task_lvl;

        if (parent.hasClass("task")) {
            childClass = "subtask";
            task_lvl = 1;
        } else if (parent.hasClass("subtask")) {
            childClass = "subtask2";
            task_lvl = 2;
        } else if (parent.hasClass("subtask2")) {
            childClass = "subtask3";
            task_lvl = 3;
        } else {
            alert("Sorry. There can't be level 4 child tasks.")
            return false;
        }

        $.ajax({
                'url': location.href + "/add",
                'method': "POST",
                data: {
                    'parent_id': parent.attr("id"),
                    'task_lvl': task_lvl
                }
            })
            .done(function (data) {
                var rowData = {
                    "id": data,
                    "title": 'Task',
                    "priority": 3,
                    "percentage": 0,
                    'color': parent.find("select[name=colorpicker]").val(),
                    'comment': "",
                    'link': ""
                };


                childClass += " " + parent.attr("id");
                renderRow(rowData, childClass);
                blockPercentages();
            });

    });

    /**
     * Remove a task from the table + database
     * Applies recursion to remove descendant rows too
     */
    $("#tasklist_table").on("click", "button.deleteRow", function () {

        var confirm = window.confirm("Are you sure you want to delete this task? It'll delete all its subtasks.");
        if (!confirm) {
            return false;
        }

        var idsToRemove = childRecursionRemoving($(this).parents("tr").attr("id"), []);
        idsToRemove.push($(this).parents("tr").attr("id"));
        $(this).parents("tr").addClass("deleted");

        $.ajax({
            method: "POST",
            url: "/TaskList/deleteTasks/",
            data: {
                deleted: JSON.stringify(idsToRemove)
            }
        }).done(function () {
            $(".deleted").remove();
            updateShowingRows();
            console.log("Tasks deleted successfully");

        }).fail(function () {
            $(".deleted").removeClass("deleted");
            console.log("An error was encountered while trying to delete the tasks");
        });

        blockPercentages();

    });

    /**
     * Add class "deleted" to child rows with recursion
     *
     * @param {String} id parent id
     *
     */
    function childRecursionRemoving(id, idsToRemove) {

        $("." + id).each(function () {
            idsToRemove = childRecursionRemoving($(this).attr("id"), idsToRemove);
            $(this).addClass("deleted");
            idsToRemove.push($(this).attr("id"));

        });

        return idsToRemove;
    }

    /**
     * Click the checkbox will cross out the row and disable
     * all his elements to not allow future changes.
     *
     * Click again will revert to the original state.
     */
    $("#tasklist_table").on("click", "input:checkbox", function () {

        var row = $(this).parents("tr");
        if ($(this).prop("checked")) {

            if ($('.' + row.attr('id')).length) {
                var confirm = window.confirm("This action will complete all the subtasks belonging to this task. Are you sure you want to continue?");
                if (confirm) {
                    completeSubtasksRecursion(row.attr('id'));
                    strikeout(row);
                } else {
                    $(this).prop("checked", false);
                }

            } else {
                strikeout(row);
            }
        } else {
            if (!$('.' + row.attr('id')).find(".strikeout").length) {
                row.find('*').removeClass("strikeout").removeAttr("disabled");
                row.find(".percentage").changeVal(0);
            } else {

                $(this).prop("checked", true);
            }
        }

        //blockPercentages();
        //$(".strikeout").prop("disabled", true);
    });

    /**
     * Cross out a task & change its percentage to 100
     * 
     * @param {jQuery Object tr} row
     */
    function strikeout(row) {
        row.find('*:not(.row-checkbox, .row-checkbox > *)')
            .prop("disabled", true)
            .addClass("strikeout");
        row.find("input.percentage").changeVal(100);

    }

    /**
     * Recursion for completing tasks
     * 
     * @param {int} parentId
     */
    function completeSubtasksRecursion(parentId) {
        $.each($('.' + parentId), function () {
            if ($('.' + $(this).attr('id')).length) {
                completeSubtasksRecursion($(this).attr('id'));
            }

            strikeout($(this));
        });
    }

    /**
     * Update parents row percentage depending on their children percentage
     */
    $("#tasklist_table").on("change", "input.percentage", function (event) {

        recalculatePercentage($(this).parents("tr"));

        if ($(this).val() === "100" && !($(this).parents("tr").has('.strikeout').length)) {

            strikeout($(this).parents("tr"))
            $(this).parents("tr")
                .find("input:checkbox")
                .prop("checked", true);

        } else if ($(this).val() === "100" && ($(this).parents("tr").has('.strikeout').length)) {
            $(this).parents("tr")
                .find("input:checkbox")
                .prop("checked", true);
        }

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

        $("." + classes[1] + ":not(.deleted)").find("input.percentage").each(function () {
            sum += parseInt($(this).val());
        });

        var numOfChilds = $("." + classes[1] + ":not(.deleted)").length > 0 ? $("." + classes[1] + ":not(.deleted)").length : 1;
        var total = Math.round(sum / numOfChilds);
        $("#" + classes[1]).addClass("changed").find("input.percentage").val(total);

        if ($("#" + classes[1]).length)
            recalculatePercentage($("#" + classes[1]));
    }

    /**
     * Change the class on a select tag so the
     * background will change depending on the priority level
     */
    $("#tasklist_table").on("change", "select.priority", function () {
        $(this).removeClass().addClass("priority priority-lvl-" + $(this).val());
        $(this).css("color", "white");
    });

    /**
     * Make List Title editable
     */
    $("#list_title").on("click", function listTitleToInput() {

        var $input = $("<input>", {
            val: $(this).text(),
            type: "text",
            class: "navbar-brand",
            id: "list_title"
        }).on("blur", function () {
            if ($(this).val().length === 0)
                return false;

            $.ajax({
                method: "POST",
                url: location.href + "/title",
                data: {
                    title: $(this).val()
                }
            });

            var $span = $("<span>", {
                text: $(this).val(),
                class: "navbar-brand",
                id: "list_title"
            });

            $(this).replaceWith($span);
            $span.on("click", listTitleToInput);
        });

        $(this).replaceWith($input);
        var tmpStr = $input.val();
        $input.focus().val('').val(tmpStr);
    });

    $("#tasklist_table").on("click", "td:has(span.title)", titleToInput);
    $("#tasklist_table").on("click", "td:has(span.date)", dateToInput);
    $("#tasklist_table").on("click", "td:has(span.categories)", categoryToInput);
    $("#tasklist_table").on("click", "td:has(span.tags)", tagsToInput);

});

/**
 * TD rendering
 *
 * @param {String|int} data content
 * @param {String} td_class optional class for td tag
 * @param {String} tag optional tag to wrap content
 * @param {String} tag_class optional class for optional tag
 *
 */
$.fn.renderCell = function (data, td_class, tag, tag_class) {

    $td = $("<td>");

    if (td_class !== null) {
        $td.addClass(td_class)
    }

    if (tag !== null) {

        $tag = $("<" + tag + ">");

        if (tag_class !== null) {
            $tag.addClass(tag_class);
        }

        $(this).append($td.append($tag.append(data)));
    } else {
        $(this).append($td.append(data));
    }

}