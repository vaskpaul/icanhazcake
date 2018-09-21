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

                $("#read_table").stickyTableHeaders({
                        fixedOffset: parseInt($(".fixed").css("height").replace("px", ""))
                });



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

                $("#filter_status").multiselect({
                        nonSelectedText: 'Status',
                        delimiterText: ' or ',
                });

                $("#filter_status").multiselect('dataprovider', filters.status);

                $("#filter_categories").multiselect({
                        nonSelectedText: 'Categories',
                        delimiterText: ' or ',
                        dataprovider: filters.categories
                });

                $("#filter_categories").multiselect('dataprovider', filters.categories);

                $("#filter_tags").multiselect({
                        nonSelectedText: 'Tags',
                        delimiterText: ' or ',
                        dataprovider: filters.tags
                });

                $("#filter_tags").multiselect('dataprovider', filters.tags);

                if (parseInt(filters.completed)) {
                        $("#hide_completed").parent("label").addClass("active");
                        $("#hide_completed").prop("checked", true);
                }

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

        });


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
         * Instanciate a FooTable object and assign it to $ft
         * 
         * After initialization make first row changes (cross out, color...)
         */
        function initializeFootable() {
                $ft = $('#read_table').footable({
                        "columns": $.get("/js/json/read_cols.json"),
                        "rows": $.ajax("/mobile/" + (location.href).split("/")[5] + "/loadrows", {
                                dataType: 'json'
                        })
                });

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