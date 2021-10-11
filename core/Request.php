<?php

/**
 * ユーザーのリクエスト情報を制御するクラス
 */
class Request
{
    /**
     * HTTPメソッドがPOSTかどうか判定
     * 
     * @return bool
     */
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        return false;
    }

    /**
     * $_GET変数から指定した名前のURLパラメータ値を取得
     * 
     * @param  string      $name URLパラメータ名
     * @param  string|null $default 名前の値が存在しない場合のデフォルト値
     * @return string|null 取得した値
     */
    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return $default;
    }

    /**
     * $_POST変数から指定した名前のURLパラメータ値を取得
     * 
     * @param  string      $name URLパラメータ名
     * @param  string|null $default 名前の値が存在しない場合のデフォルト値
     * @return string|null 取得した値
     */
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return $default;
    }

    /**
     * サーバのホスト名を取得する
     * 
     * @return string
     */
    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * HTTPSでアクセスされたかどうか判定
     * 
     * @return bool
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }

    /**
     * リクエストされたURI情報を取得
     * 
     * @return string URIのホスト部分以降の値
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * ベースURLを取得
     * 
     * @return string ベースURL
     */
    public function getBaseUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];

        $request_uri = $this->getRequestUri();

        if (0 === strpos($request_uri, $script_name)) {
            // フロントコントローラがURLに含まれる場合
            return $script_name;
        } else if (0 === strpos($request_uri, dirname($script_name))) {
            // フロントコントローラが省略されている場合
            return rtrim(dirname($script_name), '/');
        }
        return '';
    }

    /**
     * GETパラメーターを除いたベースURL以降のURLを取得
     * 
     * @return string ベースURLを除いたURL
     */
    public function getPathInfo()
    {
        $base_url = $this->getBaseUrl();
        $request_uri = $this->getRequestUri();

        if (false !== ($pos = strpos($request_uri, '?'))) {
            // $_SERVER['REQUEST_URI']に含まれるGETパラメーターを取り除く
            $request_uri = substr($request_uri, 0, $pos);
        }

        // GETパラメーターを除いた$_SERVER['REQUEST_URI']からベースURLを除く
        $path_info = (string)substr($request_uri, strlen($base_url));

        return $path_info;
    }
}
