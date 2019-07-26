<?php
    //khai báo biến toàn cục kết nối
    global $conn;

    //hàm kết nối database
    function connect(){
        global $conn;
        $conn_string = "host=127.0.0.1 port=5432 dbname=test user=postgres password=admin";
        $conn = pg_connect($conn_string) or die('Could not connect: '.pg_last_error());
    }

    //hàm đóng kết nối
    function disconnect(){
        global $conn;
        if($conn){
            pg_close($conn);
        }
    }

    //hàm đếm tổng số lượng
    function count_all_member(){
        global $conn;
        $query = pg_query($conn,'select count(*) as total from member');
        if($query){
            $row = pg_fetch_array($query,NULL,PGSQL_ASSOC);
            return $row['total'];
        }
        return 0;
    }

    //lấy danh sách member
    function get_all_member($limit,$start){
        global $conn;
        $sql = 'select * from member limit '.(int)$limit.' offset '.((int)$start);
        $query = pg_query($conn,$sql);

        $result = array();
        if($query){
            while($row=pg_fetch_array($query,NULL,PGSQL_ASSOC)){
                $result[] = $row;
            }
        }
        return $result;
    }
 ?>
