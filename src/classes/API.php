<?php

namespace Service\Http;

class API
{
    private $request;
    private $type;
    private $debug = false;
    private $header = false;
    private $user = false;
    private $urlGet = false;
    private $rows = false;

    public function setDebug($data)
    {
        $this->debug = $data;
    }

    public function setGet($data)
    {
        $this->urlGet = $data;
    }

    public function getToken($name = "Authorization")
    {
        $this->header = apache_request_headers();
        return $this->header[$name];
    }

    public function getAuthen()
    {
        $this->user = [
            "username" => $_SERVER['PHP_AUTH_USER'],
            "password" => $_SERVER['PHP_AUTH_PW']
        ];
        return $this->user;
    }

    public function setRequest()
    {
        $str = file_get_contents("php://input");

        if ($this->isJson($str) && $str) {
            $this->request = json_decode($str, true);
            $this->type = 'json';
            if ($_GET && $this->urlGet) {
                $this->request = array_merge($this->request, $_GET);
            }
        } else if (preg_match('/form-data;/', $str)) {
            $this->request = [];
            $this->request = $this->parseRawHttpRequest($this->request);
            $this->type = 'form-data';
            if ($_GET && $this->urlGet) {
                $this->request = array_merge($this->request, $_GET);
            }
        } else if (preg_match("~([^&]+)=([^&]+)~", $str)) {
            parse_str($str, $this->request);
            $this->type = 'urlencode';
            if ($_GET && $this->urlGet) {
                $this->request = array_merge($this->request, $_GET);
            }
        } else if ($_POST) {
            $this->request = $_POST;
            $this->type = 'POST';
            if ($_GET && $this->urlGet) {
                $this->request = array_merge($this->request, $_GET);
            }
        } else if ($_GET) {
            $this->request = $_GET;
            $this->type = '_GET';
            if ($_GET && $this->urlGet) {
                $this->request = array_merge($this->request, $_GET);
            }
        }

        return $this->request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getType()
    {
        return $this->type;
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function parseRawHttpRequest(array &$data)
    {
        $input = file_get_contents('php://input');
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];
        $blocks = preg_split("/-+$boundary/", $input);
        array_pop($blocks);
        foreach ($blocks as $id => $block) {
            if (empty($block)) {
                continue;
            }
            if (strpos($block, 'application/octet-stream') !== false) {
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } else {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $data[$matches[1]] = $matches[2];
        }
        return $data;
    }

    public function setError($arr, $message)
    {
        return $this->response(false, $arr, $message);
    }

    public function setResponse($arr, $message)
    {
        return $this->response(true, $arr, $message);
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    private function response($type, $arr, $message)
    {
        $response = [
            'status' => $type,
            'message' => $message
        ];
        if ($arr) {
            $response['data'] = $arr;
        }
        if ($this->rows) {
            $response['rows'] = $this->rows;
        }
        if ($this->debug) {
            $response['request'] = $this->request;
            $response['type'] = $this->type;
            if ($this->header) {
                $response['header'] = $this->header;
            }
            if ($this->user) {
                $response['user'] = $this->user;
            }
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
