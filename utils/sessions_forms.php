<?php 

isset($_SESSION['razao']) ? $_SESSION['razao'] : "";
isset($_SESSION['cnpj']) ? $_SESSION['cnpj'] : "";
isset($_SESSION['ag']) ? $_SESSION['ag'] : "";
isset($_SESSION['cc']) ? $_SESSION['cc'] : "";
isset($_SESSION['pix']) ? $_SESSION['pix'] : "";
isset($_SESSION['invoice_one']) ? $_SESSION['invoice_one'] : $_SESSION['invoice_one'] = null;
isset($_SESSION['date_emission']) ? $_SESSION['date_emission'] : $_SESSION['date_emission'] = null; 
isset($_SESSION['value']) ? $_SESSION['value'] : $_SESSION['value'] = null;  
isset($_SESSION['notation']) ? $_SESSION['notation'] : $_SESSION['notation'] = null; 
isset($_SESSION['type_paid']) ? $_SESSION['type_paid'] : "";
isset($_SESSION['invoice_two']) ? $_SESSION['invoice_two'] : $_SESSION['invoice_two'] = null; 
isset($_SESSION['dt_expired']) ? $_SESSION['dt_expired'] : $_SESSION['dt_expired'] = null; 
isset($_SESSION['reference']) ? $_SESSION['reference'] : $_SESSION['reference'] = null; 
isset($_SESSION['account']) ? $_SESSION['account'] : ""; 

?>