<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/showtime.js" type="text/javascript" async></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/functions.js"></script>


<script>
    // Abrir submenu
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
        });
    });

    // Fomartação de valores monetarios com ','
    // aplly money mask to input
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });

</script>
</body>

</html>