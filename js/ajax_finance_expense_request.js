$('.money').mask('000.000.000.000.000,00', {
    reverse: true
});

// Ajax para a página de relatórios de saída 
$(document).ready(function() {

    $("input").keyup(function() {

        var name_search_exit = $("#name_search_exit").val();
        var values_exit = $("#values_exit").val();
        var from_date_exit = $("#from_date_exit").val();
        var to_date_exit = $("#to_date_exit").val();
        var category_exit = $("#category_exit").val();
        var expense_exit = $("#expense_type").val();

        $.post("financial_exit_ajax.php", {
            name_search_exit: name_search_exit,
            values_exit: values_exit,
            from_date_exit: from_date_exit,
            to_date_exit: to_date_exit,
            category_exit: category_exit,
            expense_exit: expense_exit
        }, function(data, status) {
            if (data) {
                $("#search_exit").html(data);
            }else {
                $("#table_report_exit").show();
                
            }
        });
    });

    $("#values_exit").on("click", function() {

        var values_exit = $("#values_exit").val();
        var name_search_exit = $("#name_search_exit").val();
        var from_date_exit = $("#from_date_exit").val();
        var to_date_exit = $("#to_date_exit").val();
        var category_exit = $("#category_exit").val();
        var expense_exit = $("#expense_type").val();

        var table_origin = document.getElementById("table_report_exit");
        table_origin.style.display = "none";

        $.post("financial_exit_ajax.php", {
            values_exit: values_exit,
            name_search_exit: name_search_exit,
            from_date_exit: from_date_exit,
            to_date_exit: to_date_exit,
            category_exit: category_exit,
            expense_exit: expense_exit
        }, function(data, status) {
            if (data) {
                $("#search_exit").html(data);
            }else {
                $("#table_report_exit").show();
                
            }
        });
    });

    $("#category_exit").on("click", function() {
        var values_exit = $("#values_exit").val();
        var name_search_exit = $("#name_search_exit").val();
        var from_date_exit = $("#from_date_exit").val();
        var to_date_exit = $("#to_date_exit").val();
        var category_exit = $("#category_exit").val();
        var expense_exit = $("#expense_type").val();
        var table_origin = document.getElementById("table_report_exit");
        table_origin.style.display = "none";

        $.post("financial_exit_ajax.php", {
            values_exit: values_exit,
            name_search_exit: name_search_exit,
            from_date_exit: from_date_exit,
            to_date_exit: to_date_exit,
            category_exit: category_exit,
            expense_exit: expense_exit,
        }, function(data, status) {
             if (data) {
                $("#search_exit").html(data);
            }else {
                $("#table_report_exit").show();
                
            }
        });
    });

    $("#expense_type").on("click", function() {
        var values_exit = $("#values_exit").val();
        var name_search_exit = $("#name_search_exit").val();
        var from_date_exit = $("#from_date_exit").val();
        var to_date_exit = $("#to_date_exit").val();
        var category_exit = $("#category_exit").val();
        var expense_exit = $("#expense_type").val();

        var table_origin = document.getElementById("table_report_exit");
        table_origin.style.display = "none";

        $.post("financial_exit_ajax.php", {
            values_exit: values_exit,
            name_search_exit: name_search_exit,
            from_date_exit: from_date_exit,
            to_date_exit: to_date_exit,
            category_exit: category_exit,
            expense_exit: expense_exit,
        }, function(data, status) {
            if (data) {
                $("#search_exit").html(data);
            }else {
                $("#table_report_exit").show();
                
            }
        });
    });
});
// Fim Ajax para a página de relatórios de saída 