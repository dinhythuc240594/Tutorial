<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta  http-equiv="Content-Type" content="text/html;charset=utf-8">
        <title>Ajax - Return data type Text</title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-2.0.0.min.js"></script>

    </head>
    <body>
        <div id="result1">Text</div><br>
        <div id="result2">JSon</div><br>
        <div id="result3">XML</div><br>
        <input type="button" name="clickme" value="Get List Text" id="text-click">
        <input type="button" name="clickme" value="Get List JSon" id="json-click">
        <input type="button" name="clickme" value="Get List XML" id="xml-click">
    </body>
    <script type="text/javascript">
        $('#text-click').click(function(){
            $.ajax({
                url:'text.php',
                type:'get',
                dataType:'text',
                success:function(result){
                    $('#result1').html(result);
                }
            });
        });


        $('#xml-click').click(function(){
            $.ajax({
                url:'xml.php',
                type:'get',
                dataType:'xml',
                success:function(result){
                    var html = '';
                    html += '<p>Get List XML</p>';
                    html += '<table border="1" cellspacing="0" cellpacing="10">';
                    html += '<tr>';
                        html += '<td>';
                            html += 'Username';
                        html += '</td>';
                        html += '<td>';
                            html += 'Email';
                        html += '</td>';
                    html += '</tr>';

                    // lặp để lấy data
                    $(result).find('items').each(function(key,val){
                        html += '<tr>';
                            html += '<td>';
                                html += $(val).find('username').text();
                            html += '</td>';
                            html += '<td>';
                                html += $(val).find('email').text();
                            html += '</td>';
                        html += '</tr>';
                    });
                    html += '</table>';

                    $('#result3').html(html);
                }
            });
        });


        $('#json-click').click(function(){
            $.ajax({
                url:'json.php',
                type:'get',
                dataType:'json',
                success:function(result){
                    var html = '';
                    html += '<p>Get List JSon</p>';
                    html += '<table border="1" cellspacing="0" cellpacing="10">';
                    html += '<tr>';
                        html += '<td>';
                            html += 'Username';
                        html += '</td>';
                        html += '<td>';
                            html += 'Email';
                        html += '</td>';
                    html += '</tr>';

                    // kết quả là 1 object JSon
                    //nên cần loop kết quả
                    $.each(result, function(key,item){
                        html += '<tr>';
                            html += '<td>';
                                html += item['username'];
                            html += '</td>';
                            html += '<td>';
                                html += item['email'];
                            html += '</td>';
                        html += '</tr>';
                    });
                    html += '</table>';

                    $('#result2').html(html);
                }
            });
        });
    </script>
</html>
