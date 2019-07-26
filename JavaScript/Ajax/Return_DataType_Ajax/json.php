<?php

    //kết nối database
    $conn_string = "host=127.0.0.1 port=5432 dbname=test user=postgres password=admin";
    $conn = pg_connect($conn_string) or die('Could not connect: '.pg_last_error());

    //lấy danh sách thành viên
    $query = pg_query($conn,"select* from member");

    //biến result
    $result = array();

    if(pg_num_rows($query) > 0){
        while($row=pg_fetch_array($query,NULL,PGSQL_ASSOC)){
            $result[]=array(
                'username'=>$row['username'],
                'email'=>$row['email']
            );
        }
    }
    die(json_encode($result));
 ?>
