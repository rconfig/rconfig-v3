  $(window).load(function() {
  // alert(document.URL);
	$('#noticeBoard').hide();
	var rid = location.search.split('rid=')[1]
	var username = location.search.split('username=')[1]
	var password = location.search.split('password=')[1]
	if (!username && !password){
		runDownloadScript(rid, "0", "0");
	}else{
		runDownloadScript(rid, username, password);
	}
  });
  
  
  
function runDownloadScript(rid, username, password){
    $.ajax({
        async: false, // prevent an async call
        url: 'lib/ajaxHandlers/ajaxDownloadNow.php?rid='+rid+'&username='+username+'&password='+password,
        data: {},
        dataType: "json",
		 complete: function(){
				$('#loading').fadeOut(500);
				$('#loading').remove();
				$('#noticeBoard').show();
            },
        success:function(data){
			$.each(data, function(i, item) {
				// alert(item)
				$('#noticeBoard').append('<div class="noticeDiv">'+item+'</>');
			});
        }
    });
}