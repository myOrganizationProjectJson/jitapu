<?php/** * VeryTalk @版权所有 * @author Jason 2014 *  获取 http://www.jitapu.com爬虫 */class GetnameAction extends Action{    public $GetJitapuInfo;    public $GetJitapuContent;    public function _initialize() {     $this->GetJitapuInfo=M("GetJitapuInfo");     $this->GetJitapuContent=M("GetJitapuContent");    }    /**     * 入口     */    public function index(){        $this->getJitapuInfo();    }        public function getcontent(){        if($_REQUEST['id']){            $content=$this->getPlatContent($_REQUEST['id']);            $this->assign("content",$content);            $_POST['keyword']=$content['title'];        }        $this->assign('serach','1');        $this->display("index");    }            /**     *      * @param string $KeyWord 搜索的关键字     * @param number $location 0歌曲 1歌手     */    public function getJitapuInfo(){       set_time_limit(0);        /**         *  获取 http://www.jitapu.com爬虫         */        header("Content-Type: text/html; charset=UTF-8");        //获取网页内容        $for=array("b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");        foreach($for as $fk=>$f){            echo $URL="http://www.jitapu.com/listArtist.aspx?path=".$f;        //得到网页内容        $html=file_get_contents($URL);        $html=mb_convert_encoding($html, "UTF-8", "GBK");        //匹配搜素结束全部内容        preg_match_all("/<tr>[\s]*<td class=\"dlList1\">[\s]*(.*)[\s]*<\/td>[\s]*<\/tr>/si",$html,$con);        //将内容从GBK转成UTF编码        preg_match_all("/<a href=\'(.*?)\'>(.*?)<\/a>/si",$con[1][0],$cons);        //$cons[1]是所有url [2]是所有名字        foreach($cons[1] as $k=>$r){            preg_match_all("/listSong(.*?)ArtistName=/si",$r,$urlpre);            $name=trim($cons[2][$k]);            $namess=$name;            $name=mb_convert_encoding($name, "GBK", "auto");            $name=rawurlencode($name);            $nameurl=$urlpre[0][0].$name;            $nameurl="http://www.jitapu.com/".$nameurl;            $html=file_get_contents($nameurl);            $html=mb_convert_encoding($html, "UTF-8", "GBK");            preg_match_all("/<div id=\"listMain\">(.*?)<\/div>/si",$html,$listurl);            //所有的谱子url            $listurl=$listurl[1][0];            preg_match_all("/<a href='(.*?)\'>(.*?)<\/a>/si",$listurl,$listurlall);            $info = array();            foreach($listurlall[1] as $ke=>$re){                //只要txt的                if (eregi("(TXT){1}",$re)){                    //去空格                    //查找URL和标题内容                    $urlTitle=$listurlall[2][$ke];                    //URL                    $Url="http://www.jitapu.com/".$re;                    $this->getJitapuContent($Url,$urlTitle,$namess);                }            }        }       }        return $info;    }    /**     * 获取详情啊啊啊     * @param string $URL     * @param string $title     * @param string $name     */    public function getJitapuContent($URL='',$title='',$name=''){        set_time_limit(0);        header("Content-Type: text/html; charset=UTF-8");        preg_match_all("/Url=(.*?)\.htm/si",$URL,$URL);        $URL= $URL[1][0];        $URL="http://www.jitapu.com/tabPages/".$URL.".htm";        //得到网页内容        $html=file_get_contents($URL);        preg_match_all("/<div id=\"txt\">[\s]*<pre>[\s]*(.*)[\s]*<\/pre>/si",$html,$con);        $html=mb_convert_encoding($con[1][0], "UTF-8", "GBK");        $html=preg_replace("/[-]/", "=",$html);         $this->setJitapuContent($title,$URL,$html,$name);        echo $title; echo "<br/>";        flush();        ob_flush();        //将内容从GBK转成UTF编码       // echo $html;    }        /**     * 储存点击查询后的信息     * @param string $title     * @param string $titleurl     * @param string $name     * @param string $nameurl     * @param string $cid     */    public function setJitapuInfo($title='',$titleurl='',$name='',$nameurl='',$cid='1'){        set_time_limit(0);        $time=time();        $data=array(            'title'=>$title,            'titleurl'=>$titleurl,            'name'=>$name,            'nameurl'=>$nameurl,            'cid'=>$cid,            'status'=>1        );        $status=$this->GetJitapuInfo->where($data)->find();        if($status){//等待处理...//            if($status['updatetime']<$time-216000){//两个月//                $data['updatetime']=$time;//                $status=$this->GetJitapuInfo->where($data)->save();//            }        }else{            $data['updatetime']=$time;            $this->GetJitapuInfo->add($data);        }    }    /**     * 储存获取到的谱子     * @param unknown $title     * @param unknown $titleurl     * @param unknown $content     * @param string $cid     */    public function setJitapuContent($title,$titleurl,$content,$name,$cid='1'){        set_time_limit(0);            $time=time();            $data=array(                'title'=>$title,                'titleurl'=>$titleurl,                'cid'=>$cid,                'name'=>$name,                'status'=>1            );            $status=$this->GetJitapuContent->where($data)->find();            if($status){//等待处理...    //            if($status['updatetime']<$time-216000){//两个月    //                $data['updatetime']=$time;    //                $status=$this->GetJitapuInfo->where($data)->save();    //            }            }else{                $data['content']=$content;                $data['updatetime']=$time;                $this->GetJitapuContent->add($data);            }    }}function change($content){    $content=str_replace("吉他谱 http://www.jitapu.com", "",$content);    $content=str_replace("我们欢迎您转载曲谱，但请保留我们的地址和作者名！", "纯文本吉他谱编辑网站",$content);    $content=str_replace("jitapu", "verytalk",$content);    echo $content;}?>