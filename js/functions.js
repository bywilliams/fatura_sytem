

// Função que abre o tooltip de observação dos relatórios
function openTooltip(i) {
    // Mostrar tooltip
    $("#grupos" + i).click(function() {
        $("#tooltip_" + i).show();
    });

    // fechar tooltip
    $("#close" + i).click(function() {
        $("#tooltip_" + i).hide();
    });

    $(document).mouseup(function(e) {
        if ($(e.target).closest(".tooltip_" + i).length === 0) {
            $("#tooltip_" + i).hide();
        }
    });
}


 // the selector will match all input controls of type :checkbox
    // and attach a click event handler 
    $("input:checkbox").on('click', function() {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });