<?php

/**
 * コントローラ
 */
abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    /**
     * 認証が必要なアクションのリスト
     */
    protected $auth_actions = array();

    /**
     * コンストラクタ
     * 
     * @param Application $application アプリ
     */
    public function __construct($application)
    {
        // コントローラ名をクラス名から取得
        // get_class($this)で自身のクラス名を取得し、substrの-10によって後ろの'Controller'を取り除く
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->db_manager  = $application->getDbManager();
    }

    /**
     * Applicationクラスから呼ばれ、アクションを実行する
     * 
     * @param string $action アクション名
     * @param array $params アクションメソッドに渡すパラメータ
     * @return string レスポンスとして返すコンテンツ
     */
    public function run($action, $params = array())
    {
        $this->action_name = $action;

        // アクションのメソッド名 : アクション名+'Action'
        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            // アクションメソッドが存在しない場合404エラー画面へ
            $this->forward404();
        }

        // ログインせずに認証が必要なアクションを呼び出した場合、例外を投げる
        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            throw new UnauthorizedActionException();
        }

        // アクションメソッドを呼び出しコンテンツを受け取る
        $content = $this->$action_method($params);

        return $content;
    }

    /**
     * ビューファイルをレンダリング
     * 
     * @param array $variables ビューファイルに渡す連想配列
     * @param string $template ビューファイル名(nullの場合はアクション名を使用)
     * @param string $layout レイアウトファイル名
     * @return string レンダリング結果
     */
    protected function render($variables = array(), $template = null, $layout = 'layout')
    {
        // Viewクラスのコンストラクタに渡すデフォルト値
        $defaults = array(
            'request'  => $this->request,
            'base_url' => $this->request->getBaseUrl(),
            'session'  => $this->session,
        );

        // Viewクラスのインスタンス作成
        $view = new View($this->application->getViewDir(), $defaults);

        // ビューファイルが指定されなかった場合は、アクション名を使用
        if (is_null($template)) {
            $template = $this->action_name;
        }

        // ビューファイルへのパスにコントロール名を追加
        $path = $this->controller_name . '/' . $template;

        return $view->render($path, $variables, $layout);
    }

    /**
     * 404画面へリダイレクト
     * 
     * @throws HttpNotFoundException
     */
    protected function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from '
            . $this->controller_name . '/' . $this->action_name);
    }

    /**
     * 任意のURLへリダイレクト
     * 
     * @param string $url
     */
    protected function redirect($url)
    {
        // リダイレクトには絶対URLが必要なため、'https://'または、'http://'で始まっていない場合
        // Requestオブジェクトを元に絶対URLを組み立てる
        if (!preg_match('#http?://#', $url)) {
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $base_url = $this->request->getBaseUrl();

            $url = $protocol . $host . $base_url . $url;
        }

        // リダイレクト
        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }

    /**
     * CSRFトークンを生成
     * 
     * @param string $form_name
     * @return string
     */
    protected function generateCsrfToken($form_name)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        // CSRFトークンとなるランダムな文字列を生成
        $bytes = random_bytes(32);
        $token = str_replace(['/', '+', '='], '', base64_encode($bytes));
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    /**
     * CSRFトークンが妥当か判定
     * 
     * @param string $form_name
     * @param string $token
     * @return boolean
     */
    protected function checkCsrfToken($form_name, $token)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        // セッションに一致するトークンが格納されているか判定
        if (false !== ($pos = array_search($token, $tokens, true))) {
            // 1度利用したトークンは削除する
            unset($tokens[$pos]);
            $this->session->set($key, $tokens);

            return true;
        }

        return false;
    }

    /**
     * 指定されたアクションに認証が必要か判定
     * 
     * @param string $action
     * @return boolean
     */
    protected function needsAuthentication($action)
    {
        if ($this->auth_actions === true
            || (is_array($this->auth_actions)
                && in_array($action, $this->auth_actions))) {
                return true;
        }
        
        return false;
    }
}
