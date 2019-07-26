<?php
    /**
     *
     */
    class Pagination
    {

        protected $_config = array(
            'current_page' => 1, //trang hiện tại
            'total_record' => 1, //tổng số record
            'total_page' => 1, //tổng số trang
            'limit' => 10, //limtt
            'start' => 0, //start
            'link_full' => '', //link full có dạng như sau: domain/com/page/{page}
            'link_first' => '', //link trang đầu tiên
            'range' => 9, //số button trang bạn hiển thị
            'min' => 0, //tham số min
            'max' => 0 //tham số max, min là 2 tham số private
        );


        function get_config($key){
            return $this->_config[$key];
        }

        //hàm khởi tạo phân trang
        function init($config=array()){
            ///lặp qua từng phần tử config truyền vào và gán config của đối số
            //trước khi gán vào thì phải kiểm tra thông số config truyền vào có
            //trong hệ thống config không, nếu có thì mới gán

            foreach ($config as $key => $val) {
                if (isset($this->_config[$key])) {
                    $this->_config[$key] = $val;
                }
            }

            ///kiểm tra thông số limit truyền vào có nhỏ hơn 0 hay không?
            //nếu nhỏ thì gán cho limit = 0, vì trong csdl không cho dl nhỏ > 0

            if($this->_config['limit'] < 0){
                $this->_config['limit'] = 0;
            }

            //tính total page, công thức tính tổng số trang như sau
            //total_page = ciel(total_record/limit);
            $this->_config['total_page'] = ceil($this->_config['total_record']/$this->_config['limit']);

            //kiểm tra tổng số trang
            if(!$this->_config['total_page']){
                $this->_config['total_page'] = 1;
            }

            //kiểm tra các trường hợp
            //    + nếu truyền nhỏ hơn thì cho = 1
            //    + nếu truyền lớn hơn số trang hiện tại, thì cho = tổng số trang
            if($this->_config['current_page'] < 1){
                $this->_config['current_page'] = 1;
            }

            if($this->_config['current_page'] > $this->_config['total_page']){
                $this->_config['current_page'] = $this->_config['total_page'];
            }

            //tính start
            $this->_config['start'] = ($this->_config['current_page']-1)*$this->_config['limit'];

            //tính tổng số trang show ra
            $midlle = ceil($this->_config['range']/2);

            if($this->_config['total_page'] < $this->_config['range']){
                $this->_config['min'] = 1;
                $this->_config['max'] = $this->_config['total_page'];
            }else{
                $this->_config['min'] = $this->_config['current_page'] - $midlle + 1;
                $this->_config['max'] = $this->_config['current_page'] + $midlle - 1;

                //kiểm tra max và min
                //nếu min < 1 và max = range
                if($this->_config['min'] < 1){
                    $this->_config['min'] = 1;
                    $this->_config['max'] = $this->_config['range'];
                }else if($this->_config['max'] > $this->_config['total_page']){ //ngược lại nếu min lớn hơn total page
                    $this->_config['max'] = $this->_config['total_page'];
                    $this->_config['min'] = $this->_config['total_page'] - $this->_config['range'] + 1;
                }
            }
        }

        //hàm lấy link theo trang
        private function __link($page){
            //nếu trang < 1 thì ta sẽ link first
            if($page <= 1 && $this->_config['link_first']){
                return $this->_config['link_first'];
            }
            //ngược lại nếu link full
            return str_replace('{page}',$page,$this->_config['link_full']);
        }


        //hàm lấy mã html
        function html(){
            $p = '';
            if($this->_config['total_record'] > $this->_config['limit']){
                $p = '<ul>';

                //nút prev and first
                if($this->_config['current_page'] > 1){
                    $p .= '<li><a href="'.$this->__link('1').'">First</a></li>';
                    $p .= '<li><a href="'.$this->__link($this->_config['current_page']-1).'">Prev</a></li>';
                }

                //lặp trong khoảng cách min và max để hiển thị nut
                for($i = $this->_config['min']; $i <= $this->_config['max']; $i++){
                    //trang hiện tại
                    if($this->_config['current_page'] == $i){
                        $p .='<li><span>'.$i.'</span></li>';
                    }else {
                        $p .='<li><a href="'.$this->__link($i).'">'.$i.'</a></li>';
                    }
                }

                //nút last và next
                if($this->_config['current_page'] < $this->_config['total_page']){
                    $p .='<li><a href="'.$this->__link($this->_config['current_page']+1).'">Next</a></li>';
                    $p .='<li><a href="'.$this->__link($this->_config['total_page']).'">Last</a></li>';
                }
                $p .= '</ul>';
            }

            return $p;

        }

    }

 ?>
