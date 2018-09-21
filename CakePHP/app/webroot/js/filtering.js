        /*
         * ToDo List
         *
         * @author Marcos Lopez
         * 
         * FILTERING OPTIONS
         */

        var filters;

        $(document).ready(function () {

            filters = JSON.parse(window.app.filters);

            /* FILTERING OPTIONS
             * ================================ */

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

            $("#filter_btn").on("click", function (e) {
                e.preventDefault();
                filter();
            })

            $("#reset_filter").on("click", function (e) {
                e.preventDefault();
                $("#hide_completed").prop("checked", false);
                $("#hide_completed").parent("label").removeClass("active");
                $("#filter_status").multiselect('deselectAll', false);
                $("#filter_status").multiselect('updateButtonText');
                $("#filter_categories").multiselect('deselectAll', false);
                $("#filter_categories").multiselect('updateButtonText');
                $("#filter_tags").multiselect('deselectAll', false);
                $("#filter_tags").multiselect('updateButtonText');
                $("tbody > tr").not(".deleted").css("display", "table-row");

                $.ajax({
                    method: "POST",
                    url: location.href + "/" + "filter",
                    data: {
                        'filter_status': "",
                        'filter_categories': "",
                        'filter_tags': "",
                        'filter_complete': 0
                    }
                })


                $("#showing_rows").text($("#tasklist_table > tbody > tr:visible").length)
                $("#total_rows").text($("#tasklist_table > tbody > tr").length)
            });

            filter();

            /* ================================ */


            /**
             * Reset settings modal (Bootstrap) and load with the pertinent data
             * 
             * @param {var} variable to take data
             * @param {String} name for classes
             * @param {String} title Modal Title
             * 
             */
            function openSettings(variable, name, title) {
                $("#list_of_settings > tbody").empty();

                $.each(variable, function (key, value) {
                    $("#list_of_settings > tbody").append("<tr><td class='" + name + "_name'><span class='update_setting'>" + value + "</span></td><td><button class='delete_" + name + " btn btn-danger btn-xs'>Delete</button></td></tr>");
                });

                $("#settings_modal h4.modal-title").text(title);
                $("#settings_first_head").text(title + " name");
                $("#settings_modal").modal("show");

                $("span.update_setting").on("click", function settingToInput() {

                    var old_name = $(this).text();

                    var $input = $("<input>", {
                        val: $(this).text(),
                        type: "text",
                        class: "update_setting"

                    }).on("blur", function () {

                        if ($(this).val().length === 0) {

                            return false;
                        }

                        var new_name = $(this).val().trim();

                        $.ajax({
                            method: "POST",
                            url: (location.href).replace("#", "") + "/update/" + name,
                            data: {
                                'name': new_name,
                                'old_name': old_name
                            }

                        }).done(function () {
                            console.log("Setting modified successfully.");

                            var tag_name = title.toLowerCase();
                            $('option[value="' + old_name + '"]', $('#filter_' + tag_name))
                                .attr('label', new_name).attr('title', new_name).attr("value", new_name);
                            $('#filter_' + tag_name).multiselect("rebuild");

                            if ((k = variable.indexOf(old_name)) !== -1) {
                                window[name + 'List'][k] = new_name;
                            }

                            $("#tasklist_table >tbody > tr").each(function () {

                                if (tag_name === "status") {
                                    var setting = $(this).find("input.combobox");
                                    var content = setting.val();

                                    if (content === old_name) {
                                        setting.val(new_name);
                                    }
                                } else {
                                    var setting = $(this).find("." + tag_name);
                                    var content = (setting.text()).split(",");
                                    var k;

                                    if ((k = content.indexOf(old_name)) !== -1) {
                                        content[k] = new_name;
                                    }
                                    setting.text(content.join(","));

                                }

                                $(this).addClass("changed");
                                saveFilterDatabase();
                            });

                        });

                        var $span = $("<span>", {
                            text: $(this).val(),
                            class: "update_setting"
                        });

                        $(this).replaceWith($span);
                        $span.on("click", settingToInput);
                    });

                    $(this).replaceWith($input);
                    var tmpStr = $input.val();
                    $input.focus().val('').val(tmpStr);

                });

                $('.delete_' + name).on("click", function (e) {

                    e.preventDefault();

                    var parent = $(this).parents("tr");
                    var txt = parent.find('.' + name + '_name').text();
                    var i = variable.indexOf(txt);

                    if (i !== -1) {
                        variable.splice(i, 1);

                        var url = (location.href).replace("#", "") + "/delete/" + name;

                        $.ajax({
                                method: "POST",
                                url: url,
                                data: {
                                    name: txt
                                }
                            }).done(function () {
                                console.log("Setting deleted successfully.");

                                var tag_name = title.toLowerCase();
                                $('option[value="' + txt + '"]', $('#filter_' + tag_name)).remove();
                                $('#filter_' + tag_name).multiselect("rebuild");

                                parent.remove();

                                //remove setting existing in tasks

                                $("#tasklist_table >tbody > tr").each(function () {

                                    if (tag_name === "status") {
                                        var setting = $(this).find("input.combobox");
                                        var content = setting.val();

                                        if (content === txt) {
                                            setting.val("");
                                        }
                                    } else {
                                        var setting = $(this).find("." + tag_name);
                                        var content = (setting.text()).split(",");
                                        var k;

                                        if ((k = content.indexOf(txt)) !== -1) {
                                            content.splice(k, 1);
                                        }
                                        setting.text(content.join(","));

                                    }

                                    $(this).addClass("changed");
                                    saveFilterDatabase();
                                });

                            })
                            .fail(function () {
                                console.log("Error saving setting")
                            });
                    }
                });
            }

            //Open modal window with all the statuses

            $("#open_status").on("click", function () {
                openSettings(window["statusList"], "status", "Status");
            });

            //Open modal window with all the categories

            $("#open_categories").on("click", function () {
                openSettings(categoryList, "category", "Categories");
            });

            //Open modal window with all the tags

            $("#open_tags").on("click", function () {
                openSettings(tagList, "tag", "Tags");
            });

        });

        /**
         * Applies filtering selected rules to table
         *
         * @returns {undefined}
         */
        function filter() {

            $("tbody > tr").not(".deleted").css("display", "table-row");
            if ($("#hide_completed").prop("checked")) {
                $("tbody > tr").filter(function () {
                    return $(this).find(".percentage").val() === '100'
                }).css("display", "none");
            }

            if ($("#filter_status").val() !== null) {

                $.each($("tbody > tr").not(".deleted").filter(function () {
                    return $(this).css('display') != 'none';
                }), function (key, value) {


                    var statusValue = ($(this).find(".combobox").val());
                    var boolean = false;
                    if ($.inArray(statusValue, $("#filter_status").val()) == -1) {
                        $(this).css("display", "none");
                    } else {
                        if (!$(this).hasClass("task")) {
                            filterDisplayRecursion($(this))
                        }
                    }
                });
            }

            if ($("#filter_categories").val() !== null) {
                $.each($("tbody > tr").not(".deleted").filter(function () {
                    return $(this).css('display') != 'none';
                }), function (key, value) {

                    var cat_split = ($(this).find(".categories").text()).split(",");
                    var boolean = false;
                    $.each($("#filter_categories").val(), function (key, value) {

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

            if ($("#filter_tags").val() !== null) {

                $.each($("tbody > tr").not(".deleted").filter(function () {
                    return $(this).css('display') != 'none';
                }), function (key, value) {

                    var tags_split = ($(this).find(".tags").text()).split(",");

                    var boolean = false;
                    $.each($("#filter_tags").val(), function (key, value) {

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

            updateShowingRows();

            $("#tasklist_table").stickyTableHeaders({
                fixedOffset: parseInt($(".fixed").css("height").replace("px", ""))
            });

            saveFilterDatabase();
        }

        /**
         * AJAX call to save filter settings into database for posterior use
         * 
         */
        function saveFilterDatabase() {
            $.ajax({
                method: "POST",
                url: location.href + "/" + "filter",
                data: {
                    'filter_status': $("#filter_status").val() !== null ? ($("#filter_status").val()).join() : "",
                    'filter_categories': $("#filter_categories").val() !== null ? ($("#filter_categories").val()).join() : "",
                    'filter_tags': $("#filter_tags").val() !== null ? ($("#filter_tags").val()).join() : "",
                    'filter_complete': $("#hide_completed").prop("checked") ? 1 : 0
                }
            })
        }

        function filterDisplayRecursion(element) {
            while (element.length) {
                classes = element.attr('class').split(/\s+/);
                element = $("#" + classes[1]);
                element.css("display", "table-row");
            }
        }

        function updateShowingRows() {
            $("#showing_rows").text($("#tasklist_table > tbody > tr:visible").length)
            $("#total_rows").text($("#tasklist_table > tbody > tr").length)
        }