<div class="container">
    <div class="row" style="margin-top: 80px;margin-bottom: 20px">
        <div class="col-md-8">
            <form id="form">
              <div class="form-group">
<div class="form-control" id="editor">&lt;?php

</div>
              </div>
              <div class="form-group">
                <p style="text-align: center"><input class="btn btn-primary" type="submit" value="提交"></p>
              </div>
            </form>
        </div>
        <div class="col-md-4">
        
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace.js"></script>
<script>
var editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.getSession().setMode("ace/mode/php");

var originom = document.getElementById('editor');
originom.style.fontSize = '14px';



var $form = $('#form');
$form.on('submit', function(e) {
	e.preventDefault();
	var code = editor.getValue();
	$.ajax({
	    url: '/console',
	    type: 'post',
	    data: 'code=' + encodeURIComponent(code),
	})
	.done(function(result) {
		 console.log(result)
	})
})





</script>