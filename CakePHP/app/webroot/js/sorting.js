        /**
         * ToDo List ToDo List
         * 
         * @author Marcos Lopez
         */

        $(document).ready(function () {

                function sorting(field, sortFunction) {
                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {
                                return sortFunction();
                        });

                        if ($(this).attr("id") === "sort-" + field + "-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-" + field + "-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-desc sort-icon sort-' + field + '" aria-hidden="true" id="sort-' + field + '-desc"></i>');
                        } else if ($(this).attr("id") === "sort-" + field + "-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-asc sort-icon sort-' + field + '" aria-hidden="true" id="sort-' + field + '-asc"></i>');

                        }
                }

                $("#tasklist_table").on("click", ".sort-title", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {
                                var a = $(a).find(".title").text(),
                                        b = $(b).find(".title").text();

                                if (a.length === 0 && b.length > 0) {
                                        return 1;
                                } else if (b.length === 0 && a.length > 0) {
                                        return -1;
                                }
                                return alphanum(a, b);
                        });

                        if ($(this).attr("id") === "sort-title-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-title-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-desc sort-icon sort-title" aria-hidden="true" id="sort-title-desc"></i>');
                        } else if ($(this).attr("id") === "sort-title-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-asc sort-icon sort-title" aria-hidden="true" id="sort-title-asc"></i>');

                        }
                });


                $("#tasklist_table").on("click", ".sort-priority", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {

                                return $(a).find(".priority").val() - $(b).find(".priority").val();

                        });

                        if ($(this).attr("id") === "sort-priority-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-priority-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-desc sort-icon sort-priority" aria-hidden="true" id="sort-priority-desc"></i>');
                        } else if ($(this).attr("id") === "sort-priority-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-asc sort-icon sort-priority" aria-hidden="true" id="sort-priority-asc"></i>');

                        }
                });

                $("#tasklist_table").on("click", ".sort-percentage", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {

                                return $(a).find(".percentage").val() - $(b).find(".percentage").val();

                        });

                        if ($(this).attr("id") === "sort-percentage-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-percentage-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-desc sort-icon sort-percentage" aria-hidden="true" id="sort-percentage-desc"></i>');
                        } else if ($(this).attr("id") === "sort-percentage-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-asc sort-icon sort-percentage" aria-hidden="true" id="sort-percentage-asc"></i>');

                        }
                });

                $("#tasklist_table").on("click", ".sort-date", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {
                                var a = $(a).find(".date").text().split("/"),
                                        b = $(b).find(".date").text().split("/");

                                if (a.length === 1 && b.length === 3) {
                                        return 1;
                                }
                                if (a.length === 3 && b.length === 1) {
                                        return -1;
                                }
                                if (a.length === 1 && b.length === 1) {
                                        return 0;
                                }

                                return new Date(a[1] + "/" + a[0] + "/" + a[2]) - new Date(b[1] + "/" + b[0] + "/" + b[2]);

                        });

                        if ($(this).attr("id") === "sort-date-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-date-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-desc sort-icon sort-date" aria-hidden="true" id="sort-date-desc"></i>');
                        } else if ($(this).attr("id") === "sort-date-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-amount-asc sort-icon sort-date" aria-hidden="true" id="sort-date-asc"></i>');

                        }
                });

                $("#tasklist_table").on("click", ".sort-status", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {
                                
                                var a = $(a).find(".combobox").val(),
                                        b = $(b).find(".combobox").val();

                                if (a.length === 0 && b.length > 0) {
                                        return 1;
                                } else if (b.length === 0 && a.length > 0) {
                                        return -1;
                                }
                                return alphanum(a, b);
                        });

                        if ($(this).attr("id") === "sort-status-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-status-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-desc sort-icon sort-status" aria-hidden="true" id="sort-status-desc"></i>');
                        } else if ($(this).attr("id") === "sort-status-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-asc sort-icon sort-status" aria-hidden="true" id="sort-status-asc"></i>');

                        }
                });

                $("#tasklist_table").on("click", ".sort-category", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {

                                var a = $(a).find(".categories").text(),
                                        b = $(b).find(".categories").text();

                                if (a.length === 0 && b.length > 0) {
                                        return 1;
                                } else if (b.length === 0 && a.length > 0) {
                                        return -1;
                                }
                                return alphanum(a, b);
                        });

                        if ($(this).attr("id") === "sort-category-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-category-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-desc sort-icon sort-category" aria-hidden="true" id="sort-category-desc"></i>');
                        } else if ($(this).attr("id") === "sort-category-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-asc sort-icon sort-category" aria-hidden="true" id="sort-category-asc"></i>');

                        }
                });

                $("#tasklist_table").on("click", ".sort-tag", function () {

                        var subtasks = $("table#tasklist_table > tbody > tr:not(.task)");

                        var ar = $("table#tasklist_table > tbody > tr.task").get().sort(function (a, b) {
                                var a = $(a).find(".tags").text(),
                                        b = $(b).find(".tags").text();

                                if (a.length === 0 && b.length > 0) {
                                        return 1;
                                } else if (b.length === 0 && a.length > 0) {
                                        return -1;
                                }
                                return alphanum(a, b);
                        });

                        if ($(this).attr("id") === "sort-tag-desc") {
                                ar.reverse();
                        }

                        $("table#tasklist_table > tbody").empty();

                        $.each(ar, function () {
                                $(this).appendTo($("table#tasklist_table > tbody"));
                        });

                        $.each(subtasks, function () {
                                var classes = ($(this).attr("class")).split(" ");
                                $(this).insertAfter($("#" + classes[1]));
                        });

                        if ($(this).attr("id") === "sort-tag-asc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-desc sort-icon sort-tag" aria-hidden="true" id="sort-tag-desc"></i>');
                        } else if ($(this).attr("id") === "sort-tag-desc") {
                                $(this).replaceWith('<i class="fa fa-sort-alpha-asc sort-icon sort-tag" aria-hidden="true" id="sort-tag-asc"></i>');

                        }
                });
        });

        function alphanum(a, b) {

                function chunkify(t) {
                        var tz = [],
                                x = 0,
                                y = -1,
                                n = 0,
                                i, j;
                        while (i = (j = t.charAt(x++)).charCodeAt(0)) {
                                var m = (i == 46 || (i >= 48 && i <= 57));
                                if (m !== n) {
                                        tz[++y] = "";
                                        n = m;
                                }
                                tz[y] += j;
                        }
                        return tz;
                }

                var aa = chunkify(a);
                var bb = chunkify(b);
                for (x = 0; aa[x] && bb[x]; x++) {
                        if (aa[x] !== bb[x]) {
                                var c = Number(aa[x]),
                                        d = Number(bb[x]);
                                if (c == aa[x] && d == bb[x]) {
                                        return c - d;
                                } else
                                        return (aa[x] > bb[x]) ? 1 : -1;
                        }
                }
                return aa.length - bb.length;
        }