        /*
         * ToDo List
         *
         * @author Marcos Lopez
         * 
         */

        $(document).ready(function () {


                $("table#tasklist_table").on("click", "button.fa-commenting", function () {

                        var comment = $(this).prev(".comment").attr("id", "editing-comment");
                        $("#task_comment").val(comment.text());
                        $("#comments_modal").modal("show");
                });
                
                $("#comments_modal").on("click", ".btn-primary", function () {

                        $("#editing-comment").parents("tr").addClass("changed");
                        $("#editing-comment").text($("#task_comment").val().trim());
                        var $btn = $("#editing-comment").next();
                        if ($("#task_comment").val().trim() !== "" && !$btn.hasClass("btn-warning")) {
                                $btn.removeClass("btn-default").addClass("btn-warning");
                        } else if ($("#task_comment").val().trim() === "" && !$btn.hasClass("btn-default")) {
                                $btn.removeClass("btn-warning").addClass("btn-default");
                        }


                        $("#editing-comment").removeAttr("id");
                        $("#comments_modal").modal("hide");
                });

                $('#comments_modal').on('hidden.bs.modal', function (e) {
                        $("#editing-comment").removeAttr("id");
                        $(this).modal("hide");
                });

                $("table#tasklist_table").on("click", "button.fa-link", function () {
                        var link = $(this).prev(".link").attr("id", "editing-link");
                        $("#task_link").val(link.text());
                        $("#links_modal").modal("show");
                });

                $("#links_modal").on("click", ".btn-primary", function () {

                        if ($("#task_link").val().trim().length === 0) {
                                return false;
                        }

                        window.open($("#task_link").val(), "_blank");
                        $("#links_modal").modal("hide");
                });

                $("#links_modal").on("click", ".btn-warning", function () {
                        $("#editing-link").parents("tr").addClass("changed");
                        $("#editing-link").text(($("#task_link").val().trim()));
                        var $btn = $("#editing-link").next();
                        if ($("#task_link").val().trim() !== "" && !$btn.hasClass("btn-warning")) {
                                $btn.removeClass("btn-default").addClass("btn-warning");
                        } else if ($("#task_link").val().trim() === "" && !$btn.hasClass("btn-default")) {
                                $btn.removeClass("btn-warning").addClass("btn-default");
                        }

                        $("#editing-link").removeAttr("id");
                        $("#links_modal").modal("hide");
                });

                $('#links_modal').on('hidden.bs.modal', function (e) {
                        $("#editing-link").removeAttr("id");
                        $(this).modal("hide");
                });
        });
        
        /**
         * Transform span.title into an input and give focus to it
         */
        var titleToInput = function () {

                if ($("#input_title").length > 0)
                        return false;
                
                var $input = $("<input>", {
                        type: "text",
                        class: "title",
                        id: "input_title",
                        placeholder: "New task name",
                        val: $(this).text()
                });
                
                $(this).children("span").replaceWith($input);
                var tmpStr = $input.val();
                $input.focus().val('').val(tmpStr);
                $input.on("blur", titleToSpan);
        }

        /**
         * Transform input["type=text"].title into a span.title tag
         */
        var titleToSpan = function () {

                if ($(this).val().length === 0)
                        return false;
                
                var color = $(this).parents('tr').find("select[name=colorpicker]").val();
                var $span = $("<span>", {
                        text: $(this).val(),
                        class: "title"
                });
                
                $(this).replaceWith($span);
                
                $span.css('color', color);

                $span.on("click", titleToInput);
        };
        
        /**
         * 
         * @returns {undefined}
         */
        var categoryToInput = function () {

                var $input = $("<input>", {
                        val: $(this).text(),
                        type: "text",
                        id: "autocomplete_cat",
                });
                $input.addClass("categories");
                $(this).children("span").replaceWith($input);
                $("#autocomplete_cat")
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
                $input.focus();
                $input.on("blur", categoryToSpan);
        };
		
		
        /**
         * 
         * 
         * */
        var categoryToSpan = function () {

                $(this).val($.trim($(this).val()).replace(/\s*[,]\s*/g,",").replace(/,+/g, ",").replace(/[,]$/g, ""));
				
                if ($(this).val().length > 0) {

                        $(this).parents("tr").addClass("changed");
                        // Check and update the categories array
                        var cats = $(this).val().split(",");
                        for (var i = 0, long = cats.length; i < long; i++) {
								
								var cat_value = cats[i].trim();
							
                                if ($.inArray(cat_value, categoryList) === -1) {
									
                                        categoryList.push(cat_value);
										
                                        $("#filter_categories").append("<option value=" + cat_value + ">" + cat_value + "</option>");
                                        $("#filter_categories").multiselect("rebuild");
										
                                        //ajax call
                                        $.ajax({
                                                method: "POST",
                                                url: (location.href).replace("#", "") + "/save/category",
                                                data: {
                                                        name: cat_value
                                                }
                                        }).
                                                done(function () {
                                                        console.log("Setting saved successfully.");
                                                })
                                                .fail(function () {
                                                        console.log("Error saving setting")
                                                })

                                }
                        }
                }



                var $span = $("<span>", {
                        text: $(this).val(),
                        class: "categories"
                });
                $(this).replaceWith($span);
                $span.on("click", categoryToInput);
        };
        var dateToInput = function () {
                var $input = $("<input>", {
                        val: $(this).text(),
                        type: "text",
                        id: "datepicker"
                });
                $input.addClass("date");
                $(this).children("span").replaceWith($input)
                $input.datepicker({
                        format: "dd/mm/yyyy",
                        todayHighlight: true,
                        weekStart: 1
                })
                        .on("hide", function () {
                                dateToSpan($(this))
                        });
                $input.focus();
        };
        function dateToSpan(element) {
                element.datepicker("destroy");
                var $span = $("<span>", {
                        text: element.val()
                });
                $span.addClass("date");
                element.replaceWith($span);
                $span.on("click", dateToInput);
        }

        var tagsToInput = function () {

                var $input = $("<input>", {
                        val: $(this).text(),
                        type: "text",
                        id: "autocomplete_tags"
                });
                $input.addClass("tags");
                $(this).children("span").replaceWith($input);
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
                $input.focus();
                $input.on("blur", tagsToSpan);
        };
        var tagsToSpan = function () {

                $(this).val($.trim($(this).val()).replace(/\s*[,]\s*/g,",").replace(/,+/g, ",").replace(/[,]$/g, ""));
				
                if ($(this).val().length > 0) {

                        $(this).parents("tr").addClass("changed");
                        var tags = $(this).val().split(",");
						
                        for (var i = 0, long = tags.length; i < long; i++) {
							
								var tag_value = tags[i].trim();
							
                                if ($.inArray(tag_value, tagList) === -1) {

                                        tagList.push(tag_value);
										
                                        $("#filter_tags").append("<option value=" + tag_value + ">" + tag_value + "</option>");
                                        $("#filter_tags").multiselect("rebuild");
										
                                        $.ajax({
                                                method: "POST",
                                                url: (location.href).replace("#", "") + "/save/tag",
                                                data: {
                                                        name: tag_value
                                                }
                                        }).
                                                done(function () {

                                                        console.log("Setting saved successfully.");
                                                })
                                                .fail(function () {
                                                        console.log("Error saving setting")
                                                })

                                }
                        }
                }


                var $span = $("<span>", {
                        text: $(this).val(),
                        class: "tags"
                });
                $(this).replaceWith($span);
                $span.on("click", tagsToInput);
        };
        function split(val) {
                return val.split(/,\s*/);
        }

        function extractLast(term) {
                return split(term).pop();
        }

        $.fn.changeVal = function (v, event) {
                return $(this).val(v).trigger("change");
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
                        var new_status = $(this).val();
                        if (!new_status.length) //don't add empty status
                                return false;
                        if ($.inArray(new_status, statusList) === -1) {

                                $.ajax({
                                        method: "POST",
                                        url: location.href + "/save/status",
                                        data: {
                                                name: new_status
                                        }
                                }).
                                        done(function () {
                                                statusList.push(new_status);
                                                $("#filter_status").append("<option value='" + new_status + "'>" + new_status + "</option>");
                                                $("#filter_status").multiselect('rebuild');
                                                $("ul.combobox-menu").append("<li><a href=#>" + new_status + "</a></li>");
                                                console.log("Setting saved successfully.");
                                        })
                                        .fail(function () {
                                                console.log("Error saving setting")
                                        })

                        }
                });
                
                $ul = $("<ul>", {
                        class: "dropdown-menu combobox-menu",
                });
                
                $.each(statusList, function (key, value) {

                        $li = $("<li>");
                        $a = $("<a>", {
                                href: "#",
                                text: value
                        }).on("click", function (e) {
                                e.preventDefault();
                                $(this).closest("tr").addClass("changed");
                                $(this).closest('div.dropdown').find('input.combobox')
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
         * Check task tree to block percentage inputs in parents tasks
         * 
         * @returns {undefined}
         */
        function blockPercentages() {

                $("tr").each(function () {
                        if ($("." + $(this).attr("id") + ":not(.deleted)").length > 0) {
                                $(this).find(".percentage").attr("disabled", true);
                        } else {
                                $(this).find(".percentage").removeAttr("disabled");
                        }
                })
        }