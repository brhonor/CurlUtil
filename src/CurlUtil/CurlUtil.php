<?php

namespace CurlUtil;
/**
 * Class CurlUtil
 * 发送 GET POST DELETE PUT 请求
 * @param string $url [请求地址]
 * @param string $method [请求方法 GET POST PUT DELETE]
 * @param array or string $data [请求数据]    为array时:cURL会把数据编码成 multipart/form-data; json时:数据会被编码成 application/x-www-form-urlencoded
 * @param array $header [请求头]  eg: ["Content-Type:application/json"]
 * @param int $timeout [超时时间]
 *
 * @return array($httpCode,$body,$errno,$error)
 * - $httpCode [http请求状态码]
 * - $body string [响应正文]
 * - $errno int [错误码]
 * - $error string [错误描述]
 *
 */
class CurlUtil
{
    public static function request($url, $method = "GET", $data = '', $header = [], $timeout = 0)
    {
        if (empty($url)) {
            return false;
        }
        //控制请求方法范围
        $httpMethod = array('GET', 'POST', 'PUT', 'DELETE');
        $method = strtoupper($method);
        if (!in_array($method, $httpMethod)) {
            return false;
        }

        //初始化http请求
        $ch = curl_init();

        // 成功只将结果返回，不自动输出任何内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 设置返回头信息 设置不返回
        curl_setopt($ch, CURLOPT_HEADER, false);

        // 兼容HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https请求 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//https请求 不验证HOST

        // 设置请求头
        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        switch ($method) {
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
        }

        //格式化发送数据
        if ($method == 'GET' && is_array($data)) {
            if (stripos($url, "?") === FALSE) {
                $url .= '?';
            }
            $url .= http_build_query($data);
        } elseif ($method == 'POST' || $method == 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        // 设置超时时间
        if ($timeout > 0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }

        // 读取状态
        $result['httpCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //发送请求获取返回响应
        $result['data'] = curl_exec($ch);

        // 读取错误号
        $result['errno'] = curl_errno($ch);

        // 读取错误详情
        $result['error'] = curl_error($ch);

        curl_close($ch);

        return $result;
    }
}

/**
 * @example
 */

/**
 * POST
 * 当要传文件时，data必须为array:cURL会把数据编码成 multipart/form-data;
 */
//$url = 'https://dev.tapai.tv/face_merge';
//$header = [
//    "uid:48639110",
//    "x-access-sign:zhimakaimen0dingdong",
//    "x-access-time:1"
//];
//$data = [
//    'img' => new CURLFile('./timg.png'),
//    'style_id' => 0
//];
//$resp = CurlUtil::request($url, 'post', $data, $header, 30);
//var_dump($resp);

/**
 * DELETE
 */
//$url = 'https://album-dev.tapai.tv/del_publish/25/1796667';
//$header = [
//    "uid:10000",
//    "x-access-sign:boluoboluomi",
//    "x-access-time:1"
//];
//$resp = CurlUtil::request($url, 'delete', '', $header);
//var_dump($resp);


/**
 * PUT  要用x-www-form-urlencoded
 */
//$url = 'https://dev.tapai.tv/img_setting';
//$header = [
//    "uid:48639110",
//    "x-access-sign:zhimakaimen0dingdong",
//    "x-access-time:1"
//];
//$data = [
//    'val' => 666,
//];
//$data = json_encode($data, JSON_UNESCAPED_UNICODE);
//$resp = CurlUtil::request($url, 'put', $data, $header);
//var_dump($resp);

