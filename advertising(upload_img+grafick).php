<?php
defined('_JEXEC') or die();
    
class CommunityAdvertisingController extends CommunityBaseController {
      

       public function AjaxUploadAds(){
      
         $json=array();
         jimport('joomla.filesystem.file');
         jimport('joomla.utilities.utility');
        
         $mainframe = JFactory::getApplication();
         $jinput = $mainframe->input;
          
          $data_get = JRequest::get('REQUEST');
              
          $fileFilter = new JInput($_FILES);
        
          $file1 = $fileFilter->get('fileUpload', '', 'array');
          $file2 = $fileFilter->get('fileUploadtop', '', 'array');
          $file3 = $fileFilter->get('fileUploadempty', '', 'array');
          
          if(isset($file1) && count($file1)>0 && isset($file1['tmp_name'][0])  ){
            $file=$file1;
          }
          if(isset($file2) && count($file2)>0 && isset($file2['tmp_name'][0])  ){
            $file=$file2;
          }
          if(isset($file3) && count($file3)>0 && isset($file3['tmp_name'][0])  ){
            $file=$file3;
          }       
          if (!isset($file['tmp_name'][0]) || empty($file['tmp_name'][0])) {
                   
              $json['error']=JText::_('COM_COMMUNITY_NO_POST_DATA');
              print  json_encode($json);
              exit();
           } else { 
                
                   if (!CImageHelper::isValid($file['tmp_name'][0])) {
                                             
                      $json['error']=JText::_('COM_COMMUNITY_IMAGE_FILE_NOT_SUPPORTED');
                      print  json_encode($json);
                      exit();
                   } else {
                    
                        $config = CFactory::getConfig();
                        $uploadLimit = (double) $config->get('maxuploadsize');
                        $uploadLimit = ( $uploadLimit * 1024 * 1024 );
                      
                        $fileName = JApplication::getHash($file['tmp_name'][0] . time());
                        $hashFileName = JString::substr($fileName, 0, 24);
                       
                        $storage = JPATH_ROOT . '/' . $config->getString('imagefolder') . '/basket';
                        $img=$hashFileName . CImageHelper::getExtension($file['type'][0]);
                        $storageImage = $storage . '/' .$img ;
             
                    if ($data_get['posit']=='right') {
                        
                          if (!CImageHelper::resizeProportional($file['tmp_name'][0], $storageImage, $file['type'][0], 300)) {  
                               $json['error']= JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE', $storageImage); 
                               print  json_encode($json);
                               exit();
                         }else{
                           $image_file=CImage::cropsize('images/basket/'.$img,'170','100','','ads_right');
                             if(file_exists('/images/basket/'.$img)) {
                               unlink('/images/basket/'.$img); 
                             }
                         }
                         
                   } elseif ($data_get['posit']=="right-empty"){
                    
                             
                         if (!CImageHelper::resizeProportional($file['tmp_name'][0], $storageImage, $file['type'][0], 300)) {
                               $json['error']= JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE', $storageImage); 
                               print  json_encode($json);
                               exit();
                         }else{
                           $image_file=CImage::cropsize('images/basket/'.$img,'150','170','','ads_empty');
                             if(file_exists('/images/basket/'.$img)) {
                               unlink('/images/basket/'.$img); 
                             }
                         }
                         
                   }else{
                         if (!CImageHelper::resizeProportional($file['tmp_name'][0], $storageImage, $file['type'][0], 1224)) {
                                
                               $json['error']= JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE', $storageImage); 
                               print  json_encode($json);
                               exit();
                         }else{
                             $image_file=CImage::cropsize('images/basket/'.$img,'1200','70','','ads_top');
                             if(file_exists('/images/basket/'.$img)) {
                               unlink('/images/basket/'.$img); 
                             }
                         }
                   }
                       $json['name']=$file['name'][0];
                       $json['image']=$image_file;
                       $json['image_href']=JURI::base( true ).'/'.$image_file;
                       $json['data']=$data_get;
                       $json['error']='';
               

                 
                }  
           }     
             print  json_encode($json);
         exit();
       }   
  
     
       
 
   
   
       public function AjaxChart(){
        
          $mainframe = JFactory::getApplication();
          $jinput = $mainframe->input; 
          $my = CFactory::getUser();
          $adsid     = $jinput->get('adsid', '', 'STRING');
          $range     = $jinput->get('range', 'month', 'STRING');
          $companyId = $jinput->get('adsid', '0', 'INT');
		  $model = $this->getModel('advertising');
          //month
          
		  $data = array();
		
		$data['order'] = array();
		$data['customer'] = array();
		$data['xaxis'] = array();
		
		$data['order']['label'] = 'Показы';
		$data['customer']['label'] = "Клики";
		
		switch ($range) {
			case 'day':
				for ($i = 0; $i < 24; $i++) {
				
                    
                   
				}					
				break;
			case 'week':
				$date_start = strtotime('-' . date('w') . ' days'); 
				
				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));
			        	$data['order']['data'][]  = array($i, rand(5, 15)*$i );
                	$data['customer']['data'][] = array($i, rand(5, 15)*$i);
					$data['xaxis'][] = array($i, date('D', strtotime($date)));
				}
				
				break;
			default:
			case 'month':
				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;
			  $db = JFactory::getDbo(); 
              $userId=$my->id; 
             if (empty($adsid)){
                        $query	= 'SELECT COUNT(*) AS total '
                            . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                            . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                            . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                            . ' WHERE  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND DATE(at.track_date)='. $date.' AND  track_type=1  GROUP BY DAY(at.track_date)';
                        $db->setQuery( $query );
                        $result	= $db->loadObject(); 
                       	if(is_null($result)) {
                       	    $view=0;
                       	}else{
                       	    $view=$result->total;
                       	}
                        
    
                        $query	= 'SELECT COUNT(*) AS total '
                                . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                                . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                                . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                                . ' WHERE  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND DATE(at.track_date)='.  $date .'  AND track_type=2  GROUP BY DAY(at.track_date)';
                            $db->setQuery( $query );
                            $result	= $db->loadObject(); 
                           	if(is_null($result)) {
                           	    $click=0;
                           	}else{
                           	    $click=$result->total;
                           	}
                 } else{
                         $query	= 'SELECT COUNT(*) AS total '
                            . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                            . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                            . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                            . ' WHERE at.'.$db->quoteName('advertising_id').'='.$db->Quote($adsid).'  AND  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND DATE(at.track_date)='.$date.'  AND track_type=1  GROUP BY DAY(at.track_date)';
                        $db->setQuery( $query );
                        $result	= $db->loadObject(); 
                       	if(is_null($result)) {
                       	    $view=0;
                       	}else{
                       	    $view=$result->total;
                       	}
                        
    
                        $query	= 'SELECT COUNT(*) AS total '
                                . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                                . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                                . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                                . ' WHERE at.'.$db->quoteName('advertising_id').'='.$db->Quote($adsid).'  AND c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND DATE(at.track_date)='.  $date .' AND track_type=2  GROUP BY DAY(at.track_date)';
                            $db->setQuery( $query );
                            $result	= $db->loadObject(); 
                           	if(is_null($result)) {
                           	    $click=0;
                           	}else{
                           	    $click=$result->total;
                           	}
                 }  
        
        
                       
                       // $view = $model->getAllShowView($date,$my->id);
                      //  $click =$model->getAllShowClic($date,$my->id);
                        
			       	$data['order']['data'][]  = array($i,   $view);
                	$data['customer']['data'][] = array($i, $click );
					$data['xaxis'][] = array($i, date('j', strtotime($date)));
				}
				break;
			case 'year':
				for ($i = 1; $i <= 12; $i++) {
                    $userId=$my->id; 
                   
                    $db = JFactory::getDbo();
                  //$companyId
                  if (empty($adsid)){
                        $query	= 'SELECT COUNT(*) AS total '
                            . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                            . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                            . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                            . ' WHERE  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND YEAR(at.track_date)='. date('Y').' AND MONTH(at.track_date) = ' . $i . ' AND track_type=1  GROUP BY DAY(at.track_date)';
                        $db->setQuery( $query );
                        $result	= $db->loadObject(); 
                       	if(is_null($result)) {
                       	    $view=0;
                       	}else{
                       	    $view=$result->total;
                       	}
                        
    
                        $query	= 'SELECT COUNT(*) AS total '
                                . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                                . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                                . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                                . ' WHERE  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND YEAR(at.track_date)='.  date('Y') .' AND MONTH(at.track_date) = ' . $i . ' AND track_type=2  GROUP BY DAY(at.track_date)';
                            $db->setQuery( $query );
                            $result	= $db->loadObject(); 
                           	if(is_null($result)) {
                           	    $click=0;
                           	}else{
                           	    $click=$result->total;
                           	}
                 } else{
                                        $query	= 'SELECT COUNT(*) AS total '
                            . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                            . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                            . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                            . ' WHERE at.'.$db->quoteName('advertising_id').'='.$db->Quote($adsid).'  AND  c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND YEAR(at.track_date)='. date('Y').' AND MONTH(at.track_date) = ' . $i . ' AND track_type=1  GROUP BY DAY(at.track_date)';
                        $db->setQuery( $query );
                        $result	= $db->loadObject(); 
                       	if(is_null($result)) {
                       	    $view=0;
                       	}else{
                       	    $view=$result->total;
                       	}
                        
    
                        $query	= 'SELECT COUNT(*) AS total '
                                . ' FROM ' . $db->quoteName('#__advertising_tracks') . 'at  '
                                . ' INNER JOIN '.  $db->quoteName('#__advertisings') . 'c  ON (at.'.$db->quoteName('advertising_id').'=c.'.$db->quoteName('id').')  '
                                . ' LEFT JOIN '.  $db->quoteName('#__advertisings_ads') . 'ads  ON (c.'.$db->quoteName('id').'=ads.'.$db->quoteName('advertisings_id').')  '
                                . ' WHERE at.'.$db->quoteName('advertising_id').'='.$db->Quote($adsid).'  AND c.' . $db->quoteName( 'user_id' ) . '=' . $db->Quote( $userId ).' AND YEAR(at.track_date)='.  date('Y') .' AND MONTH(at.track_date) = ' . $i . ' AND track_type=2  GROUP BY DAY(at.track_date)';
                            $db->setQuery( $query );
                            $result	= $db->loadObject(); 
                           	if(is_null($result)) {
                           	    $click=0;
                           	}else{
                           	    $click=$result->total;
                           	}
                 }  
                       // $view = $model->getAllShowView(date('Y'),$my->id);
                       // $click =$model->getAllShowClic(date('Y'),$my->id);
                        
			       	$data['order']['data'][]  = array($i,   $view);
                	$data['customer']['data'][] = array($i, $click );
					$data['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i, 1, date('Y'))));
				}			
				break;	
		} 
		
		 print json_encode($data);
         
         exit();
        
        
       }
   
   
           public function GetWekDay($x){
            
                switch ($x) {
            case 0:
                return "Пн";
                break;
            case 1:
                return "Вт";
                break;
            case 2:
                return "Ср";
                break;
            case 3:
                return "Чт";
                break;
            case 4:
                return "Пт";
                break;
            case 5:
                return "Су";
                break;
            case 6:
                return "Вс";
                break;
            }
        }
}
    
    
?>