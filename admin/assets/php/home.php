<?php
    include("dbconnection.php");

    //Informacion para grafico y demas
    //General
      $query = "SELECT * FROM ticket";
    
      $ti_total = mysqli_query($con, $query);
      $general = mysqli_num_rows($ti_total);
    
    //Tickets Abiertos (estado id = 11)
      $query = "SELECT *
                FROM ticket 
                WHERE status = 11 ";
      $ti_total = mysqli_query($con, $query);
      $abi = mysqli_num_rows($ti_total);
    
    //Tickets En revisión (estado id = 10)
      $query = "SELECT *
      FROM ticket 
      WHERE status = 10 ";
    
      $ti_total = mysqli_query($con, $query);
      $revi = mysqli_num_rows($ti_total);
    
    //Tickets Cerrados (estado id = 12)
      $query = "SELECT *
      FROM ticket 
      WHERE status = 12 ";
    
      $ti_total = mysqli_query($con, $query);
      $cerr = mysqli_num_rows($ti_total); 
      
      //Notificaciones 
      $noti_query = "SELECT * 
                      FROM notificaciones
                      WHERE admin = 1
                      ORDER BY creada_en DESC";
      $noti = mysqli_query($con, $noti_query);

?>