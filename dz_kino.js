/*
function draw() {

   context.rect(0,0, 40,40);
    for(i=0; i<=440;i=i+40){
       for(j=0; j<=1000;j=j+40){
           
            context.rect(j,i, 40,40); 
       }     
    }
}*/


var boxes = []; 

//Box object to hold data for all drawn rects
function Box() {
  this.x = 0;
  this.y = 0;
  this.w = 1; // default width and height?
  this.h = 1;
  this.fill = '#444444';
  this.sector = '0';
  this.pricenum = '0';
  this.state= 0;
  this.place= 0;
  this.line = 0;
  this.cell = 0;
}
function Box_zon() {
  this.rice = 0;
  this.color = '';
  this.discription = ''; 
  this.sector = '';
  this.pricenum = '';
  this.startrow='';
}
var zone_cena=[];
function zone(price,color,discription,sector,pricenum,startrow){
  var zon = new Box_zon;  
  zon.rice = price;
  zon.color = color;
  zon.discription = discription;
  zon.sector = sector;
  zon.pricenum = pricenum;
  zon.startrow=startrow;
  zone_cena.push(zon);
} 

var primer = [];
//var rang=[];
var rang = new Array();

function unique(arr) {
    var newArr = [];
    for (var i = 0, l = arr.length; i < l; i++ ) {
                 for (var j = i+1, count = 0; j < l; j++) {
                   if (arr[i] === arr[j]) {
                      count++;
       }
             }
              if (!count) {
                  newArr.push(arr[i]); 
                  count = 0;
              }
          }
        return newArr;
}
function verificationRange(y,line){
  if (rang.length>0){
   // if (rang.length>=line){
        $.each(rang, function( index, value ) {
            if(value!=y){
               rang.push(y); 
               primer[line]=[];
               console.log(value+'=='+y);
               return;
            }
          // console.log('===>'+index+' '+value);
         });
     //}
  }else{
    rang.push(y);
    primer[line]=[];
    //console.log('==1');
  }
  
}

//Initialize a new Box, add it, and invalidate the canvas
function addRect(x, y, w, h, fill,sector,place, line,cell,pricenum) {
  var rect = new Box;
  rect.x = x;
  rect.y = y;
  rect.w = w
  rect.h = h;
  rect.fill = fill;
  rect.sector = sector;
  rect.pricenum = pricenum;
  rect.state = '1';
  rect.line= line;
  rect.cell= cell;
  rect.place=place;

  primer[line][cell]=rect; 
 invalidate();
}

var canvas;
var ctx;
var WIDTH;
var HEIGHT;
var width_k=0;
var height_k=0;
var INTERVAL = 60;  // how often, in milliseconds, we check to see if a redraw is needed
var block_w=10;
var block_h=10; 
var name_zal='';
var name_ojeckt='';
var name_ojeckt_id=0;
var descr_ojeckt='';

var isDrag = false;
var mx, my; // mouse coordinates
var detali='select'
 
 // when set to true, the canvas will redraw everything
 // invalidate() just sets this to false right now
 // we want to call invalidate() whenever we make a change
var canvasValid = false;
 
// The node (if any) being selected.
// If in the future we want to select multiple objects, this will get turned into an array
var mySel; 
 
// The selection color and width. Right now we have a red selection with a small width
var mySelectPlaceId=0;
var mySelColorBlock;
var mySelSector='0';
var mySelPrice='1';
var mySelColor;
var mySelColorLine;
var mySelWidth = 2;
 
// we use a fake canvas to draw individual shapes for selection testing
var ghostcanvas;
var gctx; // fake canvas context
 
// since we can drag from anywhere in a node
// instead of just its x/y corner, we need to save
// the offset of the mouse when we start dragging.
var offsetx, offsety;
 
// Padding and border style widths for mouse offsets
var stylePaddingLeft, stylePaddingTop, styleBorderLeft, styleBorderTop;
 
// initialize our canvas, add a ghost canvas, set draw loop
// then add everything we want to intially exist on the canvas
/*function getMouse (e) {
  console.log(e.x,e.y);
  mx=e.x;
  my=e.y;
  onload="init()"
}*/
function init() {
 
  canvas = document.getElementById('canvas');
  HEIGHT =  canvas.height;
  WIDTH = canvas.width;
  

  ctx = canvas.getContext('2d');
  ghostcanvas = document.createElement('canvas');
  ghostcanvas.height = HEIGHT;
  ghostcanvas.width = WIDTH;

  gctx = ghostcanvas.getContext('2d');

 
  //fixes a problem where double clicking causes text to get selected on the canvas
  canvas.onselectstart = function () { return false; }
 
  // fixes mouse co-ordinate problems when there's a border or padding
  // see getMouse for more detail
  if (document.defaultView && document.defaultView.getComputedStyle) {
    stylePaddingLeft = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingLeft'], 10)      || 0;
    stylePaddingTop  = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingTop'], 10)       || 0;
    styleBorderLeft  = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderLeftWidth'], 10)  || 0;
    styleBorderTop   = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderTopWidth'], 10)   || 0;
  }
 
  // make draw() fire every INTERVAL milliseconds.
//  console.log(canvasValid);    
  setInterval(draw, INTERVAL);
 
/*
  console.log(ghostcanvas);
  console.log(canvas);*/
  // add our events. Up and down are for dragging,
  // double click is for making new boxes

  canvas.ondblclick = myDblClick;
  canvas.onclick = myClick;
  // add custom initialization here:
 

    
    series=0;
  /* block_wi=parseInt(block_w)+5;
    console.log(width_k+' '+height_k+' '+block_wi);
     for(y=0; y<=width_k;y=y+block_wi){
    
       for(x=0; x<=height_k;x=x+block_wi){  
             series++;
         addRect(y, x, block_w, block_h, mySelColorBlock,series);
      }
    }*/
   /* block_wi=20+5;
    width_k=parseInt(width_k)+20;
    height_k=parseInt(height_k)+20;
    
   for(y=0; y<=width_k;y++){
    console.log(width_k+' '+height_k+' '+block_wi);
         series++;
         addRect(y, 0, 20, 20, mySelColorBlock,series);
     
       for(x=0; x<=height_k;x=x+block_wi){  
        console.log(width_k+' '+height_k+' '+block_wi);
             series++;
         addRect(y, x, 20, 20, mySelColorBlock,series);
      }
    }*/
  // add a smaller blue rectangle
/* addRect(0, 100, 25, 25, '#2BB8FF');*/
  
  /*создается масив с ценой описание с */
  
    zone("0","#4ff322","Место","","1","1");
   
   var html='';
    html +='<input type="text"   id="color_s_1" value="#4ff322"  />';
    html +='<input type="text"   id="desript_p_1" value="" placeholder="Описание места" />';
    html +='<input type="text"   id="sector_p_1"  value="0" placeholder="Сектор" />C.';
    html +='<input type="text"   id="pricenum_p_1"  value="1" placeholder="Ценавая категория" />Ц.';
    html +='<input type="text"   id="startrow_p_1"  value="1" placeholder="Начало ряда" />Р.';
    html +='<input type="button" id="button_select_color" data="1" value="Закрепить цвет" />';
    
    $('#zone_price').append(html);
    
    
    
  var ws=2;
  var wy=2;
  var indent_x=0;
  var indent_y=0;
  series_l=0;
  series_c=0;
  for(y=0; y<=height_k-1;y++){
    primer[series_l]=[];
   // console.log(series_l);

     for(x=0; x<=width_k-1;x++){      
        
          //console.log(mySelColorBlock);
       addRect(wy+indent_y, ws+indent_x, 20, 20, mySelColorBlock,mySelSector,mySelectPlaceId,series_l,series_c,mySelPrice);
       series_c++;
       wy=25+wy;
       indent_y=indent_y+1;
     }
     series_l++;
     wy=2;
     series_c=0;
     indent_y=0;
     indent_x=indent_x+1;
     ws=25+ws;
  }
  //rang=unique(rang);
 // console.log(rang);
  //console.log(primer);
}

// Draws a single shape to a single context
// draw() will call this with the normal canvas
// myDown will call this with the ghost canvas

function drawshape(context, shape, fill) {
  context.fillStyle = fill;
  ctx.strokeStyle = mySelColorLine;
  ctx.lineWidth = mySelWidth;

  // We can skip the drawing of elements that have moved off the screen:
 /* if (shape.x > WIDTH || shape.y > HEIGHT) return; 
  if (shape.x + shape.w < 0 || shape.y + shape.h < 0) return;*/
  
//fillRect strokeRect
//console.log(shape.x +'  '+shape.y +'  '+shape.w +'  '+shape.h);
  context.fillRect(shape.x,shape.y,shape.w,shape.h);
  context.strokeRect(shape.x,shape.y,shape.w,shape.h);

  //  context.stroke();
}


// While draw is called as often as the INTERVAL variable demands,
// It only ever does something if the canvas gets invalidated by our code
function clear(c) {
  c.clearRect(0, 0, WIDTH, HEIGHT);
  
}
function draw() {
    
  if (canvasValid == false) {
    clear(ctx);
 
    // Add stuff you want drawn in the background all the time here
 
    // draw all boxes
   /* var l = boxes.length;
    for (var i = 0; i < l; i++) {
         drawshape(ctx, boxes[i], boxes[i].fill);      
    }*/
   
   var count=primer.length;
    for (var i = 0; i < count; i++) {
        for (var j = 0; j < primer[i].length; j++) {
            drawshape(ctx, primer[i][j], primer[i][j].fill);   
        }
    }
  
    if (mySel != null) { 
       
              ctx.strokeStyle = mySelColorLine;
              ctx.lineWidth = mySelWidth;
              ctx.fillStyle = mySelColor;
             // ctx.fillRect = fill;
             // ctx.strokeRect(mySel.x,mySel.y,mySel.w,mySel.h);
           //  ctx.clearRect(mySel.x,mySel.y,mySel.w,mySel.h);
           ctx.fillRect(mySel.x,mySel.y,mySel.w,mySel.h);
     }
     
    // draw selection
    // right now this is just a stroke along the edge of the selected box

    // Add stuff you want drawn on top all the time here
 
 
    canvasValid = true;
  }
}

// Happens when the mouse is clicked in the canvas

 function myClick(e) {
    getMouse(e);
    clear(gctx); 

  
    for(i=0;i<=primer.length-1;i++){
       for (var j = 0; j < primer[i].length; j++) {   

              drawshape(gctx, primer[i][j], '#000000');
              
                        var imageData = gctx.getImageData(mx, my, 1, 1);
              var index = (mx + my * imageData.width) * 4;
                    
           
           if (imageData.data[3] > 0) {
                 mySel = primer[i][j];
                  if (detali=='select'){
                     isDrag = false;
                     primer[i][j].fill=mySelColor;
                     primer[i][j].sector=mySelSector;
                     primer[i][j].pricenum=mySelPrice;
                     primer[i][j].place=mySelectPlaceId;
                     
                  } else if (detali=='delete'){
                     isDrag = false;
                     primer[i].splice(j,1);
                  } else if (detali=='move'){
                    isDrag = true;
                    
                    ctx.fillStyle = '#4ff322';
                    canvas.onmousedown = myDown;
                    canvas.onmouseup = myUp;
                    
                  }   
                 invalidate();
               return;
           }
        }     
    }
    
          if (detali=='add') { 
              getMouse(e);
              var line=$( "input[name=series_line]" ).val();
             if (line>0){  
               if (primer[line].length>0){
                   
                  // addRect(x, y, w, h, fill, line,cell) {
                //    console.log(primer);
                //    console.log(primer[line].length);
                    addRect(mx - (20 / 2), my - (20 / 2), 20, 20, mySelColorBlock,mySelSector,mySelectPlaceId,line,primer[line].length,mySelPrice);
                 //  console.log(primer);
                   
               }else{
                alert('Внимание: проверте ряд возможно Вы неверно его указали....');
               }
             }else{
                alert('Ряд должен быть больше нуля');
             }
          }
       

    mySel = null;
    clear(gctx);
    invalidate();
 } 

function myDown(e){
   // console.log(isDrag);
 if (isDrag){
    
      getMouse(e);
    
      clear(gctx); // clear the ghost canvas from its last use
    
      // run through all the boxes
    for(i=0;i<=primer.length-1;i++){
       for (var j = 0; j < primer[i].length; j++) {   
        // draw shape onto ghost context
        drawshape(gctx, primer[i][j], 'black');
    
        // get image data at the mouse x,y pixel
        var imageData = gctx.getImageData(mx, my, 1, 1);
        var index = (mx + my * imageData.width) * 4;
     
        // if the mouse pixel exists, select and break
        if (imageData.data[3] > 0) {
          mySel = primer[i][j];
           // console.log(l+' '+index+' '+mx+' '+my+' '+imageData.data[3]+' '+mySel);
          offsetx = mx - mySel.x;
          offsety = my - mySel.y;
          mySel.x = mx - offsetx;
          mySel.y = my - offsety;
            // console.log(l+' '+index+' '+mx+' '+my+' '+imageData.data[3]+' '+mySel+' '+offsetx+' '+offsety+' '+mySel.x+' '+mySel.y);
          
          isDrag = true;
          canvas.onmousemove = myMove;
          invalidate();
          clear(gctx);
          return;
        }
       }
      }
      // havent returned means we have selected nothing
      mySel = null;
      // clear the ghost canvas for next time
      clear(gctx);
      // invalidate because we might need the selection border to disappear
      invalidate();
  }    
}

// Happens when the mouse is moving inside the canvas
function myMove(e){
    
  if (isDrag){
    getMouse(e);
 
    mySel.x = mx - offsetx;
    mySel.y = my - offsety;   
     //console.log(mySel.x+' '+mySel.y);
      
    // something is changing position so we better invalidate the canvas!
    invalidate();
  }
}
 
function myUp(){
  isDrag = false;
  canvas.onmousemove = null;
}

function myDblClick(e) {
    
     getMouse(e);
    clear(gctx); 
    for(i=0;i<=primer.length-1;i++){
      for (var j = 0; j < primer[i].length; j++) { 
          

          drawshape(gctx, primer[i][j], mySelColor);
          
                    var imageData = gctx.getImageData(mx, my, 1, 1);
          var index = (mx + my * imageData.width) * 4;
  
       
       if (imageData.data[3] > 0) {
             mySel = primer[i][j];
               //console.log(boxes[i]);
               primer[i][j].fill=mySelColorBlock;
                // ctx.fillStyle = '#4ff322';
             //isDrag = true;
            // canvas.onmousemove = myMove;
             invalidate();
         //    clear(gctx);
           return;
       }
      }       
    }

    mySel = null;
    clear(gctx);
    invalidate();
    
  /*getMouse(e);
  // for this method width and height determine the starting X and Y, too.
  // so I left them as vars in case someone wanted to make them args for something and copy this code
  var width = 20;
  var height = 20;
  addRect(mx - (width / 2), my - (height / 2), width, height, '#77DD44');*/
}

function invalidate() {
  canvasValid = false;
}

// Sets mx,my to the mouse position relative to the canvas
// unfortunately this can be tricky, we have to worry about padding and borders
function getMouse(e) {
      var element = canvas, offsetX = 0, offsetY = 0;

      if (element.offsetParent) {
        do {
          offsetX += element.offsetLeft;
          offsetY += element.offsetTop;
        } while ((element = element.offsetParent));
      }

      // Add padding and border style widths to offset
      offsetX += stylePaddingLeft;
      offsetY += stylePaddingTop;

      offsetX += styleBorderLeft;
      offsetY += styleBorderTop;

      mx = e.pageX - offsetX;
      my = e.pageY - offsetY
}

$( document ).ready(function() {
    
  $( "#button_designer_kino" ).bind( "click", function( event ) {
      name_zal = $('#name_k').val();
      name_ojeckt = $('#title_k option:selected').text();
      name_ojeckt_id = $('#title_k option:selected').val();
      descr_ojeckt = $('#descriptions_zal').val();
   //  console.log(name_ojeckt+' '+name_ojeckt_id);
     

     // if(NAME.length>3){    
       /*    block_w  = $('#block_w').val();
           block_h  = $('#block_h').val();*/
       if( (name_zal.length>3)||(name_ojeckt.length>3)   ){         
           width_k  = $('#width_k').val();
           height_k = $('#height_k').val();
                  
           mySelColorBlock=$('#color_m').val();
           mySelColor=$('#color_s').val();
           mySelColorLine =$('#color_r').val();  
          $('#form_color').show();
          w=(width_k*20)+(width_k*6);
          h=(height_k*20)+(height_k*6)+35;
          
          
          
          
          $('#canvas').attr('width',w);
          $('#canvas').attr('height',h);  
          $('#canvas').attr('style','  border: 1px solid #000000;margin: 0 auto;');  
          init();
          $('#form_kino').hide();
          $('#save_hall_kino').show();
          
      }else{
         alert('Введите название зала');
      }  
  });


 $( "input[name=detal]" ).bind( "click", function( event ) {
       // console.log(this.value);
        detali=this.value;
        if (detali=='add') { 
            $( "input[name=series_line]" ).attr('style','display: block;');
        }else{
            $( "input[name=series_line]" ).attr('style','display: none;');
            $( "input[name=series_line]" ).val('0');
        }
 });
 
 
  $( "#zone_price" ).on( "click", "#button_select_color", function() {
    var data=$(this).attr('data');
    var id=$(this).attr('data')-1;
       if(id in zone_cena){
         mySelColor=$('#color_s_'+data).val();
         mySelSector=$('#sector_p_'+data).val();
         mySelPrice=$('#pricenum_p_'+data).val();
         mySelectPlaceId=id;
         zone_cena[id].color=$('#color_s_'+data).val();
         zone_cena[id].discription=$('#desript_p_'+data).val();
         zone_cena[id].sector=$('#sector_p_'+data).val();
         zone_cena[id].pricenum=$('#pricenum_p_'+data).val();
         zone_cena[id].startrow=$('#startrow_p_'+data).val();
            // console.log('=='.mySelPrice); 
              //  console.log(zone_cena);
       }else{
         mySelColor=$('#color_s_'+data).val();
         mySelSector=$('#sector_p_'+data).val();
         mySelPrice=$('#pricenum_p_'+data).val();
           
        // console.log(mySelPrice);
         mySelectPlaceId=id;
         zone("0",$('#color_s_'+data).val(),$('#desript_p_'+data).val(),$('#sector_p_'+data).val(),$('#pricenum_p_'+data).val(),$('#startrow_p_'+data).val());
         //console.log(zone_cena);
       }
     
     
     
});
/*  $( "#button_select_color" ).bind( "click", function( event ) {
       
      mySelColorBlock=$('#color_m').val();
      mySelColor=$('#color_s').val();
      mySelColorLine =$('#color_r').val(); 
  });*/
  
  $( "#add_color_price" ).bind( "click", function( event ) {
           
           var cnt=zone_cena.length+1;
           zone("0",'','','','','');
       var html='';
        html +='<input type="text"   id="color_s_'+cnt+'" value="#4ff322"  />';
        html +='<input type="text"   id="desript_p_'+cnt+'" value="" placeholder="Описание места" />';
        html +='<input type="text"   id="sector_p_'+cnt+'"  value="0" placeholder="Сектор" />C.';
        html +='<input type="text"   id="pricenum_p_'+cnt+'"  value="1" placeholder="Ценовая категория" />Ц.';
        html +='<input type="text"   id="startrow_p_'+cnt+'"  value="1" placeholder="Начало ряда" />Р.';
        html +='<input type="button" id="button_select_color" data="'+cnt+'" value="Закрепить цвет" />';
        
        $('#zone_price').append(html);
    
  });  
  
  
  $( "#save_hall_kino" ).bind( "click", function( event ) {
    var token = $('input[name=token]').val();
    
          $.ajax({  
    		url: 'index.php?route=module/designer_kino/insert&token='+token,
    		type: 'post',
    		data: 'boxes='+JSON.stringify(primer)+'&name='+name_zal+'&name_ojeckt='+name_ojeckt+'&name_ojeckt_id='+name_ojeckt_id+'&descr_ojeckt='+descr_ojeckt+'&zone='+JSON.stringify(zone_cena)+'&width='+WIDTH+'&height='+HEIGHT,
    		dataType: 'html',	
    		beforeSend: function() {

            },	
     	    success: function(html) {
                alert('Сохранения зала завершено...');
               // location=window.location;   
                             
              // document.write(html);
              console.log(html);
           },
            
    		error: function(xhr, ajaxOptions, thrownError) {
    		  
    			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                
    		}
            
    	});	

        
  });
  
  
});