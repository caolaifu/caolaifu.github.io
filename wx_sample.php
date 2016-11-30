<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "life");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
              the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            file_put_contents("./post.json",$postStr);
            
            //$RX_RYPE=trim($postObj->MsgType);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE) {
                case "text":
                    $resultStr = $this->handleText($postObj);
                    break;
                case "event":
                       $resultStr = $this->handleEvent($postObj);
                    break;
               case "image":
                      $resultStr=$this->handleImage($postObj);
                       break;
                default:
                    $resultStr = "Unknow msg type: " . $RX_TYPE;
                    break;
            }
            echo $resultStr;
            //  echo $resultStr;         
        } else {
            echo "";
            exit;
        }
    }

    private function checkSignature() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function handleText($postObj) {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
        $msgType = "text";

        if (!empty($keyword)) {

            if ($keyword == '你好') {
                 $contentStr = 'Hello';
                 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            } elseif ($keyword == '苏州') {
                 $contentStr = '上有天堂，下有苏杭';
                 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            } elseif($keyword=="图片"){
                 $resultStr= $this->handleImage($postObj);
                
            }elseif($keyword=='图文'){
                $resultStr=$this->handleMultText($postObj);
                
            }else{
                $contentStr = '感谢关注'; 
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
              //  echo $resultStr;
            }              
            echo $resultStr;
           
        } else {
            echo "Input something...";
        }
    }

    public function handleMultText($postObj) { //多图文 %s是字符串
       
        $textHeader = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>";

        $textBody = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";

        $textFooter = "</Articles>
                    <FuncFlag>0</FuncFlag>
                    </xml>
                    ";
        $keyword = trim($postObj->Content);



        if (!empty($keyword)) {

            $record[0] = array(
                'title' => '观前街',
                'description' => '观前街位于江苏苏州市区，是成街于清朝时期的百年商业老街，街上老店名店云集，名声远播海内外...',
                'picUrl' => 'http://zhiyou.kantphp.com/wxtest/meidia/image.jpg',
                'url' => 'http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDM0NTEyMg==&appmsgid=10000056&itemidx=1&sign=ef18a26ce78c247f3071fb553484d97a#wechat_redirect'
            );
            $record[1] = array(
                'title' => '拙政园',
                'description' => '拙政园位于江苏省苏州市平江区，是苏州四大古名园之一，也是苏州园林中最大、最著名的一座，被列入《世界文化遗产名录》，堪称中国私家园林经典...',
                'picUrl' => 'http://zhiyou.kantphp.com/wxtest/meidia/image.jpg',
                'url' => 'http://mp.weixin.qq.com/mp/appmsg/show?biz=MjM5NDM0NTEyMg==&appmsgid=10000056&itemidx=1&sign=ef18a26ce78c247f3071fb553484d97a#wechat_redirect'
            );
            $record[2] = array(
                'title' => '拙政园',
                'description' => '拙政园位于江苏省苏州市平江区，是苏州四大古名园之一，也是苏州园林中最大、最著名的一座，被列入《世界文化遗产名录》，堪称中国私家园林经典...',
                'picUrl' => 'http://zhiyou.kantphp.com/wxtest/meidia/image.jpg',
                'url' => 'http://mp.weixin.qq.com/mp/appmsg/show?biz=MjM5NDM0NTEyMg==&appmsgid=10000056&itemidx=1&sign=ef18a26ce78c247f3071fb553484d97a#wechat_redirect'
            );

            $bodyCount = count($record);
            $bodyCount = $bodyCount < 10 ? $bodyCount : 10;
            
            $body = sprintf($textHeader, $postObj->FromUserName, $postObj->ToUserName,time(), $bodyCount);

            foreach ($record as $key => $value) {
                $body .= sprintf($textBody, $value['title'],$value['description'], $value['picUrl'], $value['url']);
            }
            $body.=$textFooter;
            
            echo $body;
        } else {
            echo "Input something...";
        }
    }
    
    public function handleImage($postObj){
        $imgTpl="<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[image]]></MsgType>
                <Image>
                <MediaId><![CDATA[%s]]></MediaId>
                </Image>
                </xml>
                ";
        $keyword = trim($postObj->Content);
        
       if (!empty($keyword)) {
              $body = sprintf($imgTpl, $postObj->FromUserName, $postObj->ToUserName,time(),'nd06Pik942VrY3p4vJX9-K1rXiaFpT8Qo2qubuyCOlafUgTyeNAF-lH6Kfn43k7E');
              
              echo $body;
       }else{
           echo "sssss";
       }
      
    }

}

?>