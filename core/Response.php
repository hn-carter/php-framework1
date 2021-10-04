<?php

/**
 * レスポンスを表す
 * HTTPヘッダとHTMLなどのコンテンツを返すのが役割
 */
class Response
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();

    /**
     * プロパティの値を元にレスポンスを送信
     */
    public function send()
    {
        // ステータスコードとテキストを送信
        header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_text);

        foreach ($this->http_headers as $name => $value) {
            // HTTPレスポンスヘッダを送信
            header($name . ': ' . $value);
        }

        // レスポンス内容を送信
        echo $this->content;
    }

    /**
     * HTMLなどクライアントに返す内容を設定
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * HTTPのステータスコードを設定
     *
     * @param int $status_code ステータスコード
     * @param string $status_text ステータステキスト
     */
    public function setStatusCode($status_code, $status_text = '')
    {
        $this->statu_code = $status_code;
        $this->statu_text = $status_text;
    }

    /**
     * HTTPヘッダを連想配列に設定
     *
     * @param string $name ヘッダの名前
     * @param string $value ヘッダの値
     */
    public function setHttpHeader($name, $value)
    {
        $this->http_headers[$name] = $value;
    }
}