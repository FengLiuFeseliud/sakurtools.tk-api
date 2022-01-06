<?php
    # error_reporting(0);

    class Link{

        private $headers = [
            "cookie" => [],
            "user-agent" => " Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",

        ];

        public function __construct($cookie=[]){
            global $headers;

            $fmt_cookie = "";
            foreach($cookie as $key => $val){
                $fmt_cookie = $fmt_cookie.$key."=".$val."; ";
            }
            $this -> headers["cookie"] = $fmt_cookie;
        }
        
        # 发送请求
        private function __link($api, $mode, $data){
            $headers = $this -> headers;

            $link = curl_init($api);
            curl_setopt($link, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($link, CURLOPT_COOKIE, $headers["cookie"]);
            curl_setopt($link, CURLOPT_HTTPHEADER, $headers);
            
            if($mode == "post"){
                curl_setopt($link, CURLOPT_POST, 1);
                curl_setopt($link, CURLOPT_POSTFIELDS, $data);
            }elseif($mode == "get" and $data != []){
                $get_data = "?";
                foreach($data as $key => $val){
                    $get_data = $get_data.$key."=".$val."&";
                }
                curl_setopt($link, CURLOPT_URL, $api.$get_data);
            }
            $data = curl_exec($link);
            $data = json_decode($data, true);
            return $data;
        }

        # get
        public function get($api, $data=[]){
            $data = $this -> __link($api, "get", $data);
            return $data;
        }

        # post
        public function post($api, $data=[]){
            $data = $this -> __link($api, "post", $data);
            return $data;
        }

        public function set_headers($headers){
            $this -> $headers = $headers;
        }

        # 检查接口数据输入
        public function censor($key, $code=4000, $fun=null){
            if(empty($_REQUEST[$key])){
                $this -> json([], $code, $key." 不能为空");
                if($fun != null){
                    return $fun();
                }
                exit();
            }
            return $_REQUEST[$key];
        }

        # 接口输出json
        public function json($data=[], $code=200, $msg="ok"){
            $return_data = [
                "code" => $code,
                "msg" => $msg,
                "data" => $data,
            ];

            if($code == 404){
                $return_data["msg"] = "not api 没有这个接口";
                echo json_encode($return_data, JSON_UNESCAPED_UNICODE);
                return;
            };
            
            echo json_encode($return_data, JSON_UNESCAPED_UNICODE);
            return;
        }

    };