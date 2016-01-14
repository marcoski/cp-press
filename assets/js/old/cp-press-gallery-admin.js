jQuery(document).ready(function(){
	var $ = jQuery;
	$('.add-image').live('click', function(event){
		event.preventDefault();
		
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			multiple: 'add',
			frame: 'post',
			library: {type: 'image'}
		});

		// When an image is selected, run a callback.
		file_frame.on('insert', function() {
			var selection = file_frame.state().get('selection');
			selection.map( function( attachment ) {

				attachment = attachment.toJSON();
				var data = {
					action: 'add_image',
					image_id: attachment.id
				};

				$.post(ajaxurl, data, function(response) {
					$("table.append-gallery").append(response.data);
				}, "json");
			});
		});

		file_frame.open();
	});
	
	$('.add-video').live('click', function(event){
		event.preventDefault();
		var data = {
			action: 'add_video_modal',
		};
		
		$.post(ajaxurl, data, function(response) {
			var videoId;
			$('<div id="add_video_modal"></div>').html(response.data).dialog({
				'title': 'Add video',
				'width': '70%',
				'buttons': {
					'Add': function(){
						$that = $(this);
						var data = {
							action				: 'add_video',
							video_id			: videoId,
							video_url			: $('#video_url').val(),
							video_title			: $('#video_title').val(),
							video_thumbnail		: $('#video_thumbnail').val()
						};

						$.post(ajaxurl, data, function(response) {
							$that.dialog('close');
							$("table.append-gallery").append(response.data);
						}, "json");
					},
					'Close': function(){
						$(this).dialog('close');
					}
				},
				'close': function(){
					$(this).remove();
				}
			});
			$.validate({
				form		: '#add_video_form',
			});
			$('#video_url').bind('validation', function(event, isValid){
				if(isValid){
					videoUrl = $('#video_url').val();
					regex = /https?\:\/\/www\.youtube\.com\/watch\?v=(\w{11})/;
					videoId = videoUrl.match(regex)[1];
					gDataVideoUrl = "http://gdata.youtube.com/feeds/api/videos/"+videoId+"?v=2&prettyprint=true&alt=jsonc"
					$.getJSON(gDataVideoUrl, function(gDataVideoJson){
						videoData = gDataVideoJson.data;
						$('#video_title').val(videoData.title);
						$('#video_thumbnail').val(videoData.thumbnail.hqDefault);
						$('#video_thumbnail_img').attr('src', videoData.thumbnail.hqDefault);
						$('#video_details').show();
					});
				}
			})
		}, "json");
	});
	
	$('.delete-image').live('click', function(event){
		dialogHtml = "<div title=\"Remove Image?\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>These image will be deleted from this gallery. Are you sure?</p></div>"
		event.preventDefault();
		
		imageId = $(event.target).attr('id').split('_')[1];
		galleryId = $('table.gallery').attr('id').split('_')[1];
		if($(this).hasClass('confirm')){
			$(dialogHtml).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					"Delete image": function() {
						data = {
							action:		'delete_image',
							image_id:	imageId,
							gallery_id:	galleryId
						};
						$.post(ajaxurl, data, function(response){
							if(response.data){
								$('#image_'+imageId).parents('tr').cpremove();
							}
						}, 'json');
						$( this ).dialog( "close" );
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		}else{
			data = {
				action:		'delete_image',
				image_id:	imageId,
				gallery_id:	galleryId
			};
			$.post(ajaxurl, data, function(response){
				if(response.data){
					$('#image_'+imageId).parents('tr').cpremove();
				}
			}, 'json');
		}
	});
	
	$('.delete-video').live('click', function(event){
		dialogHtml = "<div title=\"Remove Video?\"><p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>These video will be deleted from this gallery. Are you sure?</p></div>"
		event.preventDefault();
		
		videoId = $(event.target).attr('id');
		galleryId = $('table.gallery').attr('id').split('_')[1];
		if($(this).hasClass('confirm')){
			$(dialogHtml).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					"Delete video": function() {
						data = {
							action:		'delete_video',
							video_id:	videoId,
							gallery_id:	galleryId
						};
						$.post(ajaxurl, data, function(response){
							if(response.data){
								$('#'+videoId).parents('tr').cpremove();
							}
						}, 'json');
						$( this ).dialog( "close" );
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		}else{
			data = {
				action:		'delete_video',
				video_id:	videoId,
				gallery_id:	galleryId
			};
			$.post(ajaxurl, data, function(response){
				if(response.data){
					$('#'+videoId).parents('tr').cpremove();
				}
			}, 'json');
		}
	});
	
	$('input.cp-video-checkbox').click(function(){
		imageID = $(this).attr('id').split('-')[2];
		$p = $('#video-'+imageID);
		if(typeof($(this).attr('checked')) !== 'undefined'){
			$p.show();
		}else{
			$p.hide();
		}
	});
	
	
});