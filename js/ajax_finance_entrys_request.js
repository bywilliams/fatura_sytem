$('.money').mask('000.000.000.000.000,00', {
    reverse: true
});

// Ajax para a página de relatórios de entrada 
$(document).ready(function() {

    $("input").keyup(function() {        
       
        var name_search_entry = $("#name_search_entry").val();
        var values_entry = $("#values_entry").val();
        var from_date_entry = $("#from_date_entry").val();
        var to_date_entry = $("#to_date_entry").val();
        var category_entry = $("#category_entry").val();

        $.post("financial_entry_ajax.php", {
            name_search_entry: name_search_entry,
            values_entry: values_entry,
            from_date_entry: from_date_entry,
            to_date_entry: to_date_entry,
            category_entry: category_entry
        }, function(data, status) {
            if (data) {
                $("#search_entry").html(data);
            }else {
                $("#table_report_entry").show();
                
            }
        });
    });

    $("#values_entry").on("click", function() {
        
        var values_entry = $("#values_entry").val();
        var name_search_entry = $("#name_search_entry").val();
        var from_date_entry = $("#from_date_entry").val();
        var to_date_entry = $("#to_date_entry").val();
        var category_entry = $("#category_entry").val();

        var table_origin = document.getElementById("table_report_entry");
        table_origin.style.display = "none";

        $.post("financial_entry_ajax.php", {
            values_entry: values_entry,
            name_search_entry: name_search_entry,
            from_date_entry: from_date_entry,
            to_date_entry: to_date_entry,
            category_entry: category_entry
        }, function(data, status) {
            if (data) {
                $("#search_entry").html(data);
            }else {
                table_origin.show();
                
            }
        });
    });

    $("#category_entry").on("click", function() {

        var values_entry = $("#values_entry").val();
        var name_search_entry = $("#name_search_entry").val();
        var from_date_entry = $("#from_date_entry").val();
        var to_date_entry = $("#to_date_entry").val();
        var category_entry = $("#category_entry").val();

        var table_origin = document.getElementById("table_report_entry");
        table_origin.style.display = "none";

        $.post("financial_entry_ajax.php", {
            values_entry: values_entry,
            name_search_entry: name_search_entry,
            from_date_entry: from_date_entry,
            to_date_entry: to_date_entry,
            category_entry: category_entry
        }, function(data, status) {
            if (data) {
                $("#search_entry").html(data);
            }else {
                $("#table_report_entry").show();
                
            }
        });
    });
});
// Fim Ajax para a página de relatórios de entrada 