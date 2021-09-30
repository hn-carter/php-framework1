<?php

/**
 * ルーティング定義配列とPATH_INFOを受け取り、ルーティングパラメータを特定
 */
class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    /**
     * ルーティング定義配列を正規表現で扱える形式に変換
     * 
     * @param array $definitions ルーティング定義配列
     * @return array
     */
    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            // URLをスラッシュ(/)で分割
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (0 === strpos($token, ':')) {
                    $name = substr($token, 1);
                    // 分割した値にコロン(:)で始まる文字列があれば正規表現の形式に変換
                    $token = '(?P<' . $name . '>[^/]+)';
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }
}