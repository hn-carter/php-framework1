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
}
