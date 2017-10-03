<?php

if(!function_exists('mkpath')) {
    function mkpath($path) {
        if(@cmkdir($path) or file_exists($path)) {
            return true;
        }

        return (mkpath(dirname($path)) and cmkdir($path));
    }
    function cmkdir($path) {
        $result = mkdir($path);
        /*if($result) {
            if(!file_exists($path . '/index.html')) {
                $str = '<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>';

                $file = fopen($path . '/index.html', 'w');
                fwrite($file, $str);
                fclose($file);
            }
        }*/

        return $result;
    }
}

function url_exists($url) {
    if( @get_headers($url) ) return true;

    return false;
}

function prep_url($str = '') {
    if ($str === 'http://' OR $str === '') {
        return '';
    }

    $url = parse_url($str);
    if ( ! $url OR ! isset($url['scheme'])) {
        return 'http://'.$str;
    }

    return $str;
}

function inner_html(DOMElement $element)
{
    $doc = $element->ownerDocument;
    $html = '';
    foreach ($element->childNodes as $node) {
        $html .= $doc->saveHTML($node);
    }
    return $html;
}

function get_elements_by_class(&$parentNode, $tagName, $className) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute('class'), $className) !== false) {
            $nodes[]=$temp;
        }
    }

    return $nodes;
}