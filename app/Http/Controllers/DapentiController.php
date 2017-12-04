<?php

namespace App\Http\Controllers;
use \Curl\Curl;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Http\Request;

class DapentiController extends Controller
{


    private function get($url){
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer('http://www.dapenti.com');
        $curl->setHeader('CLIENT-IP', '127.0.0.1');
        $curl->setHeader('X-FORWARDED-FOR', '127.0.0.1');
        $curl->get($url);
        $curl->close();
        if ($curl->error) {
            response()->json(['status' => 'error', 'code' => $curl->errorCode, 'message' => $curl->errorMessage])->send();
            die();
        } else {
            return $curl->response;
        }
    }

    private function replaceImage($url){
        return str_replace('http://pic.yupoo.com/dapenti/', route('dapenti.image').'?url=', $url);
    }

    public function image(Request $request){
        $url = 'http://pic.yupoo.com/dapenti/'.$request->get('url');
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer('http://www.dapenti.com');
        $curl->setHeader('CLIENT-IP', '127.0.0.1');
        $curl->setHeader('X-FORWARDED-FOR', '127.0.0.1');
        $curl->get($url);
        return response($curl->response)->withHeaders($curl->responseHeaders);
    }


    /*
    https://appb.dapenti.com/index.php?s=/home/api/tugua/p/1/limit/30
    https://appb.dapenti.com/index.php?s=/Home/api/lehuo/p/1/limit/10
    https://appb.dapenti.com/index.php?s=/Home/api/duanzi/p/1/limit/10
    */
    public function list($type)
    {
        $url = 'https://appb.dapenti.com/index.php?s=/home/api/'.$type.'/p/1/limit/30';
        $response = $this->get($url);
        $result = collect($response->data)->transform(function ($item, $key) {
            if ($item->title != 'AD') {
                $item->link = route('dapenti.show', ['id' => explode('id=', $item->link)[1]]);
                $item->imgurl = $this->replaceImage($item->imgurl);
                return $item;
            }
        });
        // return response()->json($result->filter()->values()->all());
        $content = $result->filter()->values()->all();
        // dd($content);
        return view('dapenti.list', ['title' => $type, 'content' => $content]);

    }

    public function show($id)
    {
        $url = 'http://www.dapenti.com/blog/more.asp?name=xilei&id='.$id;
        $response = $this->get($url);
        $html = HtmlDomParser::str_get_html( iconv('GBK',"UTF-8//IGNORE", $response) );
        foreach ($html->find('script') as $ad)
        {
            $ad->outertext = '';
        }
        foreach ($html->find('ins') as $ad)
        {
            $ad->outertext = '';
        }
        $title = explode('--', $html->find('title',0)->innertext())[1];
        $content = str_replace('<hr>广告<br><!-- bigbanner --><br><hr>', '',$html->find('div.oblog_text',0)->innertext);
        $content = $this->replaceImage($content);
        return view('dapenti.show', ['title' => $title, 'content' => $content]);
    }

}
