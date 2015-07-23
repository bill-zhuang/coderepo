function callAjax(url, para)
{
	$.ajax({
		url: url,
		type: 'post',
		data: { 
			'send_para' : para,
			},
		dataType: 'json',
		success: function(strResult){	
			console.log(strResult);
			if(strResult == '') {
				alert('error info');
				return false;
			} else {
				
			}
		},
			
		error: function(XMLHttpRequest, textStatus, errorThrown) {
	    	console.log("XMLHttpRequest.status="+XMLHttpRequest.status+
	        	   	    "\nXMLHttpRequest.readyState="+XMLHttpRequest.readyState+
	                  	"\ntextStatus="+textStatus);
			}
	});
}

function httpRequest(url)
{
	var xmlhttp;
	
    try {
        xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
    } catch(e) {
        try {
            xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
        } catch(e) {
            try {
                xmlhttp = new XMLHttpRequest();
            } catch(e) {
            	
			}
        }
    }
    
    xmlhttp.open("get", url);
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4) {
            if(xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                if(result.length > 0) {
                    //document.getElementById('xxxx').style.display="block";
                    document.getElementById('xxxx').innerHTML=result;          
                }
            } else {
            	
            }
        }
    }
	
    xmlhttp.send(null);
    return;
}

//real-time refresh
var seconds = 30;

setInterval(function(){
    $.ajax({
        url: 'xxx',
        type: 'post',
        data: { 
			'xxxx' : 'xxxx',
		},
        dataType: 'json',
		success: function(strResult){	
			console.log(strResult);
			if(strResult == '') {
				alert('获取信息失败');
				return false;
			} else {
				
			}
		},
			
		error: function(XMLHttpRequest, textStatus, errorThrown){
        	console.log("XMLHttpRequest.status="+XMLHttpRequest.status+
            	   	    "\nXMLHttpRequest.readyState="+XMLHttpRequest.readyState+
                      	"\ntextStatus="+textStatus);
			}
    });
}, seconds * 1000)

function popupwindow(url)
{
	window.open(url, 'newwindow', 'height=800, width=550, top=0,left=200, toolbar=no, menubar=no, scrollbars=yes, resizable=no,location=no, status=no');
}