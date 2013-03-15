<?php
/** 输入数据管理类
 */
class Input
{
    public static $allowed_tags  = 'a|b|i|u|s|strong|em|strike|span|font|img|p|br|pre|div|';
    public static $allowed_attrs = 'href|size|style|src|color|target|align|';

    static public function getInstance()
    {
        static $instance;
        if(!isset($instance))
        {
            $c = __CLASS__;
            $instance = new $c;
        }

        return $instance;
    }

    /**
     * for backward compatibility
     * @param $input
     * @return $input
     */
    static public function getVar($input)
    {
        return $input;
    }

    /**
     * filter HTML
     * @param $html
     * @return string
     */
    static public function safeHTML($html)
    {
        // 保证标签匹配
        $body_node = self::makeNode($html);
        $html = self::innerHTML($body_node);
        // 去除不允许的标签
        $arr_allowed_tags = explode('|', self::$allowed_tags);
        foreach($arr_allowed_tags as &$tag)
        {
            $tag = '<' . trim($tag) . '>';
        }
        $html = strip_tags($html, implode('', $arr_allowed_tags));

        // 去除不允许的属性
        $body_node = self::makeNode($html);
        $body_node = self::filterNodeAttr($body_node);

        return self::innerHTML($body_node);
    }

    /**
     * 递归过滤不允许的属性
     * @param $node
     * @return $node
     */
    static function filterNodeAttr(&$node)
    {
        $arr_allowed_attrs = explode('|', self::$allowed_attrs);

        if ( !$node->attributes ) {
            return $node;
        }

        foreach ($node->attributes as $attr)
        {
            $attr_name = strtolower ($attr->nodeName);

            if (!in_array($attr_name, $arr_allowed_attrs))
            {
                $node->removeAttribute($attr_name);
            }
            else if ($attr_name == 'style')
            {
                // style 属性里面只允许 font-family, font-style, font-weight, color
                $arr_styles = explode(';', $attr->nodeValue);
                $arr_styles_filtered = array ();
                foreach ($arr_styles as $str)
                {
                    $str = trim($str);
                    if (preg_match('/^[font\-|color]/i', $str))
                    {
                        $arr_styles_filtered [] = $str;
                    }
                }
                $styles_filtered = implode(';', $arr_styles_filtered);
                if ($styles_filtered)
                {
                    $attr->nodeValue = $styles_filtered;
                }
                else
                {
                    $node->removeAttribute($attr_name);
                }
            }
            else if ($attr_name == 'href')
            {
                // href 不允许以 javascript: 开头
                $attr_value = $attr->nodeValue;
                if (preg_match('/^javascript/i', $attr_value))
                {
                    $node->removeAttribute($attr_name);
                }
            }
        }

        if ($node->hasChildNodes())
        {
            foreach($node->childNodes as $child_node)
            {
                $child_node = self::filterNodeAttr($child_node);
            }
        }

        return $node;
    }

    /**
     * 用所给的 HTML 生成一个节点
     * @param $html
     * @return DOMElement
     */
    static function makeNode($html)
    {
        $dom = new DOMDocument();

        $dom->substituteEntities = false;

        $html_prefix = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"><title></title></head><body>';
        $html_suffix = '</body></html>';

        $dom->loadHTML($html_prefix . $html . $html_suffix);

        $body_node = $dom->getElementsByTagName('body')->item(0);

        return $body_node;
    }

    /**
     * get innerHTML of a node
     * @param $node
     */
    static function innerHTML($node)
    {
        $doc = new DOMDocument();
        foreach ($node->childNodes as $child)
        {
            $copied = $doc->importNode($child, true);
            if ($copied)
            {
               $doc->appendChild($copied);
            }
        }

        return trim(preg_replace_callback('/&#(\d+);/', array(__CLASS__, 'entityToChar'), $doc->saveHTML()));
    }

    /**
     * 将实体值（Unicode）转回为字符
     * @param $v
     * @return String
     */
    static function entityToChar($v)
    {
        return iconv('ucs-2', 'UTF-8', pack('v', $v[1]));
    }
}
