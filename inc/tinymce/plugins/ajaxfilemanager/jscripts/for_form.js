function selectFile(url)
{
     var fileName = '';
     if(url.length == 0)
     {
      fileName =  ''; 
     }else
     {
        url = url.substring(0, (url.indexOf("?") == -1) ? url.length : url.indexOf("?"));
        var pos = url.lastIndexOf("");
        if(pos != -1)
        {
             fileName = url.substr(pos,url.length);
        }else
        {
           fileName = url;
        }
     }
      window.opener.document.getElementById(elementId).value = fileName;
      window.close() ;


}

