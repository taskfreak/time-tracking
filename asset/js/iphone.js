var app = {
  login:function ($form) {
    $.ajax({
      type:$form.attr("method"), 
      url:$form.attr("action"),
      dataType:"html", 
      data:$form.serialize(),
      success: function (data){
      	// $('body').append(data);
      	jQT.appendPages(data, 'ignore');
      },
      complete:function (req) {
        if (req.status === 200 || req.status === 304) {
          app.bindList('list-st0');
          app.bindList('list-st1');
          app.bindList('list-st2');
          jQT.goTo('#main', 'fade');
          $(".logout").tap(function (e) {
		    return app.logout();
		  });
		  // $('.details').css('color','#f00').tap(app.loadTaskDetails(this));
        } else {
          alert("There was an error logging in. Try again.");
        }
      }
    });
    return false;
  },
  logout:function() {
  	$.ajax({
      type:'get', 
      url:APP_LOGOUT,
      success:function() {
      },
      complete:function() {
      	$('#login').bind('pageAnimationEnd', function(event, info){
            $('.dyn').remove();
            $(this).unbind('pageAnimationEnd');
        })
      	jQT.goTo('#login','fade');
      }
	});
	return false;
  },
  bindList: function(id) {
  	console.log('wiring list #'+id);
  	$('#'+id).bind('pageAnimationEnd',function(){
  		app.clearView();
  	});
  	$('#'+id+' a.details').tap(function() {
  		var td = $(this).attr('rel');
  		console.log('load '+td+' details ...');
  		app.view(td);
  	});
  },
  /* VIEW TASK */
  view:function(id) {
  	console.log('lets view dis #'+id+' bitch');
  	$.ajax({
  		type:'get',
  		url:APP_VIEW+id,
  		dataType:'json',
  		success: function(data) {
  		 	// details
  			$('#dv_id').val(data.info.id);
  			$('#dv_title').html(data.info.title);
  			$('#dv_note').html(data.info.note);
  			$('#dv_deadline').html(data.info.deadline);
  			$('#dv_priority').html(data.info.priority);
  			$('#dv_spent').html(data.info.total);
  			// time stamps
  			var ll = $('#details-spent ul');
  			$.each(data.spent, function(k,v) {
			  ll.append('<li>'+k+'<small>'+v+'</small></li>');
			});
			// action (running or not)
			if (data.running) {
				$('#dv_action').val('stop');
				$('#dv_stop').removeClass('hide');
			} else {
				$('#dv_action').val('start');
				$('#dv_start').removeClass('hide');
			}
  		},
  		complete: function() {
  			// jQT.goTo('#view','slide');
  		}
  	});
  	return false;
  },
  /* REACT TO ACTION FROM VIEW */
  viewAction:function(lnk) {
  	var $form = $(lnk).closest("form");
  	var $id = $form.attr('id').substr(8);
  	console.log('ID1='+$id);
  	$.ajax({
      type:'post', 
      url:$form.attr('action'),
      data:$form.serialize(),
      dataType:"html",
      success: function (data) {
      	el = $('#running')[0];
      	if (el) {
      		$('#running').replaceWith(data);
      	} else {
      		$('#main .toolbar').after(data);
      	}
      },
      complete:function (req) {
        if (req.status === 200 || req.status === 304) {
        	jQT.goBack();
        } else {
          alert("Error updating task !");
        }
      }
    });
    return false;
  },
  /* CLEAR VIEW (after going back to list) */
  clearView:function() {
  	$('#dv_id').val(0);
  	$('#dv_action').val('');
	$('#dv_title').html('...');
	$('#dv_note').html('...');
	$('#dv_deadline').html('...');
	$('#dv_priority').html('...');
	$('#dv_spent').html('...');
	$('#details-spent ul').empty();
	$('#dv_start').addClass('hide');
	$('#dv_stop').addClass('hide');
  },
  /* EDIT TASK */
  edit:function() {
  	var id = $('#dv_id').val();
  	console.log('lets edit dis #'+id+' bitch');
  	$.ajax({
  		type:'get',
  		url:APP_EDIT+id,
  		dataType:'json',
  		success: function(data) {
  			$('#i_id').val(data.id);
  			$('#i_title').val(data.title);
  			$('#i_note').val(data.note);
  			$('#i_deadline').val(data.deadline);
  			$('#i_priority').val(data.priority);
  			$('#i_status').val(data.status);
  		},
  		complete: function() {
  			// jQT.goTo('#edit','flip');
  		}
  	});
  	return false;
  },
  /* SAVE TASK */
  editAction:function ($form) {
  	var i = $('#i_id').val();
  	var w = $('#i_status').val();
    $.ajax({
      type:$form.attr("method"), 
      url:$form.attr("action"),
      dataType:"html", 
      data:$form.serialize(),
      success: function (data){
      	jQT.goBack();
      },
      complete:function (req) {
        switch (req.status) {
        case 200:
        case 304:
        	if (i) {
        		// task been updated
	        	app.view(i);
	        }
	        // jQT.goBack();
			break;
		default:
          alert("There was an error saving the task.");
        }
        window.setTimeout(function() {
	        app.reloadMain();
	        app.clearEdit();
	    },500);
      }
    });
    return false;
  },
  /* CLEAR EDIT FORM */
  clearEdit: function() {
  	$("#edit :input").val('');
	$("#i_priority").val(5);
	$("#i_status").val(0);
	return true;
  },
  /* ACTION ON CURRENT TASK */
  taskAction:function(lnk) {
	var act = lnk.href.substr(lnk.href.lastIndexOf('#')+1);
  	$('#runaction').val(act);
  	var $form = $('#running');
  	$.ajax({
      type:'post', 
      url:$form.attr('action'),
      data:$form.serialize(),
      dataType:"html",
      success: function (data) {
      	$('#running').replaceWith(data);
      },
      complete:function (req) {
        if (req.status === 200 || req.status === 304) {
        } else {
          alert("Error updating task !");
        }
      }
    });
    return false;
  },
  reloadMain:function() {
  	$.ajax({
  		type:'get',
  		url:APP_RELOAD,
  		dataType:'html',
  		success: function(data) {
  			jQT.appendPages(data, 'ignore');
  			// -TODO- bind links ?	
  		}
  	});
  }
};


jQuery(function () {
	// login form
	$("#login a.login").tap(function (e) {
		var $form = $(this).closest("form");
		return app.login($form);
	});
	$("#login").submit(function (e) {
		var $form = $(this);
		return app.login($form);
	});
	// logout button
	$(".logout").tap(function (e) {
		return app.logout();
	});
	// view actions
	$(".react").tap(function (e) {
		return app.viewAction(this);
	});
	// edit button (on view)
	$("#details a.modify").click(function(e){
		app.edit();
	});;
	// edit form
	$("#edit a.save").tap(function (e) {
	    var $form = $(this).closest("form");
	    return app.editAction($form);
	});
	$("#edit").submit(function (e) {
	    var $form = $(this);
	    return app.editAction($form);
	});
	$('#edit a.back').click(function (e) {
	    return app.clearEdit();
	});
  	// bind list (if logged in)
  	app.bindList('list-st0');
	app.bindList('list-st1');
	app.bindList('list-st2');
});