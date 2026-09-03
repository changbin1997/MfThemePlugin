<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Facile 主题和 MWordStar 主题的辅助插件。
 * 
 * @package MfThemePlugin
 * @author Changbin
 * @version 1.0.0
 * @link https://www.misterma.com
 */
class MfThemePlugin_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件
     */
    public static function activate()
    {
        // 挂载评论内容输出接口（注意这里的类名更新了）
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('MfThemePlugin_Plugin', 'filterFeedComments');
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

        // 获取当前访问的路径信息
        $pathInfo = Typecho_Request::getInstance()->getPathInfo();

        // 仅在通过 RSS/Feed 路由访问时执行（如 /feed/comments/ 或 /feed/）
        if (strpos($pathInfo, '/feed') !== false) {
            // 检测是否包含 [hide] 标记包裹的内容
            if (preg_match('/\[hide\](.*?)\[\/hide\]/is', $content)) {
                return '私密评论，仅评论者和管理员可见。';
            }
        }

        return $content;
    }
}