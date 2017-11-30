<?php
	defined('_JEXEC') or die('Restricted access');
    require_once( JPATH_ROOT .'/components/com_community/models/models.php' );
 class CommunityModelVacancies extends JCCModel
 {
    var $_pagination;
    
      
    
     public function Insert($userId,$data){

    //   print_r($data); 
//vacancy_description
        $db		= $this->getDBO();
        $my           = CFactory::getUser();        
        
        
       $query	= ' SELECT  *'
                 .' FROM ' . $db->quoteName('#__community_client') . ' WHERE   '.$db->quoteName('user_id').'=' . $db->Quote($userId);  
                       
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();   

            $field		          = new stdClass();
            $field->client_id         = $result['id'];
            $field->user_id           = $my->id;
            $field->vacancy_name      = $data['vacancy']['vacancy_name'];
            $field->salary   	      = (float)str_replace(" ","",trim($data['vacancy']['salary']));
            $field->from_salary       =  isset($data['vacancy']['from_salary']) ? 1 : 0 ;
            $field->city    	      = $data['vacancy']['city'];
            if (isset($data['categoryid'])){
              $field->profarea_id    	  = $data['categoryid'];
            }
            if (isset($data['vacancy']['experience'])){
              $field->experience    	  = $data['vacancy']['experience'];
            }
            $field->student    	      = $data['vacancy']['student'];
            $field->education    	  = $data['vacancy']['education'];
            $field->language    	  =  json_encode($data['vacancy']['field_language']);

            $field->vacancy_description = trim($data['description']);
            $field->duties    	        = json_encode($data['vacancy']['field_duties']);  
            $field->demands    	        = json_encode($data['vacancy']['field_demands']);  
            if (isset($data['vacancy']['field_skills'])){ 
            $field->skills    	        = json_encode($data['vacancy']['field_skills']);
            }
            if(isset($data['schedule'])){
            $field->schedule    	    = json_encode($data['schedule']); 
            }
            if (isset($data['vacancy']['nature'])){
            $field->nature               =$data['vacancy']['nature'];
            }
            if (isset($data['vacancy']['schedule_field'])){
            $field->schedule_field       =$data['vacancy']['schedule_field'];
            }
            if (isset($data['vacancy']['employment'])){
            $field->employment           =$data['vacancy']['employment'];
            } 
            if (isset($data['vacancy']['from_employment_full']) && isset($data['vacancy']['to_employment_full'])){
            $field->from_employment_full = $data['vacancy']['from_employment_full'];
            $field->to_employment_full   = $data['vacancy']['to_employment_full'];
            }
  
  
            $field->street    	       = $data['vacancy']['street'];
            $field->home    	       = $data['vacancy']['home'];
            $field->housing    	       = $data['vacancy']['housing'];
            $field->office    	       = $data['vacancy']['office'];
            if (isset($data['vacancy']['from_date']) && isset($data['vacancy']['from_hh']) && isset($data['vacancy']['from_mm']) ) {
            $field->from_date          = date("Y-m-d H:i:ss",strtotime($data['vacancy']['from_date'].' '.$data['vacancy']['from_hh'].':'.$data['vacancy']['from_mm']) ); 
            }
            $field->dateadd    	       = date("Y-m-d H:i");
            $field->view_jobs    	   = $data['view_jobs'];
            $field->company_id    	   = $data['vacancy']['company_id'];
        
          if (isset($data['vacancy']['phone'])){
                $field->phone    	   = $data['vacancy']['phone'];  
           }
           if (isset($data['vacancy']['email'])){
                $field->email    	   = $data['vacancy']['email'];  
           }  
           if (isset($data['dop_info'])){
                $field->dop_info    	   = $data['dop_info'];  
           }           
           if (isset($data['vacancy']['contact_fio'])){
                $field->contact_fio    	   = $data['vacancy']['contact_fio'];  
           }         
            $db->insertObject('#__community_job_vacancies' ,  $field );
        
           $job_id = $db->insertid();
       
             $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_language' ) . ' '
				. 'WHERE  '. $db->quoteName('job_vacancies_id') .'='. $db->Quote( $job_id ) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
            

           foreach($data['vacancy']['field_language'] as $key => $value){
                $job_lang		          = new stdClass();
                $job_lang->country_id         = $value['language'];
                $job_lang->job_vacancies_id   = $job_id;
                $job_lang->level              = $value['level'];
                $db->insertObject('#__community_job_language' ,  $job_lang );
            }

     //навыки
    if (isset($data['vacancy']['field_skills'])){ 
          foreach($data['vacancy']['field_skills'] as $value){
            
                       $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_skills' ) . ' '
				. 'WHERE LOWER('. $db->quoteName('name') .')='.  $db->Quote(JString::strtoupper($value));
    	    $db->setQuery( $query );
            $result	= $db->loadObject();  
            
           	   if(is_null($result)) 
               {    
               	 $field		  	 = new stdClass();
    		  	 $field->name	 = $value;
    		     $db->insertObject('#__community_skills' ,  $field );
                   $i_id = $db->insertid();
               } else{
                   $i_id=$result->id;
               } 
            
            
                $field			= new stdClass();
                $field->vacancies_id   = $job_id;
    			$field->skills_id	   = $i_id; 
    		    $db->insertObject('#__community_job_skills' ,  $field );

          }  
       }
          if (!empty($data['vacancy']['salary_of'])  ||  !empty($data['vacancy']['percent']) || !empty($data['vacancy']['awards']) || !empty($data['vacancy']['piecework']) ) {
                $field			= new stdClass();
                $field->vacancies_id   = $job_id;
                if (isset($data['vacancy']['salary_of']) && !empty($data['vacancy']['salary_of'])  ){
    		    	$field->salary_of	         = 1; 
                    $field->field_salary_of	     = $data['vacancy']['field_salary_of'];
                    $field->field_salary_of_hint = $data['vacancy']['field_salary_of_hint'];
                }
                if (isset($data['vacancy']['percent']) && !empty($data['vacancy']['percent'])  ){
    		    	$field->percent	           = 1; 
                    $field->field_percent	   = $data['vacancy']['field_percent'];
                    $field->field_percent_hint = $data['vacancy']['field_percent_hint'];
                }
                if (isset($data['vacancy']['awards']) && !empty($data['vacancy']['awards'])  ){
    		    	$field->awards	           = 1; 
                    $field->field_awards	   = $data['vacancy']['field_awards'];
                    $field->field_awards_hint  = $data['vacancy']['field_awards_hint'];
                }
                if (isset($data['vacancy']['piecework']) && !empty($data['vacancy']['piecework'])  ){
    		    	$field->piecework	         = 1; 
                    $field->field_piecework	     = $data['vacancy']['field_piecework'];
                    $field->field_piecework_hint = $data['vacancy']['field_piecework_hint'];
                }
    		    $db->insertObject('#__community_job_salary' ,  $field );
           }     
                

           return true;

     }
      public function create($userId,$data){

        $db		= $this->getDBO();
        $my           = CFactory::getUser();        

       $query	= ' SELECT  *'
                 .' FROM ' . $db->quoteName('#__community_client') . ' WHERE   '.$db->quoteName('user_id').'=' . $db->Quote($userId);  
                       
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();   
            $field		          = new stdClass();
            $field->client_id         = $result['id'];
            $field->user_id           = $userId;
            $field->vacancy_name      = $data['vacancy']['vacancy_name'];
            $field->salary   	      = (float)str_replace(" ","",trim($data['vacancy']['salary']));
            $field->from_salary       =  isset($data['vacancy']['from_salary']) ? 1 : 0 ;
            $field->city    	      = $data['vacancy']['city'];
            if (isset($data['categoryid'])){
              $field->profarea_id    	  = $data['categoryid'];
            }
            $field->vacancy_description = trim($data['description']);
            $field->street    	       = $data['vacancy']['street'];
            $field->home    	       = $data['vacancy']['home'];
            $field->housing    	       = $data['vacancy']['housing'];
            $field->office    	       = $data['vacancy']['office'];
            if (isset($data['vacancy']['from_date']) && isset($data['vacancy']['from_hh']) && isset($data['vacancy']['from_mm']) ) {
            $field->from_date          = date("Y-m-d H:i:ss",strtotime($data['vacancy']['from_date'].' '.$data['vacancy']['from_hh'].':'.$data['vacancy']['from_mm']) ); 
            }
            $field->dateadd    	       = date("Y-m-d H:i");
            $field->view_jobs    	   = $data['view_jobs'];
          if (isset($data['vacancy']['phone'])){
                $field->phone    	   = $data['vacancy']['phone'];  
           }
           if (isset($data['vacancy']['email'])){
                $field->email    	   = $data['vacancy']['email'];  
           }  
           if (isset($data['dop_info'])){
                $field->dop_info    	   = $data['dop_info'];  
           }           
           if (isset($data['vacancy']['contact_fio'])){
                $field->contact_fio    	   = $data['vacancy']['contact_fio'];  
           }         
            $db->insertObject('#__community_job_vacancies' ,  $field );
        
           return     $db->insertid();

     }
     public function InsertFree($userId,$data){

    //   print_r($data); 
//vacancy_description
        $db		= $this->getDBO();
        $my           = CFactory::getUser();        
        
        
       $query	= ' SELECT  *'
                 .' FROM ' . $db->quoteName('#__community_client') . ' WHERE   '.$db->quoteName('user_id').'=' . $db->Quote($userId);  
                       
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();   

            $field		          = new stdClass();
            $field->client_id         = $result['id'];
            $field->user_id           = $my->id;
            $field->vacancy_name      = $data['vacancy']['vacancy_name'];
            $field->salary   	      = (float)str_replace(" ","",trim($data['vacancy']['salary']));
            $field->from_salary       =  isset($data['vacancy']['from_salary']) ? 1 : 0 ;
            $field->city    	      = $data['vacancy']['city'];
            if (isset($data['categoryid'])){
              $field->profarea_id    	  = $data['categoryid'];
            }
            if (isset($data['vacancy']['experience'])){
              $field->experience    	  = $data['vacancy']['experience'];
            }
            $field->student    	      = $data['vacancy']['student'];
            $field->education    	  = $data['vacancy']['education'];
            $field->language    	  =  json_encode($data['vacancy']['field_language']);

            $field->vacancy_description = trim($data['description']);
            $field->duties    	        = json_encode($data['vacancy']['field_duties']);  
            $field->demands    	        = json_encode($data['vacancy']['field_demands']);  
            if (isset($data['vacancy']['field_skills'])){ 
            $field->skills    	        = json_encode($data['vacancy']['field_skills']);
            }
            if(isset($data['schedule'])){
            $field->schedule    	    = json_encode($data['schedule']); 
            }
            if (isset($data['vacancy']['nature'])){
            $field->nature               =$data['vacancy']['nature'];
            }
            if (isset($data['vacancy']['schedule_field'])){
            $field->schedule_field       =$data['vacancy']['schedule_field'];
            }
            if (isset($data['vacancy']['employment'])){
            $field->employment           =$data['vacancy']['employment'];
            } 
            if (isset($data['vacancy']['from_employment_full']) && isset($data['vacancy']['to_employment_full'])){
            $field->from_employment_full = $data['vacancy']['from_employment_full'];
            $field->to_employment_full   = $data['vacancy']['to_employment_full'];
            }
  
  
            $field->street    	       = $data['vacancy']['street'];
            $field->home    	       = $data['vacancy']['home'];
            $field->housing    	       = $data['vacancy']['housing'];
            $field->office    	       = $data['vacancy']['office'];
            if (isset($data['vacancy']['from_date']) && isset($data['vacancy']['from_hh']) && isset($data['vacancy']['from_mm']) ) {
            $field->from_date          = date("Y-m-d H:i:ss",strtotime($data['vacancy']['from_date'].' '.$data['vacancy']['from_hh'].':'.$data['vacancy']['from_mm']) ); 
            }
            $field->dateadd    	       = date("Y-m-d H:i");
            $field->view_jobs    	   = $data['view_jobs'];
            $field->company_id    	   = $data['vacancy']['company_id'];
        
          if (isset($data['vacancy']['phone'])){
                $field->phone    	   = $data['vacancy']['phone'];  
           }
           if (isset($data['vacancy']['email'])){
                $field->email    	   = $data['vacancy']['email'];  
           }  
           if (isset($data['dop_info'])){
                $field->dop_info    	   = $data['dop_info'];  
           }           
           if (isset($data['vacancy']['contact_fio'])){
                $field->contact_fio    	   = $data['vacancy']['contact_fio'];  
           }         
            $db->insertObject('#__community_job_vacancies' ,  $field );
        
           $job_id = $db->insertid();
       
             $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_language' ) . ' '
				. 'WHERE  '. $db->quoteName('job_vacancies_id') .'='. $db->Quote( $job_id ) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
            

           foreach($data['vacancy']['field_language'] as $key => $value){
                $job_lang		          = new stdClass();
                $job_lang->country_id         = $value['language'];
                $job_lang->job_vacancies_id   = $job_id;
                $job_lang->level              = $value['level'];
                $db->insertObject('#__community_job_language' ,  $job_lang );
            }

     //навыки
    if (isset($data['vacancy']['field_skills'])){ 
          foreach($data['vacancy']['field_skills'] as $value){
            
                       $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_skills' ) . ' '
				. 'WHERE LOWER('. $db->quoteName('name') .')='.  $db->Quote(JString::strtoupper($value));
    	    $db->setQuery( $query );
            $result	= $db->loadObject();  
            
           	   if(is_null($result)) 
               {    
               	 $field		  	 = new stdClass();
    		  	 $field->name	 = $value;
    		     $db->insertObject('#__community_skills' ,  $field );
                   $i_id = $db->insertid();
               } else{
                   $i_id=$result->id;
               } 
            
            
                $field			= new stdClass();
                $field->vacancies_id   = $job_id;
    			$field->skills_id	   = $i_id; 
    		    $db->insertObject('#__community_job_skills' ,  $field );

          }  
       }
          if (!empty($data['vacancy']['salary_of'])  ||  !empty($data['vacancy']['percent']) || !empty($data['vacancy']['awards']) || !empty($data['vacancy']['piecework']) ) {
                $field			= new stdClass();
                $field->vacancies_id   = $job_id;
                if (isset($data['vacancy']['salary_of']) && !empty($data['vacancy']['salary_of'])  ){
    		    	$field->salary_of	         = 1; 
                    $field->field_salary_of	     = $data['vacancy']['field_salary_of'];
                    $field->field_salary_of_hint = $data['vacancy']['field_salary_of_hint'];
                }
                if (isset($data['vacancy']['percent']) && !empty($data['vacancy']['percent'])  ){
    		    	$field->percent	           = 1; 
                    $field->field_percent	   = $data['vacancy']['field_percent'];
                    $field->field_percent_hint = $data['vacancy']['field_percent_hint'];
                }
                if (isset($data['vacancy']['awards']) && !empty($data['vacancy']['awards'])  ){
    		    	$field->awards	           = 1; 
                    $field->field_awards	   = $data['vacancy']['field_awards'];
                    $field->field_awards_hint  = $data['vacancy']['field_awards_hint'];
                }
                if (isset($data['vacancy']['piecework']) && !empty($data['vacancy']['piecework'])  ){
    		    	$field->piecework	         = 1; 
                    $field->field_piecework	     = $data['vacancy']['field_piecework'];
                    $field->field_piecework_hint = $data['vacancy']['field_piecework_hint'];
                }
    		    $db->insertObject('#__community_job_salary' ,  $field );
           }     
                

           return true;

     }

       public function InsertFreelance($userId,$data){

    //   print_r($data); 

        $db		= $this->getDBO();
        $my           = CFactory::getUser();        
        
        
       $query	= ' SELECT  *'
                 .' FROM ' . $db->quoteName('#__community_client') . ' WHERE   '.$db->quoteName('user_id').'=' . $db->Quote($userId);  
                       
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();   

            $field		          = new stdClass();
            $field->client_id         = $result['id'];
            $field->user_id           = $my->id;
            $field->vacancy_name      = $data['vacancy']['vacancy_name'];
            $field->salary   	      = (float)str_replace(" ","",trim($data['vacancy']['salary']));
            $field->from_salary       =  isset($data['vacancy']['from_salary']) ? 1 : 0 ;
            $field->demands    	        = json_encode($data['vacancy']['field_demands']);
            if (isset($data['vacancy']['field_skills'])){  
             $field->skills    	        = json_encode($data['vacancy']['field_skills']);
            }
            $field->vacancy_description = trim($data['description']);
            $field->specialities_id     =$data['vacancy']['specialities'];
            $field->from_date          = date("Y-m-d",strtotime($data['vacancy']['from_date']));
            $field->to_date            = date("Y-m-d",strtotime($data['vacancy']['to_date']));
            $field->dateadd    	       = date("Y-m-d H:i");
            $field->view_jobs    	   = $data['view_jobs'];
            $field->company_id    	   = $data['vacancy']['company_id'];
          
          if (isset($data['vacancy']['phone'])){
                $field->phone    	   = $data['vacancy']['phone'];  
           }
           if (isset($data['vacancy']['email'])){
                $field->email    	   = $data['vacancy']['email'];  
           }  
           if (isset($data['dop_info'])){
                $field->dop_info    	   = $data['dop_info'];  
           }           
           if (isset($data['vacancy']['contact_fio'])){
                $field->contact_fio    	   = $data['vacancy']['contact_fio'];  
           }                
                           
            $db->insertObject('#__community_job_vacancies' ,  $field );
        
           $job_id = $db->insertid();
       
               $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_files' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote( $job_id ) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
       
           foreach($data['fields'] as $key => $value){
                $job_file		          = new stdClass();
                $job_file->vacancies_id   = $job_id;
                $job_file->patch          = $value;
                $db->insertObject('#__community_job_files' ,  $job_file );
            }

     //навыки
      if (isset($data['vacancy']['field_skills'])){  
          foreach($data['vacancy']['field_skills'] as $value){
            
                       $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_skills' ) . ' '
				. 'WHERE LOWER('. $db->quoteName('name') .')='.  $db->Quote(JString::strtoupper($value));
    	    $db->setQuery( $query );
            $result	= $db->loadObject();  
            
           	   if(is_null($result)) 
               {    
               	 $field		  	 = new stdClass();
    		  	 $field->name	 = $value;
    		     $db->insertObject('#__community_skills' ,  $field );
                   $i_id = $db->insertid();
               } else{
                   $i_id=$result->id;
               } 
            
            
                $field			= new stdClass();
                $field->vacancies_id   = $job_id;
    			$field->skills_id	   = $i_id; 
    		    $db->insertObject('#__community_job_skills' ,  $field );

          }  
      }
           return true;

     } 
     
    public function UpdateFreelance($data,$client_id){
     $db		= $this->getDBO();
    // print_r($data);
            // $config = CFactory::getConfig();
            // $inputFilter = CFactory::getInputFilter($config->get('allowhtml'));
            // $description = JRequest::getVar('description', $data['description'], 'post', 'string', JREQUEST_ALLOWRAW);
            // $description = $inputFilter->clean($description);
           //  $description = htmlspecialchars($data['description'], ENT_QUOTES);
           //  echo $data['description'];
             
             $query_up	= 'UPDATE ' . $db->quoteName( '#__community_job_vacancies' ) . ' '
					. 'SET ' . $db->quoteName( 'vacancy_name' ) . '=' . $db->Quote( $data['vacancy']['vacancy_name'] ) . ' , '
                    . ' ' . $db->quoteName( 'salary' ) . '=' . $db->Quote( (float)str_replace(" ","",trim($data['vacancy']['salary'])) ) . ' , '
                    . ' ' . $db->quoteName( 'profarea_id' ) . '=' . $db->Quote( isset($data['categoryid']) ? $data['categoryid'] : 0 ) . ' , '
                    . ' ' . $db->quoteName( 'view_jobs' ) . '=' . $db->Quote( $data['view_jobs'] ) . ' , '
                    . ' ' . $db->quoteName( 'vacancy_description' ) . '=' . $db->Quote($data['description']). ' , '
                    . ' ' . $db->quoteName( 'company_id' ) . '=' . $db->Quote( $data['vacancy']['company_id'] ) . ' , '
                    . ' ' . $db->quoteName( 'demands' ) . '=' . $db->Quote( json_encode($data['vacancy']['field_demands']) ) . ' ,  '
                    . ' ' . $db->quoteName( 'skills' ) . '=' . $db->Quote( isset($data['vacancy']['field_skills']) ? json_encode($data['vacancy']['field_skills']) : '' ) . ' , '
                    . ' ' . $db->quoteName( 'employment' ) . '=' . $db->Quote( isset($data['vacancy']['employment']) ? $data['vacancy']['employment'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'from_date' ) . '=' . $db->Quote( isset($data['vacancy']['from_date']) ? date("Y-m-d",strtotime($data['vacancy']['from_date'])) : '' ) . ', '
                    . ' ' . $db->quoteName( 'to_date' ) . '=' . $db->Quote( isset($data['vacancy']['to_date']) ? date("Y-m-d",strtotime($data['vacancy']['to_date'])) : '' ) . ' '
                    . 'WHERE  ' . $db->quoteName( 'job_vacancies_id' ) . '=' . $db->Quote( $data['job_vacancies_id'] ).' AND '.$db->quoteName( 'client_id' ).'='. $db->Quote($client_id);
 

			$db->setQuery( $query_up );
            $db->query();
    
   

          $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_files' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote( $data['job_vacancies_id'] ) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
       
           foreach($data['fields'] as $key => $value){
                $job_file		          = new stdClass();
                $job_file->vacancies_id   = $data['job_vacancies_id'];
                $job_file->patch          = $value;
                $db->insertObject('#__community_job_files' ,  $job_file );
            }

     //навыки
      if (isset($data['vacancy']['field_skills'])){  
          foreach($data['vacancy']['field_skills'] as $value){
            
                       $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_skills' ) . ' '
				. 'WHERE LOWER('. $db->quoteName('name') .')='.  $db->Quote(JString::strtoupper($value));
    	    $db->setQuery( $query );
            $result	= $db->loadObject();  
            
           	   if(is_null($result)) 
               {    
               	 $field		  	 = new stdClass();
    		  	 $field->name	 = $value;
    		     $db->insertObject('#__community_skills' ,  $field );
                   $i_id = $db->insertid();
               } else{
                   $i_id=$result->id;
               } 
            
            
                $field			= new stdClass();
                $field->vacancies_id   = $data['job_vacancies_id'];
    			$field->skills_id	   = $i_id; 
    		    $db->insertObject('#__community_job_skills' ,  $field );

          }  
      }
    
    }
    public function Update($data,$client_id){
        
        $db		= $this->getDBO();

                    $query_up	= 'UPDATE ' . $db->quoteName( '#__community_job_vacancies' ) . ' '
					. 'SET ' . $db->quoteName( 'vacancy_name' ) . '=' . $db->Quote( $data['vacancy']['vacancy_name'] ) . ' , '
                    . ' ' . $db->quoteName( 'salary' ) . '=' . $db->Quote( (float)str_replace(" ","",trim($data['vacancy']['salary'])) ) . ' , '
                    . ' ' . $db->quoteName( 'from_salary' ) . '=' . $db->Quote( isset($data['vacancy']['from_salary']) ? 1 : 0 ) . ' , '
                    . ' ' . $db->quoteName( 'city' ) . '=' . $db->Quote( $data['vacancy']['city'] ) . ' , '
                    . ' ' . $db->quoteName( 'profarea_id' ) . '=' . $db->Quote( isset($data['categoryid']) ? $data['categoryid'] : 0 ) . ' , '
                    . ' ' . $db->quoteName( 'experience' ) . '=' . $db->Quote( isset($data['vacancy']['experience']) ? $data['vacancy']['experience'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'student' ) . '=' . $db->Quote( $data['vacancy']['student'] ) . ' , '
                    . ' ' . $db->quoteName( 'view_jobs' ) . '=' . $db->Quote( $data['view_jobs'] ) . ' , '
					. ' ' . $db->quoteName( 'education' ) . '=' . $db->Quote( $data['vacancy']['education'] ) . ' , '
                    . ' ' . $db->quoteName( 'vacancy_description' ) . '=' . $db->Quote(trim($data['description'])) . ' , '
                    . ' ' . $db->quoteName( 'company_id' ) . '=' . $db->Quote( $data['vacancy']['company_id'] ) . ' , '
                    . ' ' . $db->quoteName( 'duties' ) . '=' . $db->Quote( json_encode($data['vacancy']['field_duties']) ) . ' , '
                    . ' ' . $db->quoteName( 'demands' ) . '=' . $db->Quote( json_encode($data['vacancy']['field_demands']) ) . ' ,  '
                    . ' ' . $db->quoteName( 'skills' ) . '=' . $db->Quote( isset($data['vacancy']['field_skills']) ? json_encode($data['vacancy']['field_skills']) : '' ) . ' , '
                    . ' ' . $db->quoteName( 'schedule' ) . '=' . $db->Quote( isset($data['schedule']) ? json_encode($data['schedule']) : '' ) . ' , '
                    . ' ' . $db->quoteName( 'nature' ) . '=' . $db->Quote( isset($data['vacancy']['nature']) ? $data['vacancy']['nature'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'schedule_field' ) . '=' . $db->Quote( isset($data['vacancy']['schedule_field']) ? $data['vacancy']['schedule_field'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'employment' ) . '=' . $db->Quote( isset($data['vacancy']['employment']) ? $data['vacancy']['employment'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'from_employment_full' ) . '=' . $db->Quote( isset($data['vacancy']['from_employment_full']) ? $data['vacancy']['from_employment_full'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'to_employment_full' ) . '=' . $db->Quote( isset($data['vacancy']['to_employment_full']) ? $data['vacancy']['to_employment_full'] : '' ) . ' , '
                    . ' ' . $db->quoteName( 'street' ) . '=' . $db->Quote( $data['vacancy']['street'] ) . ' , '
                    . ' ' . $db->quoteName( 'home' ) . '=' . $db->Quote( $data['vacancy']['home'] ) . ' , '
                    . ' ' . $db->quoteName( 'housing' ) . '=' . $db->Quote( $data['vacancy']['housing'] ) . ' , '
                    . ' ' . $db->quoteName( 'office' ) . '=' . $db->Quote( $data['vacancy']['office'] ) . ' , '
                    . ' ' . $db->quoteName( 'from_date' ) . '=' . $db->Quote( isset($data['vacancy']['from_hh']) ? date("Y-m-d H:i:ss",strtotime($data['vacancy']['from_date'].' '.$data['vacancy']['from_hh'].':'.$data['vacancy']['from_mm']) ) : '' ) . ' '
                    . 'WHERE  ' . $db->quoteName( 'job_vacancies_id' ) . '=' . $db->Quote( $data['job_vacancies_id'] ).' AND '.$db->quoteName( 'client_id' ).'='. $db->Quote($client_id);
 
 
            
			$db->setQuery( $query_up );
            $db->query();
             
               
                 $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_language' ) . ' '
				. 'WHERE  '. $db->quoteName('job_vacancies_id') .'='. $db->Quote( $data['job_vacancies_id']) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
            
            //print_r($data['vacancy']['field_language']);
            
           foreach($data['vacancy']['field_language'] as $key => $value){
                $job_lang		          = new stdClass();
                $job_lang->country_id         = $value['language'];
                $job_lang->job_vacancies_id   = $data['job_vacancies_id'];
                $job_lang->level              = $value['level'];
                $db->insertObject('#__community_job_language' ,  $job_lang );
            }
            
            
            
           //навыки
    if (isset($data['vacancy']['field_skills'])){ 
          foreach($data['vacancy']['field_skills'] as $value){
            
                       $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_skills' ) . ' '
				. 'WHERE LOWER('. $db->quoteName('name') .')='.  $db->Quote(JString::strtoupper($value));
    	    $db->setQuery( $query );
            $result	= $db->loadObject();  
            
           	   if(is_null($result)) 
               {    
               	 $field		  	 = new stdClass();
    		  	 $field->name	 = $value;
    		     $db->insertObject('#__community_skills' ,  $field );
                   $i_id = $db->insertid();
               } else{
                   $i_id=$result->id;
               } 
            
            
                $field			= new stdClass();
                $field->vacancies_id   = $data['job_vacancies_id'];
    			$field->skills_id	   = $i_id; 
    		    $db->insertObject('#__community_job_skills' ,  $field );

          }  
       }
               $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_salary' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote( $data['job_vacancies_id'] ) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                 
      if (!empty($data['vacancy']['salary_of'])  ||  !empty($data['vacancy']['percent']) || !empty($data['vacancy']['awards']) || !empty($data['vacancy']['piecework']) ) {
                $field			= new stdClass();
                $field->vacancies_id   = $data['job_vacancies_id'];
                if (isset($data['vacancy']['salary_of']) && !empty($data['vacancy']['salary_of'])  ){
    		    	$field->salary_of	         = 1; 
                    $field->field_salary_of	     = $data['vacancy']['field_salary_of'];
                    $field->field_salary_of_hint = $data['vacancy']['field_salary_of_hint'];
                }
                if (isset($data['vacancy']['percent']) && !empty($data['vacancy']['percent'])  ){
    		    	$field->percent	           = 1; 
                    $field->field_percent	   = $data['vacancy']['field_percent'];
                    $field->field_percent_hint = $data['vacancy']['field_percent_hint'];
                }
                if (isset($data['vacancy']['awards']) && !empty($data['vacancy']['awards'])  ){
    		    	$field->awards	           = 1; 
                    $field->field_awards	   = $data['vacancy']['field_awards'];
                    $field->field_awards_hint  = $data['vacancy']['field_awards_hint'];
                }
                if (isset($data['vacancy']['piecework']) && !empty($data['vacancy']['piecework'])  ){
    		    	$field->piecework	         = 1; 
                    $field->field_piecework	     = $data['vacancy']['field_piecework'];
                    $field->field_piecework_hint = $data['vacancy']['field_piecework_hint'];
                }
    		    $db->insertObject('#__community_job_salary' ,  $field );
           }       
            
            
            return true;
    }
    
  public function Delete($job_vacancies_id){
 	            $db		= $this->getDBO();
                
                 $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_vacancies' ) . ' '
				. 'WHERE  '. $db->quoteName('job_vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_skills' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_salary' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query(); 
                
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_reviews' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancyid') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_like' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
             
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_language' ) . ' '
				. 'WHERE  '. $db->quoteName('job_vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                
                $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_files' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
           	    $db->setQuery( $query_d );
                $db->query();  
                
  }  
  
  public function GetJobLanguage ($job_vacancies_id){
    $db		= $this->getDBO();
    $query	= ' SELECT  * '.' FROM ' . $db->quoteName('#__community_job_language'). ' jl '
    .' LEFT JOIN ' . $db->quoteName('#__community_country').' ct ON jl.country_id=ct.country_id '
    . ' WHERE  jl.'. $db->quoteName('job_vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
    
    $db->setQuery( $query );        
    
    $result	= $db->loadAssocList();    
        

   return $result; 
 }
 public function GetJobFiles ($job_vacancies_id){
    $db		= $this->getDBO();
    $query	= ' SELECT  * '.' FROM ' . $db->quoteName('#__community_job_files'). ' WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote($job_vacancies_id) ; 
    $db->setQuery( $query );        
    $result	= $db->loadAssocList();    
    
   return $result; 
 }
 public function GetCountry ( ){
    $db		= $this->getDBO();
    $query	= ' SELECT  * '.' FROM ' . $db->quoteName('#__community_country').' WHERE status=1';
    
    $db->setQuery( $query );        
    
    $result	= $db->loadAssocList();    
        

   return $result; 
 }
 public function GetGroupCompany ($my_id){
    $db		= $this->getDBO();
    $query	= ' SELECT  * '
                 .' FROM ' . $db->quoteName('#__community_company') . 'g  WHERE  g.'.$db->quoteName('ownerid').'=' . $db->Quote($my_id);
    $db->setQuery( $query );        
    $result	= $db->loadAssocList();    
        

        return $result;
 }
  public function GetClient($my_id){   
      
        $db		= $this->getDBO();
           $query	= ' SELECT  *'
                 .' FROM ' . $db->quoteName('#__community_client') . ' WHERE   '.$db->quoteName('user_id').'=' . $db->Quote($my_id);  
                       
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();    
        
        
        return $result;
  }
      public function GetMyVacancies($my_id=0,$data=array(),$limit = null, $limitstart = null){
        
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        $extraSQL=" ";

        $limit = ( is_null($limit) ) ? $this->getState('limit') : $limit;
        $limitstart = ( is_null($limitstart) ) ? $this->getState('limitstart') : $limitstart;
      
      if (!empty($my_id)){
      $query	= ' SELECT  COUNT(*) '
                 .' FROM ' . $db->quoteName('#__community_client') . 'c  INNER JOIN '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
               	. 'ON c.'.$db->quoteName('id').'=cjv.'.$db->quoteName('client_id').' AND c.'.$db->quoteName('user_id').'=' . $db->Quote($my_id)
                . ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0')
			    . $extraSQL ;
     }

        $db->setQuery($query);
        $total = $db->loadResult();
      
            // Appy pagination
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($total, $limitstart, $limit);
        
        }
        
       // soc_community_profarea
           
     if (!empty($my_id)){

              $query	= 'SELECT  * '
                . ' FROM ' . $db->quoteName('#__community_client') . 'c  INNER JOIN '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
               	. ' ON c.'.$db->quoteName('id').'=cjv.'.$db->quoteName('client_id').' AND c.'.$db->quoteName('user_id').'=' . $db->Quote($my_id)
                . ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0')
			    . $extraSQL 
                . ' ORDER BY cjv.'.$db->quoteName('dateadd').' DESC  '
                . ' LIMIT ' . $limitstart . ',' . $limit;
     }

        $db->setQuery($query);        
        $result	= $db->loadAssocList(); 
        
        return $result;
    
   }  
   public function GetMyListVacancies($my_id=0,$data=array(),$limit = null, $limitstart = null){
        
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        $extraSQL=" ";
       /*
          [vac_field_fio] => on
          [vac_field_city] => on
          [vac_field_salary] => on
          [search] => 
       */
      if (isset($data['compani_id'])){
         if (!empty($data['compani_id'])){
            
              $extraSQL .= ' AND cjv.'.$db->quoteName('company_id').' = ' . $db->Quote( $data['compani_id'] ) . ' ';
         }
      }
    
       if (isset($data['vac_field_fio'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('vacancy_name').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }   
       
       if (isset($data['office_name'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('vacancy_name').' LIKE ' . $db->Quote( '%' . $data['office_name'] . '%' ) . ' ';
       }   
       
       
       if (isset($data['vac_field_city'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('city').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }   
       
       if (isset($data['vac_field_salary'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('salary').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }    
       
       if (isset($data['salary_to']) and isset($data['salary_do'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('salary').'>=' . $db->Quote($data['salary_to']) . ' and cjv.'.$db->quoteName('salary').'<=' . $db->Quote($data['salary_do']) .' ';
       }  
       
       if (isset($data['experience_to']) and isset($data['experience_do'])){
        
        $extraSQL .= ' AND cjv.'.$db->quoteName('experience').'>=' . $db->Quote($data['experience_to']) . ' and cjv.'.$db->quoteName('experience').'<=' . $db->Quote($data['experience_do']) .' ';
     
       }  
       
       if (isset($data['area'])){
         if (!empty($data['area'])){
          $extraSQL .= ' AND cjv.'.$db->quoteName('city').' IN (' .  $data['area']   . ') ';
          }
       }      
            if (isset($data['profarea'])){
         if (!empty($data['profarea'])){
          $extraSQL .= ' AND cjv.'.$db->quoteName('profarea_id').' IN (' .  $data['profarea']   . ') ';
          }
       }        
       
       if (isset($data['student'])){
         if (!empty($data['student'])){
           $extraSQL .= ' AND cjv.'.$db->quoteName('student').' LIKE ' . $db->Quote( '%' . $data['student'] . '%' ) . ' ';
        }
       }      
       if (isset($data['education'])){
          if (!empty($data['education'])){
             $extraSQL .= ' AND cjv.'.$db->quoteName('education').' LIKE ' . $db->Quote( '%' . $data['education'] . '%' ) . ' ';
          }  
       }   
   
      if (isset($data['schedule_field'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('schedule_field').' = ' . $db->Quote(  $data['schedule_field']  ) . ' ';
       }  
       if (isset($data['nature'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('nature').' = ' . $db->Quote( $data['nature'] ) . ' ';
       } 
       
       if (isset($data['employment'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('employment').' = ' . $db->Quote( $data['employment']  ) . ' ';
       }   
       

       if (isset($data['type'])){
        $extraSQL .= ' AND (cjv.'.$db->quoteName('view_jobs').' = ' . $db->Quote($data['type']) . ' OR cjv.'.$db->quoteName('view_jobs').' =4 ) ';
       }
      /* else{
        $extraSQL .= ' AND (cjv.'.$db->quoteName('view_jobs').' = ' . $db->Quote(1) . ' OR cjv.'.$db->quoteName('view_jobs').' =4 ) ';
       }  */ 
         $order="";
      if (isset($data['order'])){
         $order=$data['order'];
      }
    
                    // Get limit
        $limit = ( is_null($limit) ) ? $this->getState('limit') : $limit;
        $limitstart = ( is_null($limitstart) ) ? $this->getState('limitstart') : $limitstart;
      
      if (!empty($my_id)){
      $query	= ' SELECT  COUNT(*) '
                 .' FROM ' . $db->quoteName('#__community_client') . 'c  INNER JOIN '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
               	 . 'ON c.'.$db->quoteName('id').'=cjv.'.$db->quoteName('client_id').' AND c.'.$db->quoteName('user_id').'=' . $db->Quote($my_id)
                 . ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0')
			     . $extraSQL ;
     }else{
              $query	= ' SELECT  COUNT(*) '
                 .' FROM '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
                . ' WHERE cjv.' . $db->quoteName('statusp') . ' = ' . $db->Quote('0')
			    . $extraSQL ;
     }

        $db->setQuery($query);
        $total = $db->loadResult();
      
            // Appy pagination
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($total, $limitstart, $limit);
        
        }
        
       // soc_community_profarea
           
     if (!empty($my_id)){

              $query	= 'SELECT  * '
                . ' FROM ' . $db->quoteName('#__community_client') . 'c  INNER JOIN '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
               	. ' ON c.'.$db->quoteName('id').'=cjv.'.$db->quoteName('client_id').' AND c.'.$db->quoteName('user_id').'=' . $db->Quote($my_id)
                . ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0')
			    . $extraSQL 
                . $order
                . ' LIMIT ' . $limitstart . ',' . $limit;
     }else{      
                
            $query	= 'SELECT cjv.*, cn.status as status_job, cs.name as status_name,(SELECT cc.name FROM ' . $db->quoteName('#__community_city') . ' cc WHERE cc.city_id=cjv.city LIMIT 1  )  as city '
                . ' FROM  '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
                . ' LEFT JOIN '.  $db->quoteName('#__community_negotiations') . ' cn  ' 
                . ' ON cjv.'.$db->quoteName('job_vacancies_id').' = cn.'.$db->quoteName('job_id').' AND cn.'.$db->quoteName('type').'=' . $db->Quote('job'). ' AND cn.'.$db->quoteName('user_id').'='. $db->Quote($my->id)
                . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id') 
                . ' WHERE cjv.' . $db->quoteName('statusp') . ' = ' . $db->Quote('1')
			    . $extraSQL 
                . $order
                . ' LIMIT ' . $limitstart . ',' . $limit;
     } 


        $db->setQuery($query);      
        $result	= $db->loadAssocList(); 
        
        return $result;
    
   }
      public function TotalVacancies($my_id=0,$data=array(),$limit = null, $limitstart = null){
        
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        $extraSQL=" ";
       /*
          [vac_field_fio] => on
          [vac_field_city] => on
          [vac_field_salary] => on
          [search] => 
       */
      if (isset($data['compani_id'])){
         if (!empty($data['compani_id'])){
            
              $extraSQL .= ' AND cjv.'.$db->quoteName('company_id').' = ' . $db->Quote( $data['compani_id'] ) . ' ';
         }
      }
    
       if (isset($data['vac_field_fio'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('vacancy_name').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }   
       
       if (isset($data['office_name'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('vacancy_name').' LIKE ' . $db->Quote( '%' . $data['office_name'] . '%' ) . ' ';
       }   
       
       
       if (isset($data['vac_field_city'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('city').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }   
       
       if (isset($data['vac_field_salary'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('salary').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
       }    
       
       if (isset($data['salary_to']) and isset($data['salary_do'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('salary').'>=' . $db->Quote($data['salary_to']) . ' and cjv.'.$db->quoteName('salary').'<=' . $db->Quote($data['salary_do']) .' ';
       }  
       
       if (isset($data['experience_to']) and isset($data['experience_do'])){
        
        $extraSQL .= ' AND cjv.'.$db->quoteName('experience').'>=' . $db->Quote($data['experience_to']) . ' and cjv.'.$db->quoteName('experience').'<=' . $db->Quote($data['experience_do']) .' ';
     
       }  
       
       if (isset($data['area'])){
         if (!empty($data['area'])){
          $extraSQL .= ' AND cjv.'.$db->quoteName('city').' IN (' .  $data['area']   . ') ';
          }
       }      
            if (isset($data['profarea'])){
         if (!empty($data['profarea'])){
          $extraSQL .= ' AND cjv.'.$db->quoteName('profarea_id').' IN (' .  $data['profarea']   . ') ';
          }
       }        
       
       if (isset($data['student'])){
         if (!empty($data['student'])){
           $extraSQL .= ' AND cjv.'.$db->quoteName('student').' LIKE ' . $db->Quote( '%' . $data['student'] . '%' ) . ' ';
        }
       }      
       if (isset($data['education'])){
          if (!empty($data['education'])){
             $extraSQL .= ' AND cjv.'.$db->quoteName('education').' LIKE ' . $db->Quote( '%' . $data['education'] . '%' ) . ' ';
          }  
       }   
   
      if (isset($data['schedule_field'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('schedule_field').' = ' . $db->Quote(  $data['schedule_field']  ) . ' ';
       }  
       if (isset($data['nature'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('nature').' = ' . $db->Quote( $data['nature'] ) . ' ';
       } 
       
       if (isset($data['employment'])){
        $extraSQL .= ' AND cjv.'.$db->quoteName('employment').' = ' . $db->Quote( $data['employment']  ) . ' ';
       }   
       

       if (isset($data['type'])){
        $extraSQL .= ' AND (cjv.'.$db->quoteName('view_jobs').' = ' . $db->Quote($data['type']) . ' OR cjv.'.$db->quoteName('view_jobs').' =4 ) ';
       }
      /* else{
        $extraSQL .= ' AND (cjv.'.$db->quoteName('view_jobs').' = ' . $db->Quote(1) . ' OR cjv.'.$db->quoteName('view_jobs').' =4 ) ';
       }  */ 
         $order="";
      if (isset($data['order'])){
         $order=$data['order'];
      }
    
   
                
            $query	= 'SELECT  count(*) as total '
                . ' FROM  '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
                . ' LEFT JOIN '.  $db->quoteName('#__community_negotiations') . ' cn  ' 
                . ' ON cjv.'.$db->quoteName('job_vacancies_id').' = cn.'.$db->quoteName('job_id').' AND cn.'.$db->quoteName('type').'=' . $db->Quote('job'). ' AND cn.'.$db->quoteName('user_id').'='. $db->Quote($my->id)
                . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id') 
                . ' WHERE cjv.' . $db->quoteName('statusp') . ' = ' . $db->Quote('1')
			    . $extraSQL 
                . $order;
     


        $db->setQuery($query);      
        $result	= $db->loadAssoc(); 
        
        return $result;
    
   }
       public function GetFilterInterests($data){
        $db		= $this->getDBO(); 
        $query_in	= ' SELECT *   FROM ' . $db->quoteName('#__community_client_interests').' WHERE ' . $db->quoteName('name') . ' like '.$db->Quote('%'.$data['interest_name'].'%').'  ';
        $db->setQuery( $query_in );        
        $result_in	= $db->loadAssocList();     
        return $result_in;
    }
    public function GetFilterSkills($data){
        $db		= $this->getDBO(); 
        $query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_skills').' WHERE ' . $db->quoteName('name') . ' like '.$db->Quote('%'.$data['skill_name'].'%').'  ';
        $db->setQuery( $query_city );        
        $result_city	= $db->loadAssocList();     
        return $result_city;
    }
    public function GetFilterSkillSelect($data){
        $db		= $this->getDBO(); 
        
        $result_profarea=array();
          if (isset($data['skills'])){
             if (!empty($data['skills'])){
                
              $query_profarea	= ' SELECT *   FROM ' . $db->quoteName('#__community_skills').'   ';  
              $query_profarea .= ' WHERE '.$db->quoteName('id').' IN (' .  $data['skills']   . ') ';
              $db->setQuery( $query_profarea );        
              $result_profarea	= $db->loadAssocList();   
              
              }
           } 
           
  
        return $result_profarea;
    }
    public function GetFilterCity($data){
        
        $session = JFactory::getSession();
       
        $id=$session->get('country_id','1'); 
        
        
        $db		= $this->getDBO(); 
        $query_city	= ' SELECT co.country_id,co.country_name,ci.name,ci.city_id,r.name as namer,ar.name_region   FROM ' . $db->quoteName('#__community_country').' co' 
        .' LEFT JOIN '. $db->quoteName('#__community_region').' r ON ( co.country_id=r.country_id)' 
        .' LEFT JOIN '. $db->quoteName('#__community_city').' ci ON ( r.region_id=ci.region_id)' 
        .' LEFT JOIN '. $db->quoteName('#__community_areas').' ar ON ( ci.areas_id=ar.areas_id)'
        .' WHERE  co.' . $db->quoteName('country_id') . ' = '.$db->Quote($id).' AND  ci.' . $db->quoteName('name') . ' like '.$db->Quote('%'.$data['city_name'].'%').' ORDER BY ci.sort_by ASC LIMIT 6 ';
        $db->setQuery( $query_city );        
        $result_city	= $db->loadAssocList();     
        return $result_city;
    }
 
    public function getCityMinsk($data_search=null, $limit_p=null){
        
        if(empty($data_search)){
            $data_search=1;
        }

        $db		= $this->getDBO();  
        //$query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_region');
        $query_city	= ' SELECT  *   FROM ' . $db->quoteName('#__community_country').' co' 
        .' LEFT JOIN '. $db->quoteName('#__community_region').' ri ON ( co.country_id=ri.country_id) '
        .' WHERE  co.' . $db->quoteName('country_id') . ' = '.$db->Quote($data_search);
        
        $db->setQuery( $query_city );       
        $result_region	= $db->loadAssocList();     
       
       $data=array();
        foreach ($result_region as $val){
            
           $data[]=array(
            'region_id' => $val['region_id'],
            'name'      => $val['name'],
            'country_name'      => $val['country_name'],
            'parent'    => $this->GetCityRegion($val['region_id'],null,$limit_p)
           ); 
        }
    
  

       
        return $data;
    }
    
    public function GetCityAll($data_search=null, $limit_p=null){
        
        $session = JFactory::getSession();
        $id=$session->get('country_id','1');       

        $db		= $this->getDBO();  
        //$query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_region');
        $query_city	= ' SELECT  *   FROM ' . $db->quoteName('#__community_country').' co' 
        .' LEFT JOIN '. $db->quoteName('#__community_region').' ri ON ( co.country_id=ri.country_id) '
        .' WHERE  co.' . $db->quoteName('country_id') . ' = '.$db->Quote($id);
        
        $db->setQuery( $query_city );       
        $result_region	= $db->loadAssocList();     
       
       $data=array();
        foreach ($result_region as $val){
            
           $data[]=array(
            'region_id' => $val['region_id'],
            'name'      => $val['name'],
            'country_name'      => $val['country_name'],
            'parent'    => $this->GetCityRegion($val['region_id'],$data_search,$limit_p)
           ); 
        }
    
  

       
        return $data;
    }
    public function getRegion($city_id){
         $db		= $this->getDBO();
         $query_city	= ' SELECT cr.region_id, cr.name  FROM ' . $db->quoteName('#__community_city').' cc '
         . ' LEFT JOIN '.  $db->quoteName('#__community_region') . ' cr ON (cc.region_id=cr.region_id) ' 
         .'  WHERE city_id='.$city_id;
         $db->setQuery( $query_city );        
         $result_city	= $db->loadAssocList();    
       
        return $result_city;  
    }
    public function GetCityRegion($region_id,$data_search=null,$limit_p=null){
        $db		= $this->getDBO();

        $query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_city').' WHERE region_id='.$region_id.' AND type_city="ci"';
        
        if(!empty($data_search['city_name'])){
           $query_city .= ' AND ' . $db->quoteName('name') . ' like '.$db->Quote('%'.$data_search['city_name'].'%');
        }
        if(!is_null($limit_p) ){
          if(!empty($limit_p)){
          $query_city .= " ORDER BY sort_by ASC LIMIT ".(int)$limit_p." ";
          }
        }
        $db->setQuery( $query_city );        
        $result_city	= $db->loadAssocList();    
       
        return $result_city;  
    }
    public function GetFilterCitySelect($data){
        $db		= $this->getDBO(); 
        
        $result_city=array();
          if (isset($data['area'])){
             if (!empty($data['area'])){
              
         
                
                
                
              $query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_city').'   ';  
              $query_city .= ' WHERE '.$db->quoteName('city_id').' IN (' .  $data['area']   . ') ';
              $db->setQuery( $query_city );        
              $result_city	= $db->loadAssocList();   
              
              }
           } 
           
  
        return $result_city;
    }
    
     public function GetFilterProfareaSelect($data){
        $db		= $this->getDBO(); 
        
        $result_profarea=array();
          if (isset($data['profarea'])){
             if (!empty($data['profarea'])){
                
              $query_profarea	= ' SELECT *   FROM ' . $db->quoteName('#__community_profarea').'   ';  
              $query_profarea .= ' WHERE '.$db->quoteName('id').' IN (' .  $data['profarea']   . ') ';
              $db->setQuery( $query_profarea );        
              $result_profarea	= $db->loadAssocList();   
              
              }
           } 
           
  
        return $result_profarea;
    }   
  
    
    public function GetFilterProfarea($data){
      
        
        
         $db		= $this->getDBO();  
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_profarea').' WHERE  ' . $db->quoteName('name') . ' like ' . $db->Quote('%'.$data['profarea_name'].'%');  
        
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
 
      return $results;   
      
    }
    public function GetProfarea(){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_profarea').' WHERE parent=0  ';  
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
         $Profarea=array();
         
         foreach($results as $val){
            
            $Profarea[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'name'     =>$val['name'],
              'sort_by'  =>$val['sort_by'],
              'patch'    =>$this->GetProfareaPatch($val['id']) 
            
            );
            
         }
      return $Profarea;   
    }
    public function GetProfareaPatch($id){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_profarea').' WHERE parent='.(int)$id.'  ';  
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
         $Profarea=array();
         
         foreach($results as $val){
            
            $Profarea[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'name'     =>$val['name'],
              'sort_by'  =>$val['sort_by'],
              'patch'    =>$this->GetProfareaPatch($val['id']) 
            
            );
            
         }
         return $Profarea;
    }  
    
    public function GetSpecialities($types=0){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_specialities').' WHERE '. $db->quoteName('types') .'='.$db->Quote($types).'  ORDER BY id ASC  ';  
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
       return $results;   
    }
    
      public function GetSpecialitiesId($id){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT  *   FROM ' . $db->quoteName('#__community_specialities').' WHERE '. $db->quoteName('id') .'='.$db->Quote($id).'   ';  
         $db->setQuery( $query );        
         $results	= $db->loadAssoc(); 
         
       return $results;   
    }  
     public function GetEducationId($id){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT  *   FROM ' . $db->quoteName('#__community_education').' WHERE '. $db->quoteName('id') .'='.$db->Quote($id).'   ';  
         $db->setQuery( $query );        
         $results	= $db->loadAssoc(); 
         
       return $results;   
    } 
    public function GetFilterCritery($my_id){
       
         $db		= $this->getDBO();  
         $query_solary	= ' SELECT min(salary) as salary_to ,max(salary) as salary_do   FROM ' . $db->quoteName('#__community_job_vacancies').' ';
         $db->setQuery( $query_solary );        
         $result_solary	= $db->loadAssoc();  
        
        $db		= $this->getDBO(); 
        $query_student	= ' SELECT DISTINCT student   FROM ' . $db->quoteName('#__community_job_vacancies').' ';
        $db->setQuery( $query_student );        
        $result_student	= $db->loadAssocList();  
        
        $db		= $this->getDBO(); 
        $query_education	= ' SELECT DISTINCT education   FROM ' . $db->quoteName('#__community_job_vacancies').' ';
        $db->setQuery( $query_education );        
        $result_education	= $db->loadAssocList();  
        
       
        $db		= $this->getDBO(); 
        $query_city	= ' SELECT *   FROM ' . $db->quoteName('#__community_city').' ORDER BY sort_by DESC LIMIT 10 ';
        $db->setQuery( $query_city );        
        $result_city	= $db->loadAssocList();     
       
       
        $result=array(
         'solary'    =>$result_solary,
         'student'   =>$result_student,
         'education' =>$result_education,
         'city'      =>$result_city
        );
    
    /*
       print('<pre>');
       print_r($result);
       print('</pre>');*/
       
       return $result;
    }
   public function getPagination() {
        return $this->_pagination;
    } 
    
  public function  GetVacancies($client_id=0,$vacancies_id){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
  
    if (!empty($client_id)){
    
        $query	= ' SELECT *, (SELECT cp.name FROM ' . $db->quoteName('#__community_profarea') . ' cp WHERE cp.id=jb.profarea_id LIMIT 1  )  as profarea, (SELECT cc.name FROM ' . $db->quoteName('#__community_city') . ' cc WHERE cc.city_id=jb.city LIMIT 1  )  as city_name  FROM ' . $db->quoteName('#__community_job_vacancies') . ' jb'  
               	 .' WHERE '. $db->quoteName('user_id').'='. $db->Quote($client_id) .' AND '.$db->quoteName('job_vacancies_id').'=' . $db->Quote($vacancies_id);
      
      }else{
       
             $query_up	= 'UPDATE ' . $db->quoteName( '#__community_job_vacancies' ) . ' '
					. 'SET viewed=viewed+1   '
               . 'WHERE  ' . $db->quoteName( 'job_vacancies_id' ) . '=' . $db->Quote( $vacancies_id );

			$db->setQuery( $query_up );
            $db->query();
       
           $query	= ' SELECT cjv.*, cn.status as status_job, cs.name as status_name   FROM ' . $db->quoteName('#__community_job_vacancies') . ' cjv'  
                . ' LEFT JOIN '.  $db->quoteName('#__community_negotiations') . ' cn  ' 
                . ' ON cjv.'.$db->quoteName('job_vacancies_id').' = cn.'.$db->quoteName('job_id').' AND cn.'.$db->quoteName('type').'=' . $db->Quote('job'). ' AND cn.'.$db->quoteName('user_id').'='. $db->Quote($my->id)
                . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id') 
               	 .' WHERE  cjv.'.$db->quoteName('job_vacancies_id').'=' . $db->Quote($vacancies_id);
                 
      } 
        $db->setQuery( $query );        
        $result	= $db->loadAssoc();   
     
     
     
     

     
     
        return $result;
        
  }
  
  public function GetSearchResume($data){
    $db		= $this->getDBO();
    $extraSQL	= '';
    
     if (!empty($data['filtr_special'])){
        $extraSQL .= ' AND c.'.$db->quoteName('office_name').' LIKE ' . $db->Quote( '%' . $data['filtr_special'] . '%' ) . ' ';
     }
     if (!empty($data['filtr_city'])){
        $extraSQL .= ' AND c.'.$db->quoteName('city').' LIKE ' . $db->Quote( '%' . $data['filtr_city'] . '%' ) . ' ';
     }  
     if (!empty($data['filtr_skills_input'])){
        $extraSQL .= ' AND c.'.$db->quoteName('key_skills').' LIKE ' . $db->Quote( '%' . $data['filtr_skills_input'] . '%' ) . ' ';
     } 
     if (!empty($data['filtr_education'])){
        $extraSQL .= ' AND ce.'.$db->quoteName('educationlevel').'=' . $db->Quote($data['filtr_education']) . ' ';
     }   
     if ( !empty($data['filtr_age_from'])  ) {
   
        $extraSQL .= ' AND (year(now())-year(c.birthdate))  <= ' . $db->Quote($data['filtr_age_from']) . ' ';
     }    

      if ( !empty($data['filtr_age_to']) ) {
   
        $extraSQL .= ' AND (year(now())-year(c.birthdate)) >=' . $db->Quote($data['filtr_age_to']) . ' ';
     }  
     if (!empty($data['filtr_gender']) && $data['filtr_gender']!='unknown'){
        $extraSQL .= ' AND c.'.$db->quoteName('gender').'=' . $db->Quote($data['filtr_gender']) . ' ';
     }   
         
     	$query	= 'SELECT *,(year(now())-year(c.birthdate)) as full_years FROM ' . $db->quoteName('#__community_client') . ' as c'
                    	. ' INNER JOIN ' . $db->quoteName('#__community_client_education') . ' AS ce ON c.' . $db->quoteName('id') . '= ce.' . $db->quoteName('client_id')
						. ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0')
						. $extraSQL 
						. ' GROUP BY c.' . $db->quoteName('id');
					/*	. ' ORDER BY '.$db->quoteName('count').' DESC '
						. ' LIMIT '.$limitstart .' , '.$limit;*/
                     
                   
      		$db->setQuery( $query );
		$rows	= $db->loadAssocList();       
        /*
       print('<pre>');
       print_r($rows);
       print('</pre>');*/  
                   
     return $rows;
  }
  public function GetSearchClient($data){
    $db		= $this->getDBO();
     $my           = CFactory::getUser();
    $extraSQL	= '';
    $HSQL ='';
      $fam='c.'.$db->quoteName('firstname');
      $im='c.'.$db->quoteName('lastname');
      $otch='c.'.$db->quoteName('fathername');
  
  
   
      if (isset($data['field_search'])){ 
            if (!empty($data['field_search'])){ 
               $extraSQL .= ' AND  CONCAT('.$fam.','.$im.','.$otch.') LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
            }
      }
    
  
     
      if (isset($data['office_name'])){
            if (!empty($data['office_name'])){ 
             $extraSQL .= ' AND c.'.$db->quoteName('office_name').' LIKE ' . $db->Quote( '%' . $data['office_name'] . '%' ) . ' ';
           }  
      }   
    
      if (isset($data['area'])){
         if (!empty($data['area'])){
          $extraSQL .= ' AND c.'.$db->quoteName('city').' IN (' .  $data['area']   . ') ';
          }
     }    
      if (isset($data['educationid'])){
         if (!empty($data['educationid'])){
          $extraSQL .= ' AND ce.'.$db->quoteName('education_id').' IN (' .  $data['educationid']   . ') ';
          }
     }    
     if (isset($data['gender_m']) || isset($data['gender_f'])   ){
                        
         $m=isset($data['gender_m']) ? $data['gender_m'] : '';
         $f=isset($data['gender_f']) ? $data['gender_f'] : '';
                           
         if ( !empty($m) && !empty($f) ){
             $extraSQL .= ' AND (c.'.$db->quoteName('gender').' = ' . $db->Quote($m) . '  ';
             $extraSQL .= ' OR  c.'.$db->quoteName('gender').' = ' . $db->Quote($f) . ') ';
        }else{
          if ( !empty($m) ){
             $extraSQL .= ' AND c.'.$db->quoteName('gender').' = ' . $db->Quote($m) . ' ';
          }                                 
          if ( !empty($f) ){
             $extraSQL .= ' AND c.'.$db->quoteName('gender').' = ' . $db->Quote($f) . ' ';
          } 
       } 
          
      } 
       if(isset($data['type'])){
            if($data['type']==1){
               $extraSQL .= ' AND c.'.$db->quoteName('st_work').' = ' . $db->Quote(1) . ' ';
            }elseif($data['type']==2){
               $extraSQL .= ' AND c.'.$db->quoteName('st_freelance').' = ' . $db->Quote(1) . ' ';
            }elseif($data['type']==3){
                $extraSQL .= ' AND c.'.$db->quoteName('st_part').' = ' . $db->Quote(1) . ' ';
            }
        }
      if (isset($data['year_to'])  ) {
   
             $extraSQL .= ' AND (year(now())-year(c.birthdate))  >= ' . $db->Quote((int)number_format($data['year_to'], 0, '', '')) . ' ';
        }    
                    
        if (isset($data['year_do']) ) {
                       
             $extraSQL .= ' AND (year(now())-year(c.birthdate)) <=' . $db->Quote((int)number_format($data['year_do'], 0, '', '')) . ' ';
        }     


       
       if (isset($data['experience_to']) and isset($data['experience_do'])){
        
          $HSQL .= ' HAVING '.$db->quoteName('workyear').'>=' . $db->Quote((int)$data['experience_to']) . '  and '.$db->quoteName('workyear').'<=' . $db->Quote((int)$data['experience_do']) .' ';
     
       }  

     	$query	= 'SELECT CONCAT('.$fam.','.'" "'.','.$im.','.'" "'.','.$otch.') as fio, (year(now())-year(c.birthdate)) as full_years, c.*, cn.status as status_job, cs.name as status_name, '
                        . ' COALESCE((SELECT SUM(year(ccw.enddate)-year(ccw.startdate))  FROM  ' . $db->quoteName('#__community_client_work') . 'ccw WHERE ccw.client_id=c.id   ),0) as workyear '
                        . ' FROM ' . $db->quoteName('#__community_client') . ' as c'
                        . ' LEFT JOIN '.$db->quoteName('#__users').' as u '. ' ON c.'.$db->quoteName('user_id').' = u.'.$db->quoteName('id') 
                        . ' LEFT JOIN '.  $db->quoteName('#__community_negotiations') . ' cn  ' 
                        . ' ON c.'.$db->quoteName('user_id').' = cn.'.$db->quoteName('job_id').' AND cn.'.$db->quoteName('type').'=' . $db->Quote('resume'). ' AND cn.'.$db->quoteName('user_id').'='. $db->Quote($my->id)
                    	. ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id') 
                       	. ' LEFT JOIN '.  $db->quoteName('#__community_client_education') . ' ce  ' 
                        . ' ON ce.'.$db->quoteName('client_id').' = c.'.$db->quoteName('id') 
                        . ' LEFT JOIN '.$db->quoteName('#__community_users').' as cu '. ' ON (cu.'.$db->quoteName('userid').'= u.'.$db->quoteName('id').')' 
                        . ' WHERE c.' . $db->quoteName('published') . ' = ' . $db->Quote('0').' AND c.' . $db->quoteName('status_search') . ' != ' . $db->Quote('2').' AND u.'.$db->quoteName('block').' = '.$db->Quote('0').' AND c.'.$db->quoteName('user_id').'<>27 '
						. $extraSQL
                        . $HSQL.' ORDER BY cu.points DESC'; 
                       // . ' GROUP BY c.id'; 
                    
      		$db->setQuery( $query );
		$rows	= $db->loadAssocList();   
        
      /* print('<pre>');
       print_r($rows);
       print('</pre>');*/
                   
     return $rows;
  }
  
  public function GetSearchCompany($data){
    $db		= $this->getDBO();
    $extraSQL	= '';

  
    if (isset($data['comp_field_fio'])){ 
        if (!empty($data['field_search'])){ 
          $extraSQL .= ' AND gc.'.$db->quoteName('name_company').' LIKE ' . $db->Quote( '%' . $data['field_search'] . '%' ) . ' ';
        }
    }
    
     if (isset($data['comp_field_city'])){
        $extraSQL .= ' AND gc.'.$db->quoteName('city').' LIKE ' . $db->Quote( '%' . $data['comp_text_city'] . '%' ) . ' ';
     }  

     if (isset($data['comp_field_activities'])){
        $extraSQL .= ' AND gc.'.$db->quoteName('description_activities').' LIKE ' . $db->Quote( '%' . $data['comp_text_activities'] . '%' ) . ' ';
     }  

         
     	$query	= 'SELECT * FROM ' . $db->quoteName('#__community_groups') . ' as g'
                        . ' INNER JOIN ' . $db->quoteName('#__community_groups_company') . ' AS gc ON g.' . $db->quoteName('id') . '= gc.' . $db->quoteName('groups_id')
						. ' WHERE g.' . $db->quoteName('published') . ' = ' . $db->Quote('1')
						. $extraSQL;

       
                        
      		$db->setQuery( $query );
		$rows	= $db->loadAssocList();   
        
      /* print('<pre>');
       print_r($rows);
       print('</pre>');*/
                   
     return $rows;
  }
  
  public function GetJobClient($client_id){
    $db		= $this->getDBO();
    
    $query	= ' SELECT *  FROM ' . $db->quoteName('#__community_client_work') . ' '  
               	 .' WHERE '. $db->quoteName('client_id').'='. $db->Quote($client_id);
       
        $db->setQuery( $query );        
        $result	= $db->loadAssocList();   
     
     return $result;
  }
    
    
     public function GetAddWishList($data){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
 
      $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_wishlist' ) . ' '
				. 'WHERE '. $db->quoteName('user_id') .'='.  $db->Quote($my->id) .' AND '. $db->quoteName('val') .'='.  $db->Quote($data['val']) .' AND ' . $db->quoteName('type') . ' = ' . $db->Quote( $data['type'] );
    	
         $db->setQuery( $query );
         $result	= $db->loadObject();
         
         
         if(is_null($result)){        
            if (isset($my->id)){ 
                
                $job_wishlist		          = new stdClass();
                $job_wishlist->user_id        = $my->id;
                $job_wishlist->type           = $data['type'];
                $job_wishlist->status         = '1';
                $job_wishlist->dateadd        = date("Y-m-d");
                $job_wishlist->val            = $data['val'];
                
                $db->insertObject('#__community_wishlist' ,  $job_wishlist );
                
                return true;
           }
        }else{
            return false;
        }   
    }
   public function GetDelWishList($data){
           $db		= $this->getDBO();
                
                 $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_wishlist' ) . ' '
				. 'WHERE  '. $db->quoteName('id') .'='. $db->Quote($data['id_wishlist']) ; 
           	    $db->setQuery( $query_d );
                $db->query(); 
    }
   public function GetAddNegotiations($data){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
       
         $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_negotiations' ) . ' '
				. 'WHERE '. $db->quoteName('user_id') .'='.  $db->Quote($my->id) .' AND '. $db->quoteName('job_id') .'='.  $db->Quote($data['val']) .' AND ' . $db->quoteName('type') . ' = ' . $db->Quote( $data['type'] );
    	
         $db->setQuery( $query );
         $result	= $db->loadObject();
      
         if(is_null($result)){        
                
                $job_negotiations		          = new stdClass();
                $job_negotiations->user_id        = $my->id;
                $job_negotiations->actor          = $data['actor'];
                $job_negotiations->type           = $data['type'];
                $job_negotiations->substate       = '1';
                $job_negotiations->status         = '3';
                $job_negotiations->dateadd        = date("Y-m-d H:i");
                $job_negotiations->job_id         = $data['val'];
                $job_negotiations->note           = $data['note'];
                $job_negotiations->view_type      = $data['view_type']; 
                $job_negotiations->view_jobs      = $data['view_jobs'];
                $db->insertObject('#__community_negotiations' ,  $job_negotiations ); 

               return true;
               
        }else{
            return false;
        } 
   }
     public function AddNegotiationsJob($userid,$company_id,$view_jobs,$note){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
       
         $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_negotiations' ) . ' '
				. 'WHERE '. $db->quoteName('user_id') .'='.  $db->Quote($my->id) .' AND '. $db->quoteName('company_id') .'='.  $db->Quote($company_id) .' AND ' . $db->quoteName('type') . ' = ' . $db->Quote( 'job' );
    	
         $db->setQuery( $query );
         $result	= $db->loadObject();
      
         if(is_null($result)){        
                
                $job_negotiations		          = new stdClass();
                $job_negotiations->user_id        = $userid;
                $job_negotiations->actor          = $my->id;
                $job_negotiations->type           = 'job';
                $job_negotiations->substate       = '1';
                $job_negotiations->status         = '10';
                $job_negotiations->dateadd        = date("Y-m-d H:i");
                $job_negotiations->job_id         = 0;
                $job_negotiations->company_id     = $company_id;
                $job_negotiations->note           = $note;
                $job_negotiations->view_type      = '1';
                $job_negotiations->view_jobs      = $view_jobs;
                $db->insertObject('#__community_negotiations' ,  $job_negotiations ); 

               return true;
               
        }else{
            return false;
        } 
   }
   

    public function AddNegotiationsResume($userid,$jobid,$view_jobs,$note){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
       
         $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_negotiations' ) . ' '
				. 'WHERE '. $db->quoteName('user_id') .'='.  $db->Quote($my->id) .' AND '. $db->quoteName('job_id') .'='.  $db->Quote($jobid) .' AND ' . $db->quoteName('type') . ' = ' . $db->Quote( 'resume' );
    	
         $db->setQuery( $query );
         $result	= $db->loadObject();
      
         if(is_null($result)){        
                              $vacancies = JTable::getInstance('Vacancies', 'CTable');
                              $vacancies->load($jobid);
                $job_negotiations		          = new stdClass();
                $job_negotiations->user_id        = $my->id;
                $job_negotiations->actor          = $userid;
                $job_negotiations->type           = 'resume';
                $job_negotiations->substate       = '1';
                $job_negotiations->status         = '3';
                $job_negotiations->dateadd        =  date("Y-m-d H:i");
                $job_negotiations->job_id         = $jobid;
                $job_negotiations->company_id     = $vacancies->company_id;
                $job_negotiations->note           = $note;
                $job_negotiations->view_type      = '2';
                $job_negotiations->view_jobs      = $view_jobs;
                $db->insertObject('#__community_negotiations' ,  $job_negotiations ); 

               return true;
               
        }else{
            return false;
        } 
   }  

   public function GetNegotiationsJob($field,$view_job){
       
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        // AND cn.' . $db->quoteName('type') . ' = ' . $db->Quote('job').'
     	$query	= 'SELECT cn.*,cn.user_id as resume_id,cn.id as negotiations_id,cs.sub_staus,cs.name as status_name,cs.dop_status,cs.status_id FROM ' . $db->quoteName('#__community_negotiations') . ' as cn'
                        . ' LEFT JOIN '. $db->quoteName('#__community_client') . 'c '  
                        . ' ON cn.'.$db->quoteName('user_id').' = c.'.$db->quoteName('user_id')
                        . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id')
						. ' WHERE cn.' . $db->quoteName($field) . ' = ' . $db->Quote($my->id).'  AND cn.'.$db->quoteName('status').'!=6'; 
						               //.' AND cn.'.$db->quoteName('view_jobs').'='.$db->Quote($view_job)    
      	$db->setQuery( $query );
    	$result	= $db->loadAssocList();   
        return $result;
    
   }    
   
   public function GetNegotiationsUser($field,$typ_client){
       
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        
       //cn.' . $db->quoteName('type') . ' = ' . $db->Quote('resume').' AND
        
     	$query	= 'SELECT cn.*,cn.user_id as resume_id,cn.id as negotiations_id ,cs.name as status_name,cs.sub_staus,cs.dop_status,cs.status_id FROM ' . $db->quoteName('#__community_negotiations') . ' as cn'
                        . ' LEFT JOIN '. $db->quoteName('#__community_client') . 'c '  
                        . ' ON cn.'.$db->quoteName('user_id').' = c.'.$db->quoteName('user_id')
                        . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id')
						. ' WHERE cn.' . $db->quoteName($field) . ' = ' . $db->Quote($my->id).' AND  cn.'.$db->quoteName('status').'!=6 '.' AND cn.'.$db->quoteName('view_jobs').'='.$db->Quote($typ_client); 
              
      	$db->setQuery( $query );
	
    	$result	= $db->loadAssocList();   
        
        return $result;
    
   }
      public function GetTotalNegotiationsJob($field,$view_job){
       
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        // AND cn.' . $db->quoteName('type') . ' = ' . $db->Quote('job').'
     	$query	= 'SELECT count(*) as total FROM ' . $db->quoteName('#__community_negotiations') . ' as cn'
                        . ' LEFT JOIN '. $db->quoteName('#__community_client') . 'c '  
                        . ' ON cn.'.$db->quoteName('user_id').' = c.'.$db->quoteName('user_id')
                        . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id')
						. ' WHERE cn.' . $db->quoteName($field) . ' = ' . $db->Quote($my->id).'  AND cn.'.$db->quoteName('status').'!=6'; 
					//.' AND cn.'.$db->quoteName('view_jobs').'='.$db->Quote($view_job)	

    
                        
      	$db->setQuery( $query );
	
    	$result	= $db->loadResult();  

        return $result;
    
   }  
      public function GetTotalNegotiationsUser($field,$typ_client){
       
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        
       //cn.' . $db->quoteName('type') . ' = ' . $db->Quote('resume').' AND
        
     	$query	= 'SELECT count(*) FROM ' . $db->quoteName('#__community_negotiations') . ' as cn'
                        . ' LEFT JOIN '. $db->quoteName('#__community_client') . 'c '  
                        . ' ON cn.'.$db->quoteName('user_id').' = c.'.$db->quoteName('user_id')
                        . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id')
						. ' WHERE cn.' . $db->quoteName($field) . ' = ' . $db->Quote($my->id).' AND  cn.'.$db->quoteName('status').'!=6 '; 
             //.' AND cn.'.$db->quoteName('view_jobs').'='.$db->Quote($typ_client) 
      	$db->setQuery( $query );
	
    	$result	= $db->loadResult();    
        
        return $result;
    
   }    
    public function GetNegotiationsResume($field){
       
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        
        
        
     	$query	= 'SELECT cn.*,cn.actor as resume_id,cn.id as negotiations_id ,cs.name as status_name,cs.sub_staus,c.office_name,c.firstname,c.lastname,c.fathername FROM ' . $db->quoteName('#__community_negotiations') . ' as cn'
                        . ' LEFT JOIN '. $db->quoteName('#__community_client') . 'c '  
                        . ' ON cn.'.$db->quoteName('actor').' = c.'.$db->quoteName('user_id')
                        . ' LEFT JOIN '.  $db->quoteName('#__community_status') . ' cs  ' 
                        . ' ON cn.'.$db->quoteName('status').' = cs.'.$db->quoteName('status_id')
						. ' WHERE cn.' . $db->quoteName($field) . ' = ' . $db->Quote($my->id).' AND cn.' . $db->quoteName('type') . ' = ' . $db->Quote('resume').'  AND cn.'.$db->quoteName('status').'!=6';
						

    
                        
      	$db->setQuery( $query );
	
    	$result	= $db->loadAssocList();   
        
        return $result;
    
   }  
      public function GetUpadateNegotiations ($data=array(),$viewed_one=0,$negotiations_id=0){
        
            $db		= $this->getDBO();
     
          if ($viewed_one>0 && $negotiations_id>0){
            
                                    $query_up	= 'UPDATE ' . $db->quoteName( '#__community_negotiations' ) . ' '
    					. 'SET ' . $db->quoteName( 'status' ) . '=' . $db->Quote( $viewed_one ) . '  '
                   . 'WHERE  ' . $db->quoteName( 'id' ) . '=' . $db->Quote( $negotiations_id ).' AND  '. $db->quoteName( 'viewed_one' ) . '=0'  ;
    
    			$db->setQuery( $query_up );
                $db->query();
            
          }else{
                        $query_up	= 'UPDATE ' . $db->quoteName( '#__community_negotiations' ) . ' '
    					. 'SET ' . $db->quoteName( 'status' ) . '=' . $db->Quote( $data['status_id'] ) . '  '
                   . 'WHERE  ' . $db->quoteName( 'id' ) . '=' . $db->Quote( $data['negotiations_id'] );
    
    			$db->setQuery( $query_up );
                $db->query();
        }
      } 
    
          public function AddReviews($userId , $data){
 
     $db		= $this->getDBO();
    

         $field			= new stdClass();
         $field->createby	     = $userId;
         $field->messages	     = $data['text_comment'];
         $field->vacancyid	     = $data['vacancy_id'];  
         $field->parent	         = $data['parent'];
         $field->status	         = '1';
         $field->positive	         = $data['plus'];
         $field->dateadd	     = date("Y-m-d");
         $db->insertObject('#__community_job_reviews' ,  $field );

        return true;
   }
         	public function getReviewsTotalPlus($vacancyId)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT count(*) FROM '.$db->quoteName('#__community_job_reviews')
				. ' WHERE '.$db->quoteName('vacancyid').' = ' . $db->Quote( $vacancyId ).' AND '.$db->quoteName('positive').'=1 ';
		$db->setQuery( $query );

				$result	= $db->loadResult(); 
            return $result;
        
  }
 	public function getReviewsTotalMinus($vacancyId)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT count(*) FROM '.$db->quoteName('#__community_job_reviews')
				. ' WHERE '.$db->quoteName('vacancyid').' = ' . $db->Quote( $vacancyId ).' AND '.$db->quoteName('positive').'=0 ';
		$db->setQuery( $query );

			$result	= $db->loadResult(); 
            return $result;
        
    }
      	public function getReviews($vacancyId)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT * FROM '.$db->quoteName('#__community_job_reviews')
				. ' WHERE '.$db->quoteName('vacancyid').' = ' . $db->Quote( $vacancyId ).' AND '.$db->quoteName('parent').' = 0';
		$db->setQuery( $query );

		$result		= $db->loadAssocList();
        
        $data=array(); 
        
          foreach ($result as $val){
            $data[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'createby' =>$val['createby'],
              'vacancyid'=>$val['vacancyid'],
              'messages' =>$val['messages'],
              'status'   =>$val['status'],
              'dateadd'  =>$val['dateadd'],
              'positive'   =>$val['positive'],
              'patch'    => $this->getReviewsParent($vacancyId,$val['id'])
             ); 
            
          }
        return $data;

	} 
    
          	public function getReviewsParent($vacancyId,$parent)
	{
	   
       		$db		= $this->getDBO();

		$query	= 'SELECT * FROM '.$db->quoteName('#__community_job_reviews')
				. ' WHERE '.$db->quoteName('vacancyid').' = ' . $db->Quote( $vacancyId ).' AND '.$db->quoteName('parent').' = ' . $db->Quote( $parent );
		$db->setQuery( $query );

		$result		= $db->loadAssocList();
        
                $data=array(); 
        
          foreach ($result as $val){
            $data[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'createby' =>$val['createby'],
              'vacancyid'=>$val['vacancyid'],
              'messages' =>$val['messages'],
              'status'   =>$val['status'],
              'dateadd'  =>$val['dateadd']
             ); 
            
          }
        return $data;
	}
    
          	public function getTypeSalary($id)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT * FROM '.$db->quoteName('#__community_job_salary')
				. ' WHERE '.$db->quoteName('vacancies_id').' = ' . $db->Quote( $id );
		$db->setQuery( $query );

		$result		= $db->loadobject();
        
        
        return $result;

	}  
	
    public function getLanguage($id)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT * FROM '.$db->quoteName('#__community_job_language')
				. ' WHERE '.$db->quoteName('job_vacancies_id').' = ' . $db->Quote( $id );
		$db->setQuery( $query );

		$result		= $db->loadobjectList();
        
        
        return $result;

	} 
    
   	public function getSpecial($id)
	{
		$db		= $this->getDBO();

		$query	= 'SELECT name FROM '.$db->quoteName('#__community_specialities')
				. ' WHERE '.$db->quoteName('id').' = ' . $db->Quote( $id );
		$db->setQuery( $query );

		$result		= $db->loadResult();
        
        
        return $result;

	}  
    
   	public function GetTotalLike($id)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT count(*) as totla FROM '.$db->quoteName('#__community_job_like')
				. ' WHERE '.$db->quoteName('vacancies_id').' = ' . $db->Quote( $id );
		$db->setQuery( $query );
		$result		= $db->loadResult();
        return $result;

	}  
   	public function GetVerificationLike($id,$userId)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT count(*) as totla FROM '.$db->quoteName('#__community_job_like')
				. ' WHERE '.$db->quoteName('vacancies_id').' = ' . $db->Quote( $id ). ' AND '.$db->quoteName('user_id').' = ' . $db->Quote( $userId );
		$db->setQuery( $query );
		$result		= $db->loadResult();
        return $result;

	}  	
    public function GetTotalReviews($id)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT count(*) as totla FROM '.$db->quoteName('#__community_job_reviews')
				. ' WHERE '.$db->quoteName('vacancyid').' = ' . $db->Quote( $id );
		$db->setQuery( $query );
		$result		= $db->loadResult();
        return $result;

	}   
    
     public function GetAddLike($data){
       $db		= $this->getDBO();
       $my           = CFactory::getUser();
 
      $query	= 'SELECT * FROM ' . $db->quoteName( '#__community_job_like' ) . ' '
				. 'WHERE '. $db->quoteName('user_id') .'='.  $db->Quote($my->id) .' AND '. $db->quoteName('vacancies_id') .'='.  $db->Quote($data['val']);
    	
         $db->setQuery( $query );
         $result	= $db->loadObject();
         
         
         if(is_null($result)){        
            if (isset($my->id)){ 
                
                $job_like		          = new stdClass();
                $job_like->user_id        = $my->id;
                $job_like->vacancies_id   = $data['val'];
                
                $db->insertObject('#__community_job_like' ,  $job_like );
                
                return true;
           }
        }else{
            
             $query_d	= 'DELETE FROM ' . $db->quoteName( '#__community_job_like' ) . ' '
				. 'WHERE  '. $db->quoteName('vacancies_id') .'='. $db->Quote( $data['val'] ) .' AND  '. $db->quoteName('user_id') .'='.  $db->Quote($my->id); 
           	    $db->setQuery( $query_d );
                $db->query();    
                 
            return false;
        }   
    }
    
         public function GetSpecialitiesComplite($data){
   //   print_r($data);
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_specialities');

         $query	.= ' WHERE types='.(int)$data['tm'].' ';
         
         if(isset($data['spec_name'])){
           $query	.= ' AND '.$db->quoteName('name') . ' like '.$db->Quote('%'.$data['spec_name'].'%');
         }

         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
         $Profarea=array();
         
         foreach($results as $val){
            
            $Profarea[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'name'     =>$val['name']            
            );
            
         }
      return $Profarea;   
    }
     public function GetSpecialitiesTree($types){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_specialities').' WHERE parent=0 AND types='.$types;  
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
         $Profarea=array();
         
         foreach($results as $val){
            
            $Profarea[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'name'     =>$val['name'],
              'patch'    =>$this->GetSpecialitiesPatch($val['id'],$types) 
            
            );
            
         }
      return $Profarea;   
    }
    public function GetSpecialitiesPatch($id,$types){
      
         $db		= $this->getDBO(); 
         $query	= ' SELECT *   FROM ' . $db->quoteName('#__community_specialities').' WHERE parent='.(int)$id.' AND types='.$types;  
         $db->setQuery( $query );        
         $results	= $db->loadAssocList(); 
         
         $Profarea=array();
         
         foreach($results as $val){
            
            $Profarea[]=array(
              'id'       =>$val['id'],
              'parent'   =>$val['parent'],
              'name'     =>$val['name'],
              'patch'    =>$this->GetProfareaPatch($val['id']) 
            
            );
            
         }
         return $Profarea;
    }  
    
    public function GetParserRabotaBy($id){
      
         $db		= $this->getDBO(); 
         $query	= 'SELECT *   FROM ' . $db->quoteName('#__parser').' WHERE id='.(int)$id;  
         $db->setQuery( $query );        
         $results	= $db->loadAssoc(); 
         
         return $results;
         
    }  
    
     public function GetTotalCategory($cat_id){
      
         $db		= $this->getDBO();  
         $query	= ' SELECT count(*) as cnt  FROM ' . $db->quoteName('#__community_job_vacancies').' WHERE  ' . $db->quoteName('profarea_id') . '='.(int)$cat_id;  
  
         $db->setQuery( $query );        
         
         
 
      return $db->loadResult();   
      
    }  
    
      public function MapVacancies($my_id=0,$limit = null, $limitstart = null){
        
        $db		= $this->getDBO();
        $my           = CFactory::getUser();
        $extraSQL=" ";

         $extraSQL .= ' AND cjv.'.$db->quoteName('city').'!=0 ';

                    // Get limit
        $limit = ( is_null($limit) ) ? $this->getState('limit') : $limit;
        $limitstart = ( is_null($limitstart) ) ? $this->getState('limitstart') : $limitstart;
    
           


              $query	= 'SELECT  * '
                . ' FROM '.  $db->quoteName('#__community_job_vacancies') . ' cjv  ' 
                . ' WHERE cjv.' . $db->quoteName('statusp') . ' = ' . $db->Quote('1')
			    . $extraSQL 
                . ' LIMIT ' . $limitstart . ',' . $limit;
 


        $db->setQuery($query);      
        $result	= $db->loadAssocList(); 
        
        return $result;
    
   }

public function CityAll(){
        
         

        $db		= $this->getDBO(); 
        $query_city	= ' SELECT ci.city_id,ci.name as cityname,ri.name as nameregion,ar.name_region   FROM ' . $db->quoteName('#__community_city').' ci' 
        .' LEFT JOIN '. $db->quoteName('#__community_region').' ri ON ( ci.region_id=ri.region_id) '
        .' LEFT JOIN '. $db->quoteName('#__community_areas').' ar ON ( ci.areas_id=ar.areas_id) '
        .' WHERE  ci.' . $db->quoteName('country_id') . ' =1 and ci.lat=""';
        
        $db->setQuery( $query_city );       
        $result_region	= $db->loadAssocList();     
       
        return $result_region;
    }
      public function cityGeneration($lat,$lng,$city_id) {
         $db		= $this->getDBO();
        if(!empty($lat) && !empty($lng)){
        		$query_d	= 'UPDATE ' . $db->quoteName( '#__community_city' ) . ' SET '
				. ' lat='.$lat.',lng='.$lng.' WHERE   '. $db->quoteName('city_id') .' = ' . $db->Quote( $city_id  );   
               	$db->setQuery( $query_d );
                $db->query();
        }
        return true;
      }
      
 }
 
 
 
?>