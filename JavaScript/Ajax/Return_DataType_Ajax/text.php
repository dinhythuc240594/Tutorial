<?php

    // Thiết lặp font chữ UTF8 để khỏi bị lỗi font
    header('Content-Type:text/html; charset=utf-8');

    //kết nối database
    $conn_string = "host=127.0.0.1 port=5432 dbname=test user=postgres password=admin";
    $conn = pg_connect($conn_string) or die('Could not connect: '.pg_last_error());


    //lấy danh sách thành viên
    $query = pg_query($conn,"select* from member");

    //kiểm tra có dữ liệu hay không
    if(pg_num_rows($query) > 0){
        echo '<p>Get List Text</p>';
        echo '<table border="1" cellspacing="0" cellpacing="10">';
        echo '<tr>';
            echo '<td>';
                echo 'Username';
            echo '</td>';
            echo '<td>';
                echo 'Email';
            echo '</td>';
        echo '</tr>';

        //lập danh sách hiển thị dang table
        while($row=pg_fetch_array($query,NULL,PGSQL_ASSOC)){
            echo '<tr>';
                echo '<td>';
                    echo $row['username'];
                echo '</td>';
                echo '<td>';
                    echo $row['email'];
                echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
 ?>
