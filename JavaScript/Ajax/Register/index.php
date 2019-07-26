<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Form Register</title>
        <script type="text/javascript"
        src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
    </head>
    <body>
        <form class="" action="" method="post">
            <table border="0" cellpadding="10" cellspacing="0">
                <tr>
                    <td>Username</td>
                    <td><input type="text" id="username" name="username" value=""/></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="text" id="email" name="email" value=""/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="submit" value="Register">
                        <input type="reset" name="submit" value="Clear">
                    </td>
                </tr>
            </table>
        </form>
        <div id="showerror"></div>
    </body>
    <script type="text/javascript">
        $('form').submit(function(){
            //xóa trắng thẻ div show lỗi
            $('#showerror').html('');

            var username = $('#username').val();
            var email = $('#email').val();

            //kiểm tra dữ liệu có null hay không
            if($.trim(username)==''){
                alert('Bạn chưa nhập tên đăng nhập');
                return false;
            }

            if($.trim(email)==''){
                alert('Bạn chưa nhập email');
                return false;
            }

            $.ajax({
                url:'do_validate.php',
                type:'post',
                dataType:'json',
                data:{
                    username:username,
                    email:email
                },
                success:function(result){
                    //kiểm tra thông tin gửi lên có bị lỗi hay không
                    //đây lêt kết quả trả về từ do_validate.php
                    if(!result.hasOwnProperty('error')||result['error']!='success'){
                        alert('Có vẻ bạn đang truy cập trái phép!');
                        return false;
                    }

                    //log data
                    console.log(result);

                    var html = '';

                    //lấy thông tin lỗi username
                    if($.trim(result.username)!=''){
                        html += result.username +'<br>';
                    }

                    //lấy thông tin lỗi username
                    if($.trim(result.email)!=''){
                       html += result.email +'<br>';
                    }

                    //cuối cùng kiểm tra xem có lỗi
                    // không, nếu có thì xuất hiện
                    if(html!=''){
                        $('#showerror').append(html);
                    }else{
                        $('#showerror').append('Thêm thành công');
                    }
                }
            });

            return false;
        });
    </script>
</html>
