<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Facile 主题和 MWordStar 主题的辅助插件。
 * 
 * @package MfThemePlugin
 * @author Changbin
 * @version 1.1.0
 * @link https://www.misterma.com
 */
class MfThemePlugin_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件
     */
    public static function activate()
    {
        // 挂载评论内容输出接口（处理 RSS 私密评论）
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('MfThemePlugin_Plugin', 'filterFeedComments');
        // 挂载文章内容输出接口（处理 RSS 短代码）
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('MfThemePlugin_Plugin', 'filterFeedContent');
        return _t('插件已激活');
    }

    /**
     * 禁用插件
     */
    public static function deactivate()
    {
        return _t('插件已禁用');
    }

    /**
     * 插件配置面板
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 个人配置面板
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 过滤 RSS / Feed 输出的评论内容
     * 
     * @param string $content 评论内容
     * @param Widget_Abstract_Comments $widget 评论组件
     * @param string $lastResult 上一个插件返回的结果
     * @return string
     */
    public static function filterFeedComments($content, $widget, $lastResult)
    {
        $content = empty($lastResult) ? $content : $lastResult;

        // 仅在通过 RSS/Feed 路由访问时执行（如 /feed/comments/ 或 /feed/）
        if (self::isFeedRequest()) {
            // 检测是否包含 [hide] 标记包裹的内容
            if (preg_match('/\[hide\](.*?)\[\/hide\]/is', $content)) {
                return '私密评论，仅评论者和管理员可见。';
            }
        }

        return $content;
    }

    /**
     * 过滤 RSS / Feed 输出的文章内容
     *
     * 主题短代码在 RSS 页面不会解析，这里把短代码转换为 RSS 可读的内容：
     * alert / collapse 只保留内部内容，button / badge 转为普通链接，hide 转为提示文字。
     * 
     * @param string $content 文章内容
     * @param Widget_Abstract_Contents $widget 内容组件
     * @param string $lastResult 上一个插件返回的结果
     * @return string
     */
    public static function filterFeedContent($content, $widget, $lastResult)
    {
        $content = empty($lastResult) ? $content : $lastResult;

        // 仅在通过 RSS/Feed 路由访问时执行
        if (self::isFeedRequest()) {
            $content = self::convertShortcodes($content);
        }

        return $content;
    }

    /**
     * 判断当前请求是否为 RSS / Feed 页面
     *
     * @return bool
     */
    private static function isFeedRequest()
    {
        $pathInfo = Typecho_Request::getInstance()->getPathInfo();
        return is_string($pathInfo) && preg_match('#^/feed#', $pathInfo);
    }

    /**
     * 转换主题短代码为 RSS 可读的内容
     *
     * 跳过 pre / code 代码块，支持短代码嵌套，自内向外反复替换。
     *
     * @param string $content
     * @return string
     */
    private static function convertShortcodes($content)
    {
        $supportedTags = array('button', 'alert', 'collapse', 'badge', 'hide');
        $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|\[(' . implode('|', $supportedTags)
            . ')\b([^\]]*?)\](.*?)\[\/\2\]/is';

        for ($i = 0; $i < 10; $i++) {
            $converted = preg_replace_callback($pattern, function ($matches) {
                // 代码块原样保留，不处理其中的短代码
                if (!empty($matches[1])) {
                    return $matches[1];
                }

                $tag = strtolower($matches[2]);
                $inner = $matches[4];

                // 隐藏内容不输出，转为提示文字
                if ('hide' === $tag) {
                    return '此处是隐藏内容，请到文章页查看。';
                }

                // button / badge 包含 url 时转为普通链接，否则只保留内部内容
                if ('button' === $tag || 'badge' === $tag) {
                    $url = '';
                    if (preg_match('/\burl\s*=\s*(["\'])(.*?)\1/i', $matches[3], $urlMatches)) {
                        $url = trim($urlMatches[2]);
                    }

                    if ('' !== $url) {
                        return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . $inner . '</a>';
                    }
                }

                return $inner;
            }, $content);

            if (null === $converted || $converted === $content) {
                break;
            }

            $content = $converted;
        }

        return $content;
    }
}
