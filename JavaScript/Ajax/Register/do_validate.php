<?php
    //lấy thông tin username và email
    $username = isset($_POST['username'])?$_POST['username']:false;
    $email = isset($_POST['email'])?$_POST['email']:false;
    $id = mt_rand();

    //nếu cả hai thông tin đều không có
    //thì dừng lại
    if(!$username && !$email){
        die('{error:"bad_request"}');
    }

    //kết nối database
    $conn_string = "host=127.0.0.1 port=5432 dbname=test user=postgres password=admin";
    $conn = pg_connect($conn_string) or die('Could not connect: '.pg_last_error());

    //khai báo biến lưu lỗi
    $error = array('error'=>'success','username'=>'','email'=>'','connect'=>'');

    // Check connection
    if (!$conn) {
        $error['connect'] = "Connection failed";
    }else{
        $error['connect'] = "Connected successfully";
    }

    if($username){
        $query = pg_query($conn,"select count(*) as count from member where username='$username'");
        if($query){
            $row = pg_fetch_array($query,NULL,PGSQL_ASSOC);
            if((int)$row['count'] > 0){
                $error['username'] = 'Tên đăng nhập đã tồn tại';
            }
        }else{
            $error['error'] = 'bad_request';
        }
    }

    if($email){
        $query = pg_query($conn,"select count(*) as count from member where email='$email'");
        if($query){
            $row = pg_fetch_array($query,NULL,PGSQL_ASSOC);
            if((int)$row['count'] > 0){
                $error['email'] = 'Email đã tồn tại';
            }
        }else{
            $error['error'] = 'bad_request';
        }
    }

    if(!$error['$username'] && !$error['email']){
        //tiến hành lưu csdl
        $query = pg_query($conn,"insert into member(id, username, email) values ('$id','$username','$email')");            
    }

    //trả kết quả về cho client
    die(json_encode($error))

?>
