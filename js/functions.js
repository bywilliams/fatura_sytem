

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