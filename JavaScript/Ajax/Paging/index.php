<?php
    //import library data
    require_once 'database.php';

    //load library paging
    include_once 'pagination.php';

    //connect db
    connect();

    //phân trang
    $config = array(
        'current_page' => isset($_GET['page'])?$_GET['page']:1,
        'total_record' => count_all_member(), // tổng số member
        'limit' => 3,
        'link_full' => 'index.php?page={page}',
        'link_first' => 'index.php',
        'range' => 9
    );

    $paging = new Pagination();
    $paging->init($config);

    //lấy limit, start
    $limit = $paging->get_config('limit');
    $start = $paging->get_config('start');

    //lấy danh sách thành viên
    $member = get_all_member($limit,$start);

    //kiểm tra nếu là ajax thì request trả kết quả
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
        die(json_encode(array(
            'member' => $member,
            'paging' => $paging->html()
        )));
    }

    //disconnect
    disconnect();
 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
     <head>
         <meta charset="utf-8">
         <title></title>
         <script type="text/javascript" src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
         <style>
             li{float:left;margin:3px;border:solid 1px gray;list-style:none}
             a{padding:5px;}
             span{display:inline-block;padding:0px 3px;background:blue;color:white;}
         </style>
     </head>
     <body>
        <div id="content">
            <div id="list">
                <table border="1" cellpadding="5" cellspacing="0">
                    <?php foreach ($member as $item) { ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['username']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div id="paging">
                <?php echo $paging->html(); ?>
            </div>
        </div>
     </body>
     <script type="text/javascript">
        $('#content').on('click','#paging a',function(e){
            var url =$(this).attr('href');
            e.preventDefault();
            $.ajax({
                url:url,
                type:'get',
                dataType:'json',
                success:function(result){
                    //kiểm tra kết đúng định dạng
                    if(result.hasOwnProperty('member') && result.hasOwnProperty('paging')){
                        var html = '<table border="1" cellpadding="5" cellspacing="0">';
                        //lặp qua danh sách thành viên tạo html
                        $.each(result['member'],function(key,item) {
                            console.log(result['member']);
                            console.log(result['paging']);
                            html +='<tr>';
                            html +='<td>'+item['id']+'</td>';
                            html +='<td>'+item['username']+'</td>';
                            html +='<td>'+item['email']+'</td>';
                            html +='</tr>';
                        });
                        html +='</table>';

                        //thay đổi nội dung thành viên
                        $('#list').html(html);

                        //thay đổi nội dung phân trang
                        $('#paging').html(result['paging']);

                        //thay đổi url trên website
                        window.history.pushState({path:url},'',url);
                    }
                }
            });
            return false;
        });
     </script>
 </html>
