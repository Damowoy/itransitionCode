<?php
defined('_JEXEC') or die();
    
class CommunityResumeController extends CommunityBaseController {
   
   
     public function AjaxSearchResume(){
           
            $mainframe    = JFactory::getApplication();
            $jinput       = $mainframe->input;
            $my           = CFactory::getUser();
            $config       = CFactory::getConfig();


            $search = JRequest::get('REQUEST');
        
        
        $clientModel = CFactory::getModel('vacancies');
            


            
            $results = $clientModel->GetSearchClient($search);
            $result_search=array();
            
            foreach ($results as $value){
                
                 if ($value['gender']=="male"){
                        $gender="Мужской";
                  }else{
                        $gender="Женский";
                  }
          
                  $client_user  = CFactory::getUser($value['user_id']); 
                 
                  $result_search[] =array(   
                      'user_id'  =>$value['user_id'],
                      'firstname'  =>$value['firstname'],
                      'lastname'   =>$value['lastname'],
                      'fathername' =>$value['fathername'],
                      'birthdate'  =>$value['birthdate'],
                      'gender'     =>$gender, 
                      'sity'       =>$value['city'], 
                      'move'       =>$value['move'], 
                      'about'      =>$value['about'], 
                      'phone'      =>$value['phone'], 
                      'email'      =>$value['email'],
                      'about'      =>$value['about'], 
                      'to_travel'  =>$value['to_travel'],
                      'office_name'=>$value['office_name'],
                      'key_skills' =>$value['key_skills'],
                      'full_years' =>$value['full_years'],
                      'status_job' =>$value['status_job'],
                      'status_name'=>$value['status_name'],
                      'avatar'     =>$client_user->getThumbAvatar(),
                      'profile_href'=> CRoute::_('index.php?option=com_community&view=profile&task=editabout&userid=' . $value['user_id'])
                  );
                
                
            }
       

            $tmpl = new CTemplate();        
            echo  $tmpl
                    ->set('results',$result_search)
                    ->fetch('resume.ajax.index');
        
        exit(); 
     }

      public function display($cacheable = false, $urlparams = false) {
           $document = JFactory::getDocument();
           $document->setTitle('Резюме');
           $this->resume();

     }
     
     
     public function resume() {
          
            $user = CFactory::getUser();
    
            $document = JFactory::getDocument();
            $viewType = $document->getType();
            $viewName = JRequest::getCmd('view', $this->getName());
                   
            $lang = JFactory::getLanguage();
            $lang->load(COM_USER_NAME);
    
            $view = $this->getView($viewName, '', $viewType);       
                   
            $id = $user->id;
                     
          /*  if (!$id) {
                echo $view->get('error', JText::_('COM_COMMUNITY_USER_NOT_FOUND'));
            } else {}*/
                echo $view->get(__FUNCTION__);
            
          
          
          
     }
       
     
      public function rdate($param, $time = 0)
	{/*
"d M Y";
 в H:i:s"*/
		if (intval($time) == 0)
			$time = time();
		$MonthNames = array(
            "янв",
            "фев",
            "мар",
            "апр",
            "мая",
            "июн",
            "июл",
            "авг",
            "сен",
            "окт",
            "ноя",
            "дек"
		);
		if (strpos($param, 'M') === false)
			return date($param, $time);
		else {
			$str_begin  = date(mb_substr($param, 0, mb_strpos($param, 'M')), $time);
			$str_middle = $MonthNames[date('n', $time) - 1];
			$str_end    = date(mb_substr($param, mb_strpos($param, 'M') + 1, mb_strlen($param)), $time);
			$str_date   = $str_begin . $str_middle . $str_end;
			return $str_date;
		}
	}
        
        
   public function _textRever($str){       
            $str_result='';
            switch ($str) {
                case 'no-matter':
                    $str_result= "Не имеет значения";
                    break;
                case 'prof-tech':
                    $str_result= "Профессионально-техническое образование";
                    break;
                case 'secondary-specialized':
                    $str_result= "Среднее специальное образование";
                    break;
                case 'part-high':
                    $str_result= "Незаконченное высшее";
                    break;
                case 'high':
                    $str_result= "Высшее образование";
                    break;
                case 'from-1':
                    $str_result= "От 1 года";
                    break;    
                case 'from-2':
                    $str_result= "От 2 лет";
                    break;  
                case 'from-3':
                    $str_result= "От 3 лет";
                    break;    
                case 'from-4':
                    $str_result= "От 4 лет";
                    break; 
               case 'from-5':
                    $str_result= "От 5 лет";
                    break;    
                case 'yes':
                    $str_result= "Студент";
                    break;  
                case 'no':
                    $str_result= "Не студент";
                    break;                               
            }
           return $str_result; 
       }     
       
       
   public function  ajaxResumeTab($idPos,$field_search){

        $response = new JAXResponse();
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
          $model = $this->getModel('search');
         // $results = $model->GetSearchClientTop($field_search);
          $result_resume=array();
                $results = $model->GetSearchResumeTop($field_search);
                 foreach ($results as $value){
                  $client_user  = CFactory::getUser($value['user_id']); 
                  $result_resume[] =array(   
                      'user_id'    =>$value['user_id'],
                      'firstname'  =>$value['firstname'],
                      'lastname'   =>$value['lastname'],
                      'fathername' =>$value['fathername'],
                      'birthdate'  =>$value['birthdate'], 
                      'briefly'    =>$value['briefly'],
                      'office_name'=>$value['office_name'],
                      'avatar'     =>$client_user->getThumbAvatar(),
                      'profile_href'=> CRoute::_('index.php?option=com_community&view=profile&task=editabout&userid=' . $value['user_id'])
                  );
                
                
            }  
            
            $tmpl = new CTemplate();
          //  set('showFeatured', '')
            $html=$tmpl->set('result_resume',$result_resume)
                       ->set('field_search',$field_search)
                       ->fetch('search_resume.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   }
   public function  ajaxProfileTab($idPos,$field_search,$limit){

        $response = new JAXResponse();   
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
        
          $model = $this->getModel('search');
          $result_search=array();
          $results = $model->GetSearchClientTop($field_search,$limit);
          foreach ($results as $value){
                  $client_user  = CFactory::getUser($value['user_id']); 
                  $result_search[] =array(   
                      'user_id'    =>$value['user_id'],
                      'firstname'  =>$value['firstname'],
                      'lastname'   =>$value['lastname'],
                      'fathername' =>$value['fathername'],
                      'birthdate'  =>$value['birthdate'], 
                      'briefly'    =>$value['briefly'],
                      'office_name'=>$value['office_name'],
                      'avatar'     =>$client_user->getThumbAvatar(),
                      'profile_href'=> CRoute::_('index.php?option=com_community&view=profile&userid=' . $value['user_id'])
                  );
                
                
            }
            
            $tmpl = new CTemplate();
          //  set('showFeatured', '')
            $html=$tmpl->set('results',$result_search)
                       ->set('field_search',$field_search)
                       ->fetch('search_profile_pipel.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   } 
   public function  ajaxJobTab($idPos,$field_search){

        $response = new JAXResponse();
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
          $modelcompany= CFactory::getModel('company');
          $modelvacancies = CFactory::getModel('vacancies');
          $model = $this->getModel('search');
            
            $data = $model->GetSearchVacanciesTop($field_search);
                 
            $vacancies=array();

           foreach($data as $key => $value) {  
            
              $obj = JTable::getInstance('Vacancies', 'CTable');
              $obj->load($value['job_vacancies_id']);
              if(!empty($obj->salary)){
                $obj->salary=number_format( $obj->salary, 0, ' ', ' ').'руб.';
              }else{
                $obj->salary= "з/п не указана";
              }
              $obj->experience=$this->_textRever('from-'.$obj->experience);
              $obj->student=$this->_textRever($obj->student);
              $obj->education=$this->_textRever($obj->education);
              $obj->action_show=  CRoute::_('index.php?option=com_community&view=vacancies&task=preview&vacancies_id='.$obj->job_vacancies_id.'&type='.$obj->view_jobs.'');   
              $obj->cityname=$modelcompany->getCity($obj->city);   
              $obj->profarea_name=$modelcompany->getCategory($obj->profarea_id);
           //   $obj->companys=$obj->GetCompany();
              $company = JTable::getInstance('Company', 'CTable');
              $company->load($obj->company_id);
              if(empty($company->id)){
          		 $obj->companys			= new stdClass();
                 $profile         = CFactory::getUser($obj->user_id);
                 $obj->companys->avatar=$profile->getThumbAvatar(); 
                 $obj->companys->name=$obj->company_name;
              }else{
                $obj->companys			= new stdClass();
                $obj->companys->avatar=$company->getAvatar( 'avatar' ) . '?_=' . time();
                $obj->companys->name=$company->name;
              }
              $obj->CountLike=$modelvacancies->GetTotalLike($obj->job_vacancies_id);
              $obj->CountReviews=$modelvacancies->GetTotalReviews($obj->job_vacancies_id);
              $vacancies[]=$obj;

           }
            
            $tmpl = new CTemplate();
          //  set('showFeatured', '')
            $html=$tmpl->set('vacancies',$vacancies)
                       ->set('field_search',$field_search)
                       ->fetch('search_job.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   }   
   
   
   public function  ajaxCompanyTabTab($idPos,$field_search){

        $response = new JAXResponse();
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
          $modelcompany= CFactory::getModel('company');
          $modelvacancies = CFactory::getModel('vacancies');
          $model = $this->getModel('search');
            
           // $data = $model->GetSearchVacanciesTop($field_search);
                 
            $vacancies=array();
        
           $company = $modelcompany->getAllGroups(0, 'alphabetical',$field_search);
           $companyhtml=$this->getCompanyHTML($company);

            
            $tmpl = new CTemplate(); 
          //  set('showFeatured', '')
            $html=$tmpl->set('companyhtml',$companyhtml)
                       ->set('field_search',$field_search)
                       ->fetch('search_comapny.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   }   
   
   public function  ajaxGroupTabTab($idPos,$field_search){

        $response = new JAXResponse();
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
          $modelcompany= CFactory::getModel('company');
          $modelvacancies = CFactory::getModel('vacancies');
          $model = $this->getModel('search');
                        $modelgroups = CFactory::getModel('groups');
                $modelevents = CFactory::getModel('events');
                    
            //$data = $model->GetSearchVacanciesTop($field_search);
                 
            $vacancies=array();
        
         $group=$modelgroups->getAllGroups(0, 'alphabetical',$field_search);
        
          $grouphtml=$this->getGroupsHTML($group);

            
            $tmpl = new CTemplate(); 
          //  set('showFeatured', '')
            $html=$tmpl->set('grouphtml',$grouphtml)
                       ->set('field_search',$field_search)
                       ->fetch('search_group.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   }    
   
   public function  ajaxEventTabTab($idPos,$field_search){

        $response = new JAXResponse();
        $my = CFactory::getUser();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
          $modelcompany= CFactory::getModel('company');
          $modelvacancies = CFactory::getModel('vacancies');
          $model = $this->getModel('search');
                        $modelgroups = CFactory::getModel('groups');
                $modelevents = CFactory::getModel('events');
                    
           // $data = $model->GetSearchVacanciesTop($field_search);
                 

        
         $events = $modelevents->getEvents(0, null, 'alphabetical', $field_search);
         
        $eventshtml=$this->getEvaentsHTML($events);

            
            $tmpl = new CTemplate(); 
          //  set('showFeatured', '')
            $html=$tmpl->set('eventshtml',$eventshtml)
                       ->set('field_search',$field_search)
                       ->fetch('search_event.tpl');


         $response->addScriptCall('__callback',$html);
         return $response->sendResponse();  
    
   }    
       public function getCompanyCategories($category) {

            $model = CFactory::getModel('company');
            $categories = $model->getCategoriesCount();

            $categories = CCategoryHelper::getParentCount($categories, $category);

            return $categories;
        }
    public function getEvaentsHTML($eventsR,$isExpired = false, $pagination = NULL) {
        $events = array();
          

            
            $config = CFactory::getConfig();
            $format = ($config->get('eventshowampm')) ? JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_12H') : JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_24H');


    
             if ($eventsR) {
                foreach ($eventsR as $row) {
                    $event = JTable::getInstance('Event', 'CTable');
                    $event->bind($row);
                    $events[] = $event;
                }
                unset($eventsR);
            }
         
         $featured = new CFeatured(FEATURED_EVENTS);
            $featuredList = $featured->getItemIds();  
           
            $tmpl = new CTemplate();
            return $tmpl->set('showFeatured', $config->get('show_featured'))
                            ->set('featuredList', $featuredList)
                            ->set('isCommunityAdmin', COwnerHelper::isCommunityAdmin())
                            ->set('events', $events)
                            ->set('isExpired', $isExpired)
                            ->set('pagination', $pagination)
                            ->set('timeFormat', $format)
                            ->fetch('eventspastevents.list');
    }
   
       public function getCompanyHTML($tmpGroups, $tmpPagination = NULL) {
            $config = CFactory::getConfig();
            $tmpl = new CTemplate();
            $featured = new CFeatured(FEATURED_COMPANY);
            $featuredList = $featured->getItemIds();
            
            $groupModel    = CFactory::getModel('company');
            $my = CFactory::getUser();
             
            $groups = array();
            if ($tmpGroups) {
                foreach ($tmpGroups as $row) {
                    $category    = JTable::getInstance('CompanyCategory', 'CTable');
                    
            
                    $group = JTable::getInstance('Company', 'CTable');
                    $group->bind($row);  
                    $group->updateStats(); //ensure that stats are up-to-date $config->get('tips_desc_length')
                    $group->description = CStringHelper::clean(JHTML::_('string.truncate', strip_tags($group->description) , '120')); 
                    $category->load($group->categoryid); 
                    $group->categoryname=$category->name;
                    $group->isMember = $groupModel->isMember($my->id, $group->id);
                    
                    $groups[] = $group;
                }
                unset($tmpGroups);
            }

            $groupsHTML = $tmpl->set('showFeatured', $config->get('show_featured'))
                    ->set('featuredList', $featuredList)
                    ->set('isCommunityAdmin', COwnerHelper::isCommunityAdmin())
                    ->set('groups', $groups)
                    ->set('pagination', $tmpPagination)
                    ->fetch('company.list');
            unset($tmpl);

            return $groupsHTML;
        }
        
             public function getGroupsHTML($tmpGroups, $tmpPagination = NULL) {
            $config = CFactory::getConfig();
            $tmpl = new CTemplate();
            $featured = new CFeatured(FEATURED_GROUPS);
            $featuredList = $featured->getItemIds();

            $groupModel    = CFactory::getModel('groups');
            $my = CFactory::getUser();
            
            $groups = array();

            if ($tmpGroups) {
                foreach ($tmpGroups as $row) {
                    $group = JTable::getInstance('Group', 'CTable');
                    $group->bind($row);
                    $group->updateStats(); //ensure that stats are up-to-date
                    $group->description = CStringHelper::clean(JHTML::_('string.truncate', $group->description, $config->get('tips_desc_length')));
                    $group->isMember = $groupModel->isMember($my->id, $group->id);
                    $groups[] = $group;
                }
                unset($tmpGroups);
            }

            $groupsHTML = $tmpl->set('showFeatured', $config->get('show_featured'))
                    ->set('featuredList', $featuredList)
                    ->set('isCommunityAdmin', COwnerHelper::isCommunityAdmin())
                    ->set('groups', $groups)
                    ->set('pagination', $tmpPagination)
                    ->fetch('groups.list');
            unset($tmpl);

            return $groupsHTML;
        }
   
   
}
    
    
?>