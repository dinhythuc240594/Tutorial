<?php

    //kết nối database
    $conn_string = "host=127.0.0.1 port=5432 dbname=test user=postgres password=admin";
    $conn = pg_connect($conn_string) or die('Could not connect: '.pg_last_error());

    //lấy danh sách thành viên
    $query = pg_query($conn,"select* from member");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    if(pg_num_rows($query) > 0){
        while($row=pg_fetch_array($query,NULL,PGSQL_ASSOC)){
            echo '<items>';
                echo '<username>'.$row['username'].'</username>';
                echo '<email>'.$row['email'].'</email>';
            echo '</items>';
        }
    }
    echo '</root>';
 ?>
