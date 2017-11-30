<?php


defined('_JEXEC') or die;
ini_set("memory_limit",-1);
ini_set("max_execution_time", "0");
	error_reporting(E_ALL); # Уровень вывода ошибок
	ini_set('display_errors', 'on'); # Вывод ошибок включён
	ini_set("log_errors", 'on'); # Логирование включено
 set_time_limit (0); 
 ob_implicit_flush();    
 
require_once ( JPATH_LIBRARIES . '/parser/classes/RollingCurl.class.php');
require_once ( JPATH_LIBRARIES . '/parser/classes/AngryCurl.class.php');
require_once ( JPATH_LIBRARIES . '/parser/simple_html_dom.php');

jimport('joomla.application.component.controllerform');

class ParserControllerLoad extends JControllerForm
{
   
    private $urlsspecial=array();
    private $urlsPage=array(); 
    private $urlsJobList=array(); 
    private $lim=0;
    private $categoryid=0;
    private $type=0;
    
    
	public function __construct()
	{
		$this->view_list = 'parsers';
		parent::__construct();
	}
    
    
    public function pracaspecial($array=NULL,$str=''){
     if(!empty($array)){ 
      
                                $AC = new AngryCurl(array($this, 'callback_praca_page'));
                                $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt'); 
                                //$AC->init_console();
                                foreach($array as $key => $val){
                                  $AC->get($val);   
                                }
                                $AC->execute(5);
                                $AC->flush_requests();  
                                unset($AC); 
                                
                                 
          if(isset($this->urlsspecial)){
            if(count($this->urlsspecial)>0){
             //   if(empty($str)){
                 //echo count($this->urlsspecial).'<br>';
               $this->pracaspecial($this->urlsspecial,'finish');
               // }
            }
          }
          
                   
          //if($str=='finish'){
            
             if(isset($this->urlsspecial)){        
               
              if(count($this->urlsspecial)==0){   
                      if(isset($this->urlsPage) )  {           
                          if(count($this->urlsPage)>0)  {
                          //  echo count($this->urlsPage);
                                $this->praca($this->urlsPage);
                         }         
                      }
                  unset($this->urlsspecial);  
              } 
            }   
        //  }
          

                         
       }                  
    }
    public function pracaurlpage($array=NULL){
        
         $jinput = JFactory::getApplication()->input;  
         $this->lim=$jinput->getVar('lim','0');
         $this->categoryid=$jinput->getVar('categoryid','0'); 
           if(empty($array)){
              $model = $this->getModel();
              $this->urlsJobList=$model->GetParserUrl();
           }
        /* if(count($this->urlsJobList)==0)  {
           echo "finish========================================<hr>";
            unset($this->urlsJobList);
          // $this->setRedirect('index.php?option=com_parser'); 
         }  */
          if(isset($this->urlsJobList)){
                  if(count($this->urlsJobList)>0)  {
                                        $AC = new AngryCurl(array($this, 'callback_praca_job'));
                                        $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt');  
                                        foreach($this->urlsJobList as $key => $val){
                                           
                                           if($this->lim==0){
                                            $AC->get($val['url']);                                             
                                           }else{
                                             if($key<=$this->lim){
                                                 $AC->get($val['url']); 
                                             }
                                           }
                                           
                                        }
                                        $AC->execute(5);
                                        $AC->flush_requests();  
                                        unset($AC);   
                                        
                                        
                         if(count($this->urlsJobList)>0)  {
                         //   $this->pracaurlpage($this->urlsJobList);
                         }   
                  }
          }
        $this->setRedirect('index.php?option=com_parser&view=pracabys'); 
    }
    
    
        public function praca($array=NULL){
                $jinput = JFactory::getApplication()->input;  

                $url=$jinput->getVar('hr','');
                $this->type=$jinput->getVar('typeid','0');
                $this->lim=$jinput->getVar('lim','0');
                $this->categoryid=$jinput->getVar('categoryid','0');
                             
                         
                if (!empty($url)) { 
                    
                    if (!empty($url) && $this->type==3) {  
                       if(empty($array)){ 
                         
                         if(isset($this->urlsspecial)){
                           if(count($this->urlsspecial)==0){ 
                                $AC = new AngryCurl(array($this, 'callback_praca_listurl'));
                                $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt'); 
                                $AC->get($url);  
                                $AC->execute(2);
                                $AC->flush_requests();  
                                unset($AC);  
                                $this->urlsspecial=array_unique($this->urlsspecial); 
                                $this->pracaspecial($this->urlsspecial);
                           }
                         }
           
                        
                     /*  print('<pre>');
                           print_r($this->urlsPage);    
                        print('</pre>'); */
                          /*  if(isset($this->urlsPage) )  {           
                               if(count($this->urlsPage)>0)  {
                                   $this->praca($this->urlsPage);
                                }         
                            }   */
                     
                        }else{
                       

                            $AC = new AngryCurl(array($this, 'callback_praca_list'));
                            if(count($array)>0) {
                                  //$AC->init_console();
                                $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt');    
                               
                                
                                foreach($array as $val){
                                  $AC->get($val);   
                                }
                              
                                $AC->execute(3);
                                $AC->flush_requests();  
                                unset($AC);  
                          //  echo '=>>>>>>>>>>'.count($this->urlsPage);
                                if(isset($this->urlsPage) )  {  
                                    if(count($this->urlsPage)>0){  
                                      
                                        $this->praca($this->urlsPage);
                                     }
                                  }  
                                 //  unset($this->urlsPage);   
                                
                            }
                            
                            
                            
                        }
                    }
                    
               }   
            $this->setRedirect('index.php?option=com_parser'); 
        }
    
    public function savev($array=NULL){
      $jinput = JFactory::getApplication()->input;  

       $url=$jinput->getVar('hr','');
       $type=$jinput->getVar('typeid','0');
       $this->lim=$jinput->getVar('lim','0');
       $this->categoryid=$jinput->getVar('categoryid','0');
             
         
  if (!empty($url)) {   
    
    if (!empty($url) && $type==1) {  
              if(empty($array)){  
                $AC = new AngryCurl(array($this, 'callback_function_page'));
             /*  foreach($urls as $val){
                  $AC->get($val);  
               }*/
            
                $AC->get($url);  
                $AC->execute(200);
                $AC->flush_requests();  
                unset($AC);  
                if(isset($this->urlsPage) )  {           
                   if(count($this->urlsPage)>0)  {

                       $this->savev($this->urlsPage);
                    }         
                }   
              }else{
                 
                $AC = new AngryCurl(array($this, 'callback_function_list'));
                if(count($array)>0) {
                // $AC->init_console();
                 $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt');    
                    foreach($array as $val){
                      $AC->get($val);   
                    }
                    unset($this->urlsPage); 
                    $AC->execute(200);
                    $AC->flush_requests();  
                    unset($AC);  
                }
            
                if(isset($this->urlsJobList)){
             
                     if(count($this->urlsJobList)>0)  {
                       
                        $AC = new AngryCurl(array($this, 'callback_function_job'));
                        $AC->load_useragent_list(  JPATH_LIBRARIES . '/parser/useragent_list.txt');  
                       
                        foreach($this->urlsJobList as $key => $val){
                           if($this->lim==0){
                            $AC->get($val['href']);  
                           } else{
                              if($key<=$this->lim){  
                                $AC->get($val['href']);  
                              }  
                           }
                               
                           /* if($key<=1){  
                                $AC->get($val['href']);  
                              }   */ 
                        
                        }
                              /* print('<pre>');
                           print_r(    $this->urlsJobList);    
                        print('</pre>'); */       
                        $AC->execute(200);
                        $AC->flush_requests();  
                        unset($AC);  
                    }
                  }
              }
       } // end type

       
       
     }// end empty url
   
       $this->setRedirect('index.php?option=com_parser'); 
    }
    
    
    
        # Callback function example
    public function callback_function_page($response, $info, $request)
    {
        
        if($info['http_code']!==200)
        {
              // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }
         else
        {
        
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);
            $this->urlsPage[]=$info['url'].'0/200/';
             if (count($html->find('.pager-content',0) )) {
                       $page =(int)$html->find('.pager-content .nav_last',0)->plaintext;
                       $page=$page*20/200;
                 for ($i=1; $i<=$page; $i++){
                    //if($i<=2){
                    $this->urlsPage[]='http://rabota.by/jobs-retail-sales/'.(200*$i).'/200/';
                  //}
                 }      
                       
             }
            
          $html->__destruct();   
        }
        return;
    }
    public function callback_function_list($response, $info, $request)
    {
        if($info['http_code']!==200)
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }else
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);
          ///echo '-->'.$info['url'].'<br>';
             if (count($html->find('.short_blocks',0) )) {
                   foreach ($html->find('div.adholder') as $key=> $value){
                      if(count($value->find('.useful_area',0))){
                         //echo $key.' '.$value->find('.useful_area .title a',0)->href.'<br>';
                         if(count($value->find('.useful_area .title a',0))){
                            $href='http://rabota.by/'.$value->find('.useful_area .title a',0)->href;
                         }
                      
                         if(count($value->find('.useful_area .email a',0))){
                          $email=$value->find('.useful_area .email a',0)->plaintext.'<br>';
                         } 
                           $this->urlsJobList[]=array(
                             'href'  =>$href,
                             'email' =>$email
                           );
                      }
                   }    
             }
           $html->__destruct();  
        }
        return;
    }
     
	public function getModel($name = 'Parser', $prefix = 'ParserModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}    
    
    
     public function callback_function_job($response, $info, $request)
    {   
        
        $model = $this->getModel();
        if($info['http_code']!==200)
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }else
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);
         //  echo $html;
         $data=array();
         $details=array();
         $content=array();
         $contact=array();
         $dt=array();
             if (count($html->find('.view',0) )) {
                  if (count($html->find('.view .title h1',0) )) {
                     $name=$html->find('.view .title h1',0)->plaintext;
                   } 
                   
                   if (count($html->find('.view .viewOrgName .listOrgName h2',0) )) {
                     $company=$html->find('.view .viewOrgName .listOrgName h2',0)->plaintext;
                   } 
                   
                   if (count($html->find('.view .main-info .salary',0) )) {
                      $price=$html->find('.view .main-info .salary',0)->plaintext;
                     // "от 5000 руб";
                   }
                  
                   foreach ($html->find('.view .viewInline .adv-point') as $key=> $value){
                             if (count($value->find('.adv-subheadings',0) )) {
                               if (count($value->find('span',1) )) {
                                  $details[trim($value->find('.adv-subheadings',0)->plaintext)]=$value->find('span',1)->plaintext;
                               }
                             }        
                     //  echo $value.'<br>';
                   }  
                   
                    foreach ($html->find('.view .adv-full-b') as $key=> $value){
                        if (count($value->find('.comment h5',0) )) {
                          if (count($value->find('.form-line',0))) { 
                            $content[trim($value->find('.comment h5',0)->plaintext)]=$value->find('.form-line .adv-point',0)->plaintext;
                          }
                        }
                        
                    } 
                    if (count($html->find('.view .contact-info',0) )) {
                        foreach ($html->find('.view .contact-info .form-line .adv-point') as $key=> $value){
                            if (count($value->find('.adv-subheadings',0) )) {
                               if (count($value->find('span',1) )) {
                                  $contact[trim($value->find('.adv-subheadings',0)->plaintext)]=$value->find('span',1)->plaintext;
                               }
                               if (count($value->find('a',0) )) {
                                  $contact[trim($value->find('.adv-subheadings',0)->plaintext)]=$value->find('a',0)->plaintext;
                               }
                             }  
                        }
                    } 
                  /*  
                    if($html->innertext!='' and count($html->find('a'))) {
                     foreach($html->find('a') as $a){
                   echo $a.'<br>';
                   echo $a->plaintext.'<br>';
                   echo $a->href.'<br>';
                     }
                    }*/
                       
         /*      echo      $html->find('.view .contact-info .form-line .adv-point a',1)->plaintext;
                 echo      $html->find('.view .contact-info .form-line .adv-point a',0)->plaintext;
                    */
                     if (count($html->find('.view #ad_placement',0) )) {
                        if (count($html->find('.view #ad_placement',0) )) {
                          $related_cid=$html->find('.view #ad_placement #related_cid',0)->plaintext; 
                        }
                        
                        foreach ($html->find('.view #ad_placement .viewStaticBlock .form-line-static') as $key=> $value){
                              if (count($value->find('.adv-subheadings',0) )) {
                                 $dt[$value->find('.adv-subheadings',0)->plaintext]=$value->find('.adv-inside',0)->plaintext;
                              }

                        }    
                     }
                     
                     if (count($html->find('.form-line-static #adv_id',0) )) {
                        $adv_id=$html->find('.form-line-static #adv_id',0)->plaintext;
                     }
            
             }
            // echo $name.'<br>';
           //  echo $company.'<br>';
            // echo $price.'<br>';
             
        //     print_r($details);
        //     echo '<br>';
        /*    print('<pre>');
                print_r($content);    
               print('</pre>');*/
                         /*  print('<pre>');
                print_r($contact);    
               print('</pre>');*/
           // echo $adv_id.'<br>';   
                             /*         print('<pre>');
                print_r($dt);    
               print('</pre>');    */
               
               $data['name']=$name;
               $data['company']=$company;
              
              if(trim($price)=='з/п не указана'){
                $data['price']=0;
                $data['from_salary']=0;
              }else{
                $arr_sal = explode('до',$price);
                if(isset($arr_sal[0])){
                $data['price']=preg_replace("/[^0-9]/,.", '', trim($arr_sal[0]));
                }else{
                 $data['price']=0;   
                }
                $data['from_salary']=1;
                
              }
              
               
               $data['city']=$details['Город:'];
               $data['employment']=$details['Занятость:'];
               $data['experience']=$details['Опыт работы:'];
               
               $descr='';
               
        if(isset($content['Обязанности'])){        
               $descr  .='<h3>Обязанности</h3> '.$content['Обязанности'];
        }       
        if(isset($content['Требования'])){         
               $descr .='<h3>Требования</h3> '.$content['Требования'];
        }
        if(isset($content['Условия работы'])){       
               $descr .='<h3>Условия работы</h3> '.$content['Условия работы'];
        }       
        if(isset($content['Другая информация'])){  
               $descr .='<h3>Другая информация</h3> '.$content['Другая информация'];
        }         
                $data['descr']=$descr;
            
              $data['user_name']=$contact['Контактное лицо:'];
        if(isset($contact['Телефон:'])){     
              $data['phone']=$contact['Телефон:'];
        }else{
            $data['phone']='';
        }
    /*   if(isset($contact['Email:'])) {      
              $data['email']=$contact['Email:'];  
       } else{*/
              $eml=(string)$this->searchEmail($info['url']);
              $newText = preg_replace_callback(
                '/&#x([a-f0-9]+)/mi', 
                function ($m) {
                    return chr(hexdec($m[1]));
                },
                $eml
            );
              $data['email']= str_replace(';','',$newText);
              $data['adv_id']=$adv_id;
              $data['categoryid']=$this->categoryid;
              
              $date = JFactory::getDate();
           //   echo $date.'<br>';

  /*  print('<pre>');
                print_r($dt);    
               print('</pre>');  */

                $arr_date = explode(' ',$dt['Дата обновления']);
                if(isset($arr_date['2'])){ 
                    $y=$arr_date['2'];
                    $m=$this->mont($arr_date['1']);
                    $d=$arr_date['0'];
                    $h=$arr_date['3'];
                    $dats=$y.'-'.$m.'-'.$d.' '.$h;     
                    $data['dt_update']=date("Y-m-d H:i:s", strtotime($dats));    
                }else{
                    $data['dt_update']=date('Y-m-d', strtotime('now'));
                }    
               
                $arr_dates = explode(' ',$dt['Активно до']);
             if(isset($arr_dates['2'])){ 
                    $y=$arr_dates['2'];
                    $m=$this->mont($arr_dates['1']);
                    $d=$arr_dates['0'];
                    $h=$arr_dates['3'];
                    $dats=$y.'-'.$m.'-'.$d.' '.$h;
                    $data['dt_activation']= date("Y-m-d H:i:s", strtotime($dats));    
              }else{
                    $data['dt_activation']=JFactory::getDate('+1 year ' . date('Y-m-d', strtotime('now')));
              } 
            $data['type']=1;

           $html->__destruct();  
           
           $tr=$model->ParserRabInsert($data);
       
      // print_r($tr);
       
        }
        return;
    }   
    
public function utf8RawUrlDecode ($source) { 
    $decodedStr = ''; 
    $pos = 0; 
    $len = strlen ($source); 
    while ($pos < $len) { 
        $charAt = substr ($source, $pos, 1); 
        if ($charAt == '%') { 
            $pos++; 
            $charAt = substr ($source, $pos, 1); 
            if ($charAt == 'u') { 
                // we got a unicode character 
                $pos++; 
                $unicodeHexVal = substr ($source, $pos, 4); 
                $unicode = hexdec ($unicodeHexVal); 
                $entity = "&#". $unicode . ';'; 
                $decodedStr .= utf8_Encode ($entity); 
                $decodedStr .= chr($unicode-848); 
                $pos += 4; 
            } 
            else { 
                // we have an escaped ascii character 
                $hexVal = substr ($source, $pos, 2); 
                $decodedStr .= chr (hexdec ($hexVal)); 
                $pos += 2; 
            } 
        } 
        else { 
            $decodedStr .= $charAt; 
            $pos++; 
        } 
    } 
    return $decodedStr; 
} 

    
    public function mont($ms){
        
        $Month_r = array( 
        "01" => "января", 
        "02" => "февраля", 
        "03" => "марта", 
        "04" => "апреля", 
        "05" => "майя", 
        "06" => "июня", 
        "07" => "июля", 
        "08" => "августа", 
        "09" => "сентября", 
        "10" => "октября", 
        "11" => "ноября", 
        "12" => "декабря"); 
        
        
          foreach($Month_r as $key=> $val){
                // echo $key .'=>'. $val;
                 if($val==$ms){
                    return $key;
                 }
                 
                 
          }
        
       return date('m'); 
    }
    public function searchEmail($url){
        if(isset($this->urlsJobList)){
            foreach($this->urlsJobList as $val){
                if ($val['href']==$url){
                    return $val['email'];
                }
            }
            
        }
        
        
    }
    
    
  //224bel  
    
    public function callback_praca_listurl($response, $info, $request)
    {
        //https://praca.by/search/vacancies/?search[query]=&search[specialities][1134]=1
        if($info['http_code']!==200)
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }else
        {
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);
           // echo $html;
            if (count($html->find('.main-content-wrapper ul li',0) )) {
                foreach ($html->find('.main-content-wrapper ul li.catalogue__item') as $key=> $value){
                    $cnt=0;
                    if(count($value->find('.catalogue__item__count',0))){
                      $cnt=(int)$value->find('.catalogue__item__count',0)->plaintext;
                    }   
                  if($cnt>0){   
                     if(count($value->find('a',0))){
                         //echo $cnt.' --> '.$value->find('a',0)->plaintext.' ==== '.$value->find('a',0)->href.'<br>';
                         $this->urlsspecial[]=$value->find('a',0)->href;
                     }   
                  }  
              }
                
            }

           $html->__destruct();  
        }
    }
    
    public function delurlsspecial($url){
        
       if(isset($this->urlsspecial)){
        foreach($this->urlsspecial as $key => $val){
            
            if(trim($url)==$val){
               // echo trim($url).'===>'.$val.'<br>';
                unset($this->urlsspecial[$key]);
            }
        }
        
       } 
        
        
    }
    
        public function callback_praca_page($response, $info, $request)
    {
        //https://praca.by/search/vacancies/?search[query]=&search[specialities][1134]=1
        if($info['http_code']!==200)
        {
            //echo $info['url'].'<br>';
            //AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }else
        {
            //echo $info['url'].'<br>';
            $this->delurlsspecial($info['url']);
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);

            if (count($html->find('.vacancy-catalogue-list .table-ajax-control .pagination',0) )) {
            
               $el=$html->find('.vacancy-catalogue-list .table-ajax-control .pagination ul',0)->last_child()->innertext;

               $hta=$html->load($el);
                if (count($hta->find('a',0) )) {
                     // echo  $hta->find('a',0)->href.'<br>';
                      
                       $page=(int)str_replace('?page=','',$hta->find('a',0)->href);    ///stristr($hta->find('a',0)->href, '&', true); 
                       // $page=(int)str_replace('?page=','',$page);
                        if($page>0){
                            for ($i=1; $i<=$page; $i++){
                                $this->urlsPage[]=$info['url'].'?page='.$i;
                               // echo $info['url'].'&page='.$i.'<br>';
                            }
                        }
                      $hta->__destruct();  
                }      
             }else{
              $this->urlsPage[]=$info['url'].'?page=1';  
             }

           $html->__destruct();  
        }
    }
    
     public function delurlsPage($url){
       if(isset($this->urlsPage)){
            foreach($this->urlsPage as $key => $val){
                if(trim($url)==$val){
                    unset($this->urlsPage[$key]);
                }
            }
       }  
    }
    
    public function callback_praca_list($response, $info, $request)
    {
        if($info['http_code']!==200)
        {
            //./*$request->options[CURLOPT_PROXY]*/
        //    AngryCurl::add_debug_msg("->\t"  ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
       //  echo  "\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url'];
        }else
        {
            
            $this->delurlsPage($info['url']);
            
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tOK\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
            $html = str_get_html($response);
             if (count($html->find('.list',0) )) {
                   foreach ($html->find('.list li') as $key=> $value){
                      if(count($value->find('.relevance',0))){
                         if(count($value->find('.list-row-title a',0))){
                            $href=$value->find('.list-row-title a',0)->href;
                         }
                       /*  if(count($value->find('.list-row-title a',0))){
                            
                         }*/
                         $model = $this->getModel();
                         $model->AddParserUrl($href);
                          /* $this->urlsJobList[]=array(
                             'href'  =>$href
                           );*/
                      }
                   }    
             }
            // exit();
           $html->__destruct();  
        }
        return;
    }
    
   
    public function delurlsJobList($url){
       if(isset($this->urlsJobList)){
            foreach($this->urlsJobList as $key => $val){
                if(trim($url)==$val){
                    unset($this->urlsJobList[$key]);
                }
            }
       }  
    } 
    
    public function callback_praca_job($response, $info, $request)
    {
        if($info['http_code']!==200)
        {
             echo  "\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url'];
           // AngryCurl::add_debug_msg("->\t" .$request->options[CURLOPT_PROXY] ."\tFAILED\t" .$info['http_code'] ."\t" .$info['total_time'] ."\t" .$info['url']);
        }else
        { 
           // $this->delurlsJobList($info['url']);
           // echo $info['url'].'<br>';
             $model = $this->getModel();

     $model->DellParserUrl($info['url']);  
           
           
              $html = str_get_html($response);
              
              if (count($html->find('.employer-organization-info .organization-name a',0) )) {
                $company_name= $html->find('.employer-organization-info .organization-name a',0)->plaintext.'<br>';
              }
              if (count($html->find('.vacancy-view',0) )) {
                
                if (count($html->find('.vacancy-view .vacancy-status-and-title h1',0) )) {
                   $name=$html->find('.vacancy-view .vacancy-status-and-title h1',0)->plaintext.'<br>';
                }    
                
                if (count($html->find('.vacancy-view .salary',0) )) {
                   $price=$html->find('.vacancy-view .salary',0)->plaintext.'<br>';
                }        
                
                
                if (count($html->find('.vacancy-view .common-info',0) )) {
                    $info=$html->find('.vacancy-view .common-info',0)->innertext;
                    $idjob=stristr($info, '|', true);
                    $idjob=preg_replace("/[^0-9]/", '', $idjob).'<br>'; 
                    
                   if (count($html->find('.vacancy-view .common-info span.dotted',0) )) {   
                    
                         $jobdate=(string)$html->find('.vacancy-view .common-info span.dotted',0)->plaintext;
                         $jobdate=stristr($jobdate, 'Создана', true);
                         $jobdate=str_replace('Обновлена','',$jobdate);  
                         $arr_date = explode(' ',trim($jobdate));
                        // print_r($arr_date);
                         if(isset($arr_date['2'])){ 
                            $y=$arr_date['2'];
                            $m=$this->mont($arr_date['1']);
                            $d=$arr_date['0'];
                            $h=$arr_date['4'];
                            $dats=$y.'-'.$m.'-'.$d.' '.$h;     
                            $data['dt_update']=date("Y-m-d H:i:s", strtotime($dats));    
                        }else{
                            $data['dt_update']=date('Y-m-d', strtotime('now'));
                        }   
                    }
                } 
                
                
                if (count($html->find('.vacancy-view .vacancy-required .terms-list',0) )) {
                    //foreach ($html->find('.vacancy-view .vacancy-required .terms-list li') as $key=> $value){
                   
                         if(count($html->find('.vacancy-view .vacancy-required .terms-list .city',0))){
                             $city=$html->find('.vacancy-view .vacancy-required .terms-list .city .terms-list-item-description',0)->plaintext.'<br>'; 
                         }
                         if(count($html->find('.vacancy-view .vacancy-required .terms-list .nature',0))){
                             $nature=$html->find('.vacancy-view .vacancy-required .terms-list .nature .terms-list-item-description',0)->plaintext.'<br>'; 
                         }
                         if(count($html->find('.vacancy-view .vacancy-required .terms-list .schedule',0))){
                             $schedule=$html->find('.vacancy-view .vacancy-required .terms-list .schedule .terms-list-item-description',0)->plaintext.'<br>'; 
                         }
                         if(count($html->find('.vacancy-view .vacancy-required .terms-list .occupation',0))){
                             $occupation=$html->find('.vacancy-view .vacancy-required .terms-list .occupation .terms-list-item-description',0)->plaintext.'<br>'; 
                         }
                         
                        
                   // }
               
                }
                 
                  if (count($html->find('.vacancy-view .vacancy-required .terms-icons',0) )) {

                         if(count($html->find('.vacancy-view .vacancy-required .terms-icons .experience',0))){
                             $experience=$html->find('.vacancy-view .vacancy-required .terms-icons .experience',0)->plaintext.'<br>'; 
                         }
                         if(count($html->find('.vacancy-view .vacancy-required .terms-icons .education',0))){
                             $education=$html->find('.vacancy-view .vacancy-required .terms-icons .education',0)->plaintext.'<br>'; 
                         }
                         if(count($html->find('.vacancy-view .vacancy-required .terms-icons .yes-student',0))){
                             $yes_student=$html->find('.vacancy-view .vacancy-required .terms-icons .yes-student',0)->plaintext.'<br>'; 
                         }

                  }  
                 if (count($html->find('.vacancy-view .description',0) )) {
                    $description=$html->find('.vacancy-view .description',0)->innertext.'<br>'; 
                 } 
                 if (count($html->find('.vacancy-view .job-address',0) )) {
                    //Место работы
                    $job_address=$html->find('.vacancy-view .job-address',0)->innertext.'<br>'; 
                 }    
                 
                 $contact=array();//Skype:
                 if (count($html->find('.vacancy-view .footer-org-contacts',0) )) {  
                         foreach ($html->find('.vacancy-view .footer-org-contacts p') as $key=> $value){
                           if (count($value->find('span',0) )) {  
                              $contact[trim($value->find('span',0)->plaintext)]=str_replace(trim($value->find('span',0)->plaintext),'',trim($value->plaintext));
                            }
                         }
                      
                  } 
                  
                  
              } 

              
              //$nature
     
            $data['name']=trim($name);
            $data['company']=trim($company_name);
            
            if(isset($price)){
              $data['price']=preg_replace("/[^0-9]/,.", '', trim($price));
            }else{
              $data['price']=0;  
            }
            $data['from_salary']=0;
            if(isset($city)){
            $data['city']=$city;
            }
            if(isset($occupation)){
            $data['employment']=$occupation;
            }
            if(isset($schedule)){
            $data['schedule_field']=$schedule;
            }
            if(isset($education)){
            $data['education']=$education;
            }
            if(isset($experience)){
            $data['experience']=preg_replace("/[^0-9]/", '', $experience);
            }
            if(isset($yes_student)){
            $data['student']=$yes_student;
            }
             
           $descr='';
           
           if (isset($description)){
            $descr .=$description;
           } 
            if(isset($job_address)){
                $descr  .='<b>Место работы</b> '.$job_address;
            }
             
            
            $data['descr']=$descr;
            
            if(isset($contact['Контактное лицо:'])){
                $data['user_name']=trim($contact['Контактное лицо:']);
            }else{
                $data['user_name']=trim($company_name);
            }
            
           
            if(isset($contact['Номера телефонов:'])){
               $data['phone']=trim($contact['Номера телефонов:']); 
            }else{
               if(isset($contact['Skype:'])){ 
                 $data['phone']=$contact['Skype:'];
               }else{
                $data['phone']='';
               }
               
            }
           if(isset($contact['Электронная почта:'])){
               $data['email']=trim($contact['Электронная почта:']);
            }else{
               $data['email']='';
            }
            
            
  // print_r($data);
    
            $data['adv_id']=$idjob; 
            $data['categoryid']=$this->categoryid;
            $data['dt_activation']=JFactory::getDate('+1 year ' . date('Y-m-d', strtotime('now')));
            $data['type']=3;              



     
     
        if (!empty($data['email']) || !empty($data['phone']) ){
           $tr=$model->ParserRabInsert($data);
        }
        
       
        
        
               $html->__destruct();  
        }
        return;
    }
    
    //парсер вак 224 бел
    public function ajaxTets(){
        
        	$input = JFactory::getApplication()->input;
            //        $arr1=str_replace('&quot;','"',$this->request->post['boxes']);
            // $arr1=(array)json_decode($arr1, true);
           
          //   $arr1 = $input->post->get('jsondt');
          $arr1= $input->getVar('jsondt','');

            $arr1=(array)json_decode($arr1, true);
                     print_r($arr1);
                     $data=array();
                 

    //[DateMake] => 2016-05-03T14:16:00



   

                     
            $data['name']=$arr1['Spez'];
            $data['company']=$arr1['ContrAgent'];
            
            if($arr1['ZP']!='-'){
              $data['price']=preg_replace("/[^0-9]/,.", '', trim($arr1['ZP']));
            }else{
              $data['price']=0;  
            }
            $data['from_salary']=0;
            $data['city']=$arr1['City'];
            $data['employment']='';
            $data['experience']='';
             
           $descr='';
           /*  $descr  .='<h3>Обязанности</h3> '.$arr1['Обязанности'];*/
           if($arr1['Type']!='-'){   
           $descr  .=$arr1['Type'];
           }
           if($arr1['Sphere']!='-' && !empty($arr1['Sphere'])){   
             $descr  .='<h3>Cфера деятельности</h3> '.$arr1['Sphere'];
           } 
           
           if($arr1['Treb']!='-'){   
             $descr  .='<h3>Требования</h3> '.$arr1['Treb'];
           } 
          
           
           if($arr1['Skill']!='-'){  
             $descr  .='<h3>Навыки</h3> '.$arr1['Skill'];
           }  
             $descr  .='<h3>Описание</h3> '.$arr1['Conditions'];
            
            $data['descr']=$descr;
            $data['user_name']=$arr1['Operator'];
            $data['phone']=$arr1['Telephone'];
            if($arr1['Email']!='-'){  
              $data['email']=$arr1['Email'];
            }else{
              $data['email']='';
            }
            $data['adv_id']=$arr1['Key'];
            $data['categoryid']=0;
            $data['dt_update']=date('Y-m-d', strtotime($arr1['DateMake']));
            $data['dt_activation']=JFactory::getDate('+1 year ' . date('Y-m-d', strtotime('now')));
            $data['type']=2;
 
    $model = $this->getModel();
   $tr=$model->ParserRabInsert($data);
  /*   $job=array();
            foreach($arr1 as $key => $val){
               $job[]=array( 
                    'City'      =>$val['City'],
                    'Conditions'=>$val['Conditions'],
                    'ContrAgent'=>$val['ContrAgent'],
                    'DateMake'  =>$val['DateMake'],
                    'Email'     =>$val['Email'],
                    'Key'       =>$val['Key'],
                    'Operator'  =>$val['Operator'],
                    'Skill'     =>$val['Skill'],
                    'Spez'      =>$val['Spez'],
                    'Sphere'    =>$val['Sphere'],
                    'Telephone' =>$val['Telephone'],
                    'Treb'      =>$val['Treb'],
                    'Type'      =>$val['Type'],
                    'ZP'        =>$val['ZP']
               ); 
            }

              
         echo json_encode($arr1);*/
        exit();
    }
	//protected $context = 'com_parser.load';
    
    
    /*	public function getModel($name = 'Parser', $prefix = 'ParserModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}*/
    
    
		public function belparser($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$vName		= 'load';
		$vFormat	= 'belparser';
        
         $document = JFactory::getDocument();
       //     $viewType = $document->getType();
       //     $viewName = JRequest::getCmd('view', $this->getName());
//echo $viewType.' '.$viewName;

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{

	    	$view->display('belparser');
        }
        
        /*
        
         $view = $this->getView($viewName, '', $viewType);       
                   
            $id = $user->id;
                     
            if (!$id) {
                echo $view->get('error', JText::_('COM_COMMUNITY_USER_NOT_FOUND'));
            } else {
                echo $view->get(__FUNCTION__);
            }
        */
        	

	}
    
	public function pracatpl($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$vName		= 'load';
		$vFormat	= 'pracatpl';
        
         $document = JFactory::getDocument();
        
        $model = $this->getModel($vName);

		if ($view = $this->getView($vName, $vFormat))
		{
		  
            $view->setModel($model, true);
            
	    	$view->display(__FUNCTION__);
        }
        

	} 
    
	public function gszstpl($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$vName		= 'load';
		$vFormat	= 'gszstpl';
        
         $document = JFactory::getDocument();
        
        $model = $this->getModel($vName);

		if ($view = $this->getView($vName, $vFormat))
		{
		  
            $view->setModel($model, true);
            
	    	$view->display(__FUNCTION__);
        }
        

	} 
    

   
}



