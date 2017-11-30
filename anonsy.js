$( document ).ready(function() {
    $("div.anons-movi").bind( "click", function(  ) {
       $('#myModalMovi').modal('show'); 
  });   
       
  WorkHallShow();
  var ticket = []; 
  function Box() {
      this.row = 0;
      this.cell = 0;
      this.link = 0; 
      this.price = 0;
      this.sector = 0; 
      
  }
 function addTicket(row, cell, link,price,sector) {
  var arr = new Box;
    arr.row = row;
    arr.cell = cell;
    arr.link = link;
    arr.price = price;
    arr.sector = sector;   
    ticket.push(arr);
  }
    
    var time='5:00';
    var arr = time.split(":");
    var m = arr[0];
    var s = arr[1];
 
 function startTimer() {
   if (s == 0) { 
    if(m==0){ 
        alert("Время оплаты истекло");
        window.location.reload();
        $('.hall-time-payment .time-hr').hide();
        return false;
    }
      m--;
      s=60;
   }else{
      s--;
   }
     $('.hall-time-payment .time-hr').empty();
     $('.hall-time-payment .time-hr').text(m+':'+s);
    setTimeout(startTimer, 1000);
  }

  
   $('.rasp_h').on('click', "li.date_item", function(){
     
      var annons=$(this).attr('data');
      var dateinfo=$(this).find(".date_link").attr('value_date');
      $(".date_list .date_item .date_link.active").removeClass('active');
      $(this).find(".date_link").addClass('active');
      
      $.ajax({
    		url: 'index.php?route=preview/preview/nextDate',
    		type: 'post',
    		data: 'annons='+annons+'&dateinfo='+dateinfo,
    		dataType: 'html',	
    		beforeSend: function() {
     
           },	
     	    success: function(html) {
            	if( $('.bottom_content .rasp_item').length>0 ){
            	   $('.bottom_content .rasp_item').remove();
            	}
                $('.rasp_h.ready').after(html);
                WorkHallShow();
             //  document.write(html);
           },
            
    		error: function(xhr, ajaxOptions, thrownError) {
    		  
    			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                
    		}
            
    	});	
    
  
   });
   
   
 function WorkHallShow(){   
  
          
          
       $('.rasp_item ul.rasp_time').on('click', "li.btn_rasp", function(){
       
            var zal_id=$(this).parent().attr('data-format');
            var details=$(this).parent().attr('details');
            var dates=parseInt($(this).attr('data-session'),10);
            var time_id=$(this).attr('data');
            var time_seans=$(this).text();
            var rad=$(this);
            time_arr = time_seans.split(/:/);
            var now=new Date();
         
          var day = moment.unix(dates);  
          tr=  moment(day, "YYYY-MM-DD");
          timers=  moment(time_seans, "HH:mm:ss");
          

       /* console.log(tr._d.getFullYear());
        console.log(tr._d.getMonth());
        console.log(tr._d.getDate());
        console.log(timers._d.getHours());    
        console.log(timers._d.getMinutes()); */
         
        time=moment(tr._d.getFullYear()+'-'+(tr._d.getMonth()+1)+'-'+(tr._d.getDate()+1)+' '+timers._d.getHours()+':'+timers._d.getMinutes(),       "YYYY-MM-DD HH:mm");
        time_plus=moment(tr._d.getFullYear()+'-'+(tr._d.getMonth()+1)+'-'+tr._d.getDate()+' '+(timers._d.getHours()-1)+':'+timers._d.getMinutes(),       "YYYY-MM-DD HH:mm");
    /*  console.log(time_plus._d);
        console.log(now);*/

           // 

         // console.log(date);
          
              
          if($('.btn_rasp.list-time-active').length>0){
             $('.btn_rasp').removeClass('list-time-active');
           }  
             $(this).addClass('list-time-active');
        
            if($('.content-hall').length>0){
                $('.content-hall').remove();
              
            }
             if($('div.rasp_item').length>0){
                $('div.rasp_item').removeClass('active');
              }
                  
               if(now<time._d || now<time_plus._d){
                
            
                        $.ajax({
                    		url: 'index.php?route=preview/preview/show',
                    		type: 'post',
                    		data: 'zal_id='+zal_id+'&details='+details+'&date='+dates+'&time_seans='+time_seans+'&time_id='+time_id,
                    		dataType: 'html',	
                    		beforeSend: function() {
                    		 rad.parent().parent().parent().parent().removeClass().css("background","url('catalog/view/javascript/imag_load/ticket-loader.gif')right no-repeat wheat").addClass('rasp_item active').attr('id','active');
                     
                         },	
                     	    success: function(html) {
                            
                                $('.rasp_item.active').append(html);
                                
                                rad.parent().parent().parent().parent().attr('style','').addClass('rasp_item').attr('id','');
                                hall();
            
                              // document.write(html);
                           },
                            
                    		error: function(xhr, ajaxOptions, thrownError) {
                    		  
                    			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                
                    		}
                            
                    	});	
        
              
              
                }else{
                     alert('Время сеанса прошло. Бронирование запрещено.');
                }
       });
       
  
 }  
 function searchDelete(arr,row,cell,link) {
    for (var i = 0; i<=arr.length-1;  i++ ) {
       if ( (ticket[i].row==row) && (ticket[i].cell==cell) && (ticket[i].link==link) ) {
            ticket.splice(i,1);
        }           
    }
 }
  
 function  blockPrice (Jquery){
     /*  arr =  Jquery.attr('datarow').split('|');
       $('.block-price').hide();
       $('#block-price-'+arr['2']+'-'+arr['3']).show();
       $('#block-price-'+arr['2']+'-'+arr['3']).click("div.adult-prise", function(){
                console.log($(this));
       }); */
 }
  
 function hall(){
    
    var count=0;
    var total=0;
    var color=''; 
    
     $( "#zoom-move" ).draggable({
        start: function() {
                   // что то при старте
            },
            drag: function( event, ui) {
                  //функция срабатывает при перемещении
               var top=parseInt($(this).css('top'));
               var left=parseInt($(this).css('left'));
                   //console.log( $(this).css('top')+' '+$(this).css('left'));
                  $("#form_cinema_hall").css({
                    'top' :top*5,
                    'left':left*5
                  })                
           },
            stop: function() {
                   // что то при окончании перемещени
            }
     });
    
  /*form_cinema_hall  $( "#zoom-move" ).mousedown(function( event ) {
      var msg = "";
      msg += event.pageX + ", " + event.pageY;
        console.log(msg);     
    });*/
   
   
    //$('#b_sinema_hall .block-price').on('click', "div", function(event){
   /*добавление билета и удаления*/
   
    $('#b_sinema_hall').on('click', "div.place", function(event){  
        
       var jq=$(this);
       var arr=jq.attr('datarow').split('|');
       var cena=arr['4'].split(',');
       var price=cena['0'];
       
       if(1 in cena) {
        var price_kinder=cena['1'];
       }else{
         var price_kinder=price;
       }
       if (isNaN(parseInt(price)) ){
          price=0;
       }

    if (arr['6']!='1'){
        
            if(jq.attr('active')=='yes'){
                 var arr_kind=$(".frame_data_seats #row-"+arr['2']+"-"+arr['3']+' input[name=kind-price]' ).attr('data').split('|');
                 jq.attr('active','no');
                 jq.removeClass('place-active');
      
                 if($(".frame_data_seats #row-"+arr['2']+"-"+arr['3']+' input[name=kind-price]' ).is(':checked') ){
                      price=arr_kind['1'];      
                 }
                 
                  
                 if($("#row-"+arr['2']+"-"+arr['3']).length>0){
                       $("#row-"+arr['2']+"-"+arr['3'] ).remove();
                 }
                
                 total=total-parseInt(price);
                 $('.frame_cost_value #totalValue').text(accounting.formatNumber(total, 0, " ")+'р.');
                 count--;
                 $('.all-count-ticket').empty();
                 $('.all-count-ticket').append(count);
                 searchDelete(ticket,arr['2'],arr['3'],arr['5']);
                console.log(count); 
            }else{
                console.log(count);
              /*  if(arr['7']==0){
                    var count_order=4;
                }else{
                    var count_order=4;//arr['7'];
                }*/
                var count_order=4;                
               if(count<=count_order){ 
                
                  count++;  
                  
                    /*jq.addClass('place-active');*/
                    
                  //  color=jq//.css( "background-color");
                  //  jq.css( "background-color","#F8A527");
                  jq.addClass('place-active');
                    jq.attr('active','yes');
                  
                    if($("#row-"+arr['2']+"-"+arr['3']).length>0){
                       $("#row-"+arr['2']+"-"+arr['3'] ).remove();
                    }
             
                     $('.frame_data_no').attr('style','display:none;'); 
                     $('.frame_data_places').attr('style','display:block;');
                     $('.frame_data_overall').remove();
                          console.log(total+' '+parseInt(price));
                     total=total+parseInt(price);
                     addTicket(arr['2'],arr['3'],arr['5'],price,arr['8']);
                     
                     $( "<div class='frame_data_overall'>Всего билетов: <div class='all-count-ticket'> "+count+" </div> </div>" ).prependTo( "#places" );
                     $(".frame_data_seats .type").after("<span class='row' id='row-"+arr['2']+"-"+arr['3']+"'>ряд&nbsp;<span class='num'>"+arr['2']+"</span><span class='comma'></span> место&nbsp;<span class='num'>"+arr['3']+"</span><span class='comma'></span> цена&nbsp;<span class='num price' >"+accounting.formatNumber(price, 0, " ")+"р.</span><span class='comma'></span> детская цена: <input type='checkbox' name='kind-price' data='"+price+"|"+price_kinder+"|"+arr['2']+"|"+arr['3']+"|"+arr['5']+"|"+arr['8']+"' value='"+price+"'> </span>");
                 
                     $('.frame_cost_value #totalValue').text(accounting.formatNumber(total, 0, " ")+'р.');
                   
               }else{
                    alert('За один раз можно заказать не более '+(count_order+1)+' билетов');
                }
               
           
            }
    
    
              if(count==0){
                    $( ".frame_btns .btn" ).removeClass( "btn btn-primary btn-large" ).addClass( "btn btn-large disabled" );
                    $('.frame_cost').hide();
                 }
              if (count==1){ /*btn btn-primary btn-large*/
                    $( ".frame_btns .btn" ).removeClass( "btn btn-large disabled" ).addClass( "btn btn-primary btn-large" );
                    $('.frame_cost').show();
               }
        
        }else{
            
            alert('Бронирование запрещено!');
            
        }
       
        
    });    
    
    
    /*Формирование детской цены*/
     $('.frame_data_seats').on('click', "input[name=kind-price]", function(event){  
           var arr_kind=$(this).attr('data').split('|');
           
         if( $(this).is(':checked') ){
            $(".frame_data_seats #row-"+arr_kind['2']+"-"+arr_kind['3']+" .num.price").empty();
            $(".frame_data_seats #row-"+arr_kind['2']+"-"+arr_kind['3']+" .num.price").append(accounting.formatNumber(arr_kind['1'], 0, " ")+'р');
            total=total-parseInt( $(this).val()); 
            total=total+parseInt(arr_kind['1']);
            $('.frame_cost_value #totalValue').empty();
            $('.frame_cost_value #totalValue').append(accounting.formatNumber(total, 0, " ")+'р.');
            searchDelete(ticket,arr_kind['2'],arr_kind['3'],arr_kind['4']);
            addTicket(arr_kind['2'],arr_kind['3'],arr_kind['4'],arr_kind['1'],arr_kind['5']);
  
            
         } else{
            $(".frame_data_seats #row-"+arr_kind['2']+"-"+arr_kind['3']+" .num.price").empty();
            $(".frame_data_seats #row-"+arr_kind['2']+"-"+arr_kind['3']+" .num.price").append(accounting.formatNumber(arr_kind['0'], 0, " ")+'р');
        
            total=total-parseInt(arr_kind['1']); 
            total=total+parseInt($(this).val());
            $('.frame_cost_value #totalValue').empty();
            $('.frame_cost_value #totalValue').append(accounting.formatNumber(total, 0, " ")+'р.');
            searchDelete(ticket,arr_kind['2'],arr_kind['3'],arr_kind['4']);
            addTicket(arr_kind['2'],arr_kind['3'],arr_kind['4'],$(this).val(),arr_kind['5']);
         
         }  
    });
    
    
    /*отображение треуголинка ряда*/
     $('#b_sinema_hall').on('mouseover', "div.place", function(event){
        arr =  $(this).attr('datarow').split('|');
        $('#row-left-'+arr['2']).addClass('row-left-nam');
        $('#row-right-'+arr['2']).addClass('row-right-nam');
    });
    $('#b_sinema_hall').on('mouseout', "div.place", function(event){
        arr =  $(this).attr('datarow').split('|');
        $('#row-left-'+arr['2']).removeClass('row-left-nam');
        $('#row-right-'+arr['2']).removeClass('row-right-nam');
        
    });

   // $('#b_sinema_hall').on('click', "div.place", function(event){
       

      //  blockPrice($(this));
        
        
        
       /*
              //    console.log($(this));
          if($(this).attr('active')=='yes'){
            $(this).attr('active','no');
            $(this).removeClass('place-active');
            $(this).attr('style',$(this).attr('style')+color+';');
             arr =  $(this).attr('datarow').split('|');
             
             $("#row-"+arr['2']+"-"+arr['3'] ).remove();
             total=total-parseInt(arr['4']);
             $('.frame_cost_value #totalValue').text(total+'р.');
            count--;
            searchDelete(ticket,arr['2'],arr['3'],arr['5']);
           // console.log(ticket);
            
          } else{
              if(count<=4){
                count++;
                var arr_styly = $(this).attr('style').split(';');
                color=arr_styly[arr_styly.length-2];
                arr_styly.splice(arr_styly.length-2,2);

                $(this).attr('style',arr_styly.join(';')+';')
                $(this).addClass('place-active');
              
                $('.frame_data_no').attr('style','display:none;'); 
                $('.frame_data_places').attr('style','display:block;');
                
                $('.frame_data_overall').remove();
                
                
                
                $( "<div class='frame_data_overall'>Всего <i class='rzd'></i>"+count+" билета <i class='rzd'></i></div>" ).prependTo( "#places" );
                
               
                arr =  $(this).attr('datarow').split('|');
               
                addTicket(arr['2'],arr['3'],arr['5'],arr['4']);
            //    id="block-price-'.$row.'-'.$cell.'"
          
           
                $('.block-price').hide();
                $('#block-price-'+arr['2']+'-'+arr['3']).show();
                
                $(".frame_data_seats .type").after("<span class='row' id='row-"+arr['2']+"-"+arr['3']+"'>ряд&nbsp;<span class='num'>"+arr['2']+"</span><span class='comma'>,</span> место&nbsp;<span class='num'>"+arr['3']+"</span></span>");
               
                 total=total+parseInt(arr['4']);
                 
                $('.frame_cost_value #totalValue').text(total+'р.');
                
                $(this).attr('active','yes');
                //console.log(ticket);
            }else{
                alert('За один раз можно заказать не более 5 билетов');
            }
            
                      
             if(count==1){
                $( ".frame_btns .btn" ).removeClass( "btn btn-large disabled" ).addClass( "btn btn-primary btn-large" );
                $('.frame_cost').show();
             }
       
          }
         
            
            */
                  
    //});

    $(".frame_btns .btn").bind( "click", function(  ) {

       if($("a.btn.btn-primary.btn-large").attr('class')=='btn btn-primary btn-large'){
            
              $.ajax({
        		url: 'index.php?route=preview/preview/GetFormPayment',
        		type: 'post',
        		data: 'data=0',
        		dataType: 'html',	
        		beforeSend: function() {
         
               },	
         	    success: function(html) {
                 	 
                      $('#form_cinema_hall').remove();	
                      $( ".frame_btns .btn" ).removeClass( "btn btn-primary btn-large" ).addClass( "btn btn-large" );
                      $(this).text('Далее');	 
                   
                     $(html).prependTo( "#b_sinema_hall" );
                     startTimer();
                     $('.hall-time-payment .time-hr').show();

               },
                
        		error: function(xhr, ajaxOptions, thrownError) {
        		  
        			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    
        		}
                
       	});	

       }else{
            
           if($("a.btn.btn-large").attr('class')=='btn btn-large'){        
              var telephone=$('#telephone').val();
              var email    =$('#email').val();
              var payment=$('#payment_select option:selected').val();    
              var status=$('#payment_select option:selected').attr('data-status'); 
              var commission=$('#payment_select option:selected').attr('data-commission'); 
              var amount=parseInt(accounting.formatNumber($('.frame_cost_value #totalValue').text(), 0, "")); 
              var procent=(commission/100*amount);

            
             
              if(payment.length>0 && telephone.length>0 && email.length>0 ) {
                    if ($('#verification').is(':checked')) {
                         console.log('1');
                          $.ajax({
                        		url: 'index.php?route=preview/preview/reservation',
                        		type: 'post',
                        		data: 'data='+JSON.stringify(ticket)+'&email='+email+'&telephone='+telephone+'&payment='+payment+'&status='+status+'&commission='+procent+'&verification=0',
                        		dataType: 'html',	
                        		beforeSend: function() {
            
                                },	
                         	    success: function(html) {
                                  console.log(html);
                                  if(html==0){
                                    $('#telephone').attr('style','border: 1px solid #D11C1C;');
                                    $('#email').attr('style','border: 1px solid #D11C1C;');
                                    $('#payment_select').attr('style','border: 1px solid #D11C1C;');
                                    $('.text-warning.hide').show();
                                   
                                    
        
                                        
                                  }	else if(html==2){
                                  
                                     alert("Извините место времено заблокированы!");                                
                            
                                  } else{
                                         alert("Ваши билеты офрмлены. Заказ отправлен Вам с помощью смс и email."); 
                                         location=window.location;
                                           /* $('#myModal').modal('show'); 
                                           
                                            $('#myModal').on('hidden', function () {
                                               location=window.location;
                                            })*/
                               
                                     
                                  }
                               },
                                
                        		error: function(xhr, ajaxOptions, thrownError) {
                        		  
                        			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                    
                        		}
                                
                        	});
                    
                    } else {
                          console.log('0');
                        $.ajax({
                    		url: 'index.php?route=preview/preview/reservation',
                    		type: 'post',
                    		data: 'data='+JSON.stringify(ticket)+'&email='+email+'&telephone='+telephone+'&payment='+payment+'&status='+status+'&commission='+procent,
                    		dataType: 'html',	
                    		beforeSend: function() {
        
                            },	
                     	    success: function(html) {
                            
                            
                              if(html==0){
                              
                                    $('#telephone').attr('style','border: 1px solid #D11C1C;');
                                    $('#email').attr('style','border: 1px solid #D11C1C;');
                                    $('#payment_select').attr('style','border: 1px solid #D11C1C;');
                                    $('.text-warning.hide').show();
                               
                                 
    
                                    
                              } else if(html==2){
                               
                                alert("Извините место временно заблокировована!Обратитесь к администратору...");                                
                            
                              }else{
                                  console.log(html);
                                  
                                    $('#myModal').modal('show'); 
                                   
                                    $('#myModal').on('hidden', function () {
                                       location=window.location;
                                    })
                           
                                 
                              }
                              
                              
                              
                           },
                            
                    		error: function(xhr, ajaxOptions, thrownError) {
                    		  
                    			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                
                    		}
                            
                    	});
                    } 
	
                } else{
                         $('#telephone').attr('style','border: 1px solid #D11C1C;');
                         $('#email').attr('style','border: 1px solid #D11C1C;');
                         $('#payment_select').attr('style','border: 1px solid #D11C1C;');
                }
                   
          }
        
       }
       

       
    });
 


   $("#close_hall").bind( "click", function(  ) {

          if($('.btn_rasp.list-time-active').length>0){
             $('.btn_rasp').removeClass('list-time-active');
           }  
    
        
         if($('.content-hall').length>0){
                $('.content-hall').fadeOut(300, function(){ $(this).remove();});/.remove();*/
              
          }
            
          if($('div.rasp_item').length>0){
                $('div.rasp_item').removeClass('active');
          }
   });

 
 }  

    function redraw(){
                      $.ajax({
            		url: 'index.php?route=preview/preview/GetFormPayment',
            		type: 'post',
            		data: 'data=0',
            		dataType: 'html',	
            		beforeSend: function() {
             
                   },	
             	    success: function(html) {
                     	 
                          $('#form_cinema_hall').remove();	
                          $( ".frame_btns .btn" ).removeClass( "btn btn-primary btn-large" ).addClass( "btn btn-large" );
                          $(this).text('Далее');	 
                       
                         $(html).prependTo( "#b_sinema_hall" );
                         startTimer();
                         $('.hall-time-payment .time-hr').show();
    
                   },
                    
            		error: function(xhr, ajaxOptions, thrownError) {
            		  
            			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        
            		}
                    
           	});	
    }

});    