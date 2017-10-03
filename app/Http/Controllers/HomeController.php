<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\AuthorizedController;
use Illuminate\Support\Facades\Request;

class HomeController extends AuthorizedController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function download()
    {
        $data = [];

        if(Request::has('base-url')) {
            $baseUrl = Request::get('base-url');
            $targetUrl = $this->parseUrl($baseUrl);

            $title = '';
            $index = 0;

            if($targetUrl) {
                // check value first
                $firstPage = $targetUrl . '&pn=0';
                $content = @file_get_contents($firstPage);
                if($content == false) {
                    $data['error'] = 'The url is not valid. Please check the url again.';
                } else {
                    $urls = array();

                    $isPDF = $this->checkIfPDF($content);
                    if($isPDF) {
                        $temp = explode('/index.php?', $baseUrl);
                        $targetSiteBaseUrl = $temp[0];

                        $targetSiteBasePath = $this->parsePDFBasePath($content);
                        if($targetSiteBasePath != '') {
                            $targetSiteBasePath = dirname($targetSiteBasePath);
                            $title = $this->getTitle($content);

                            // make path
                            $targetPath = public_path('novels/' . $title);
                            mkpath($targetPath);

                            $index = 1;
                            while(true) {
                                // check pdf is exist
                                //$filePath = "$targetPath/$index.pdf";
                                $url = "$targetSiteBaseUrl/$targetSiteBasePath/$index.pdf";
                                $res = check_remote_file($url);

                                if($res == false) {
                                    break;
                                }

                                $urls[] = $url;

                                /*if($index > 3) {
                                    break;
                                }*/

                                $index++;
                            }
                        }


                        $data['title'] = $title;
                        $data['totalPages'] = $index;
                        $data['urls'] = $urls;
                    } else {
                        $html = $this->parseContent($content);
                        if($html == false) {
                            $data['error'] = 'The url is not valid. Please check the url again.';
                        } else {
                            // get title and total pages
                            $title = $this->getTitle($content);

                            // make path
                            $targetPath = public_path('novels/' . $title);
                            mkpath($targetPath);

                            // save first page
                            $this->saveHtml($html, $targetPath);

                            // download pages
                            $index = 1;
                            while(true) {
                                $pageUrl = $targetUrl . "&pn=$index";
                                $html = @file_get_contents($pageUrl);
                                if($html == false) {
                                    break;
                                }

                                $content = $this->parseContent($html);
                                if($content == false || strlen($content) < 500) {
                                    break;
                                }

                                // save content to html
                                $this->saveHtml($content, $targetPath, $index);

                                $index++;
                                /*if($index == 3) {
                                    break;
                                }*/
                            }

                            $data['title'] = $title;
                            $data['totalPages'] = $index;
                        }
                    }
                }
            }
        }

        return view('home', $data);
    }

    /**
     * @param $url
     * @return string
     */
    private function parseUrl($url)
    {
        $parsedUrl = parse_url(prep_url($url));
        parse_str(isset($parsedUrl['query'])?$parsedUrl['query']:'', $params);
        if(isset($params['pn'])) {
            unset($params['pn']);
        }

        $scheme = (isset($parsedUrl['sheme']) ? $parsedUrl['sheme'] : 'http') . '://';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        if($host == '') {
            return '';
        }

        $url = $scheme . $host . '/' . $parsedUrl['path'] . '?' . http_build_query($params);
        return $url;
    }

    /**
     * @param $content
     * @return string
     */
    private function getTitle($content) {
        $doc = new \DOMDocument;
        $doc->preserveWhiteSpace = FALSE;
        @$doc->loadHTML($content);

        $nodes = $doc->getElementsByTagName('title');
        $title = @$nodes->item(0)->nodeValue;
        return mb_convert_encoding($title, "UTF-8") ?: 'No Title Detected';
    }

    private function checkIfPDF($content) {
        // check if this is pdf novel
        $isPDF = strpos($content, 'function showcontent()');
        if($isPDF != false) {
            return true;
        }

        return false;
    }

    /**
     * @param $content
     * @return bool
     */
    private function parseContent($content) {
        $doc = new \DOMDocument;
        $doc->preserveWhiteSpace = FALSE;
        @$doc->loadHTML($content);

        // remove comments
        $xpath = new \DOMXPath($doc);
        foreach($xpath->query('//ul[contains(attribute::class, "comment_list")]') as $e ) {
            // Delete this node
            $e->parentNode->removeChild($e);
        }
        foreach($xpath->query('//div[contains(attribute::class, "comment")]') as $e ) {
            // Delete this node
            $e->parentNode->removeChild($e);
        }

        // get right content
        $node = @$doc->getElementById('wrapper');//->getElementsByTagName('div')->item(0);
        if(!$node) {
            return false;
        }

        $node = $node->getElementsByTagName('div')->item(0);
        if(!$node) {
            return false;
        }

        $node = $node->getElementsByTagName('div')->item(0);
        if(!$node) {
            return false;
        }

        // replace all link with #
        $links = $node->getElementsByTagName('a'); // get first
        foreach($links as $link) {
            $url = $link->getAttribute('href');
            $pn = get_param_from_url($url, 'pn');
            if($pn >= 0) {
                $url = sprintf('%03d.html', $pn);
            } else {
                $url = '#';
            }
            $link->setAttribute('href', $url);
        }

        return @inner_html($node);
    }

    private function parsePDFBasePath($content) {
        $doc = new \DOMDocument;
        $doc->preserveWhiteSpace = FALSE;
        @$doc->loadHTML($content);

        // remove comments
        $xpath = new \DOMXPath($doc);
        foreach($xpath->query('//img[contains(attribute::src, "contents/book/literature")]') as $e ) {
            // Delete this node
            return $e->getAttribute('src');
        }

        return '';
    }

    /**
     * @param $content
     * @param $path
     * @param int $index
     */
    private function saveHtml($content, $path, $index=0) {
        $fileName = $path . '/' . sprintf('%03d.html', $index);
        $content = view('novel.page', compact('content', 'index'));

        $file = fopen($fileName, 'w+');
        fwrite($file, $content);
        fclose($file);
    }
}
