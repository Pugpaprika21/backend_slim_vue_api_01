<?php

use Service\Http\Init\Http;

if (!function_exists('url_where')) {

    /**
     * @param string $path_url
     * @param array $query_str
     * @param bool $encode_query_str
     * @return string|void
     */
    function url_where($path_url, $query_str = [], $encode_query_str = false)
    {
        $params = "";

        if (count($query_str) > 0) $params = "?";

        if (file_exists($path_url)) {

            $query_to_str = http_build_query($query_str, '', '&');

            if ($encode_query_str) {
                $query_to_str = base64_encode($query_to_str);
            }

            return $path_url . $params . $query_to_str;
        }

        http_response_code(Http::NOT_FOUND);
        exit(1);
    }
}

if (!function_exists('redirect_to')) {

    /**
     * @param string $path 
     * @param array $queryParams
     * @return void
     */
    function redirect_to($path, $queryParams = [])
    {
        $queryString = http_build_query($queryParams);

        if (file_exists($path)) {

            if (count($queryParams) > 0) {
                header("location: {$path}?{$queryString}");
                exit(1);
            } else {
                header("location: {$path}");
                exit(1);
            }
        } else {
            http_response_code(404);
            exit(1);
        }
    }
}

if (!function_exists('echo_r')) {
    /**
     * @param null $param
     * @param boolean $exist
     * @return void
     */
    function echo_r($param = null, $exist = true)
    {
        $data = print_r($param, true);
        echo sprintf("<pre><div id='debug-data' style='padding: 10px; color: #FFFFFF; background-color: #000000;'>%s</div></pre>", $data);
        if ($exist) exit;
    }
}

if (!function_exists('token_generator')) {

    /**
     * #token_generator();
     *
     * @param string $salt
     * @return void
     */
    function token_generator($salt = "example_salt")
    {
        $token = uniqid();
        $token = hash("sha256", $token . $salt . time());
        return $token;
    }
}

if (!function_exists('now')) {

    /**
     * @param string $time_zone -> D = DATE, T = TIME, F = DATE AND TIME
     * @return string
     */
    function now($format_date_as_time = 'F')
    {
        $formatted_now = '';
        switch (strtoupper($format_date_as_time)) {
            case 'D':
                $formatted_now = date('Y-m-d');
                break;
            case 'T':
                $formatted_now = date('H:i:s', time());
                break;
            default:
                $formatted_now = date('Y-m-d') . ' ' . date('H:i:s', time());
                break;
        }
        return $formatted_now;
    }
}

if (!function_exists('ip')) {

    /**
     * @return string
     */
    function ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if (!function_exists('write_log')) {

    /**
     * #write_log("oooo", "../logs/process/insert_user.txt");
     * 
     * @param string $message
     * @param string $file
     * @param bool $write_file
     * @return int|false
     */
    function write_log($message, $file = 'log.txt', $write_file = WRITE_LOG)
    {
        if ($write_file == false) return;

        $log = function () use ($message, $file) {
            $time = now();
            $log = "[$time] $message\n";
            return file_put_contents($file, $log, FILE_APPEND | LOCK_EX);
        };

        if (file_exists($file)) return $log();
        return $log();
    }
}

if (!function_exists('write_json')) {

    /**
     * #write_json("oooo", "../logs/json/data.json");
     * 
     * @param string|array $message
     * @param string $file
     * @param boolean $write_file
     * @return int|false
     */
    function write_json($message, $file = 'log.json', $write_file = WRITE_LOG)
    {
        if ($write_file == false) return false;

        $message = is_array($message) ? json_encode($message, JSON_PRETTY_PRINT) : null;

        $log = function () use ($message, $file) {
            $log = "$message";
            return file_put_contents($file, $log);
        };

        if (file_exists($file)) return $log();
        return $log();
    }
}

if (!function_exists('download_file')) {

    /**
     * #download_file('../../public/img/logo.PNG');
     *
     * @param string $file_path
     * @return void
     */
    function download_file($file_path)
    {
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            return;
        }
        throw new Exception("File not found : {$file_path}");
    }
}

if (!function_exists('line_notify')) {

    /**
     * @param string $token
     * @param string $message
     * @param string|null $image_url
     * @return array
     */
    function line_notify($token, $message, $image_url = null)
    {
        $line_api = 'https://notify-api.line.me/api/notify';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $line_api,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer {$token}"
            ),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'message' => $message,
                'imageThumbnail' => $image_url,
                'imageFullsize' => $image_url,
            )),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}

// if (!function_exists('view')) {

//     /**
//      * @param string $view_file 
//      * @param array $view_vars 
//      * @return string|void 
//      */
//     function view($view_file, $view_vars = [])
//     {
//         $view_path = __DIR__  .  $view_file;
        
//         if (!file_exists($view_path)) {
//             http_response_code(404);
//             throw new Exception("view path not found. [ {$view_file} ]");
//         }

//         extract($view_vars);
//         ob_start();
//         require_once $view_path;
//         return ob_get_clean();
//     }
// }

if (!function_exists('rend_string')) {

    /**
     * @param string $input_str
     * @param integer $length
     * @return string
     */
    function rend_string($input_str = '', $length = 10)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ{$input_str}";
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('file_upload')) {

    /**
     * file_upload('../../upload/image/', [
     *      'name' => $file['user_profile']['name'],
     *      'tmp_name' => $file['user_profile']['tmp_name']
     * ]);
     * 
     * return file name
     *
     * @param string $path_upload
     * @param array $file
     * @return string|void 
     */
    function file_upload($path_upload, $file)
    {
        $path = __DIR__ . $path_upload;
        if (!file_exists($path)) {
            http_response_code(404);
            echo_r(['path_upload' => $path_upload, 'file' => $file]);
        }

        $imageFileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        $typeFile = array_merge(['jpg', 'jpeg', 'png'], ['JPG', 'JPEG', 'PNG']);
        if (!in_array($imageFileType, $typeFile)) {
            echo_r(['extension_not_found' => $file]);
        }

        $nameFile = explode('.', $file['name']);
        $file_name = rend_string($nameFile[0]) . round(microtime(true) * 1000) . '.' . $imageFileType;

        $file_target = $path . $file_name;
        move_uploaded_file($file['tmp_name'], $file_target);
        chmod($file_target, 0777);

        $d = now('d');
        write_log($file_target,  __DIR__ . "/../../logs/process/upload_files_{$d}.txt");
        return $file_name;
    }
}

if (!function_exists('req')) {

    /**
     * #req('name');
     *
     * @param string $key
     * @return string
     */
    function req($key)
    {
        return isset($_REQUEST[$key]) ? str($_REQUEST[$key]) : "";
    }
}

if (!function_exists('get_b64')) {

    /**
     * #get_b64('name');
     *
     * @param string $key
     * @param null $default
     * @return string|null
     */
    function get_b64($key, $default = null)
    {
        $query_str = array_keys($_GET);
        $decoded_query = base64_decode(isset($query_str[0]) ? $query_str[0] : []);
        $params = array();
        parse_str($decoded_query, $params);

        return isset($params[$key]) ? str($params[$key]) : $default;
    }
}

if (!function_exists('response')) {

    /**
     * @return Http|null
     */
    function response()
    {
        $http = new Http();

        if ($http instanceof Http) return $http;
        return null;
    }
}

if (!function_exists('str')) {

    /**
     * #str('text');
     *
     * @param string $data
     * @return string
     */
    function str($data = '')
    {
        $strippedText = strip_tags($data);
        $cutSlas = stripslashes($strippedText);
        return htmlspecialchars($cutSlas);
    }
}
