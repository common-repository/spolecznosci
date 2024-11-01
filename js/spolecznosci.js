jQuery(document).ready(function(){
	spolecznosci.LBVInit();
});

Spolecznosci = function() {
	var instance = this;
	
	this.tiphide = function() {

		jQuery.post('/wp-admin/admin-ajax.php', {action: 'videotip_action'}, function(response) {
			jQuery('#spolecznosci_videotip').fadeOut(function(){
				jQuery(this).remove();
			});
		});
	}
	
	this.init = function() {
		var html = '<div id="spolecznosci_videotip"><div id="spolecznosci_videotip_close"></div></div>';
		jQuery('#wpadminbar').append(html);
		
		var offset = jQuery('#wp-admin-bar-spolecznosci_menu').offset();
		
		jQuery('#spolecznosci_videotip').css('left',offset.left+jQuery('#wp-admin-bar-spolecznosci_menu').width());
		jQuery('#spolecznosci_videotip').fadeIn();
		jQuery('#spolecznosci_videotip_close').click(function(){
			instance.tiphide();
		});
	}
	
	this.LBVInit = function() {
		jQuery('.LBVideo a').click(function(){
			var href = jQuery(this).attr('href');
			var text = jQuery(this).text();
			var YTID = href.split('=')[1];
			
			jQuery(this).css('opacity','.5')
			
			console.log(YTID);
			
			var html = '<div id="video_overlay"></div>';
			jQuery('body').append(html);
			var overlay = jQuery('#video_overlay');
			overlay.click(function(){
				instance.LBVDestroy();
			});
			overlay.fadeTo('fast',.8,function(){
				var html = '<div id="video_player">';
				html += '<div id="ico_close"></div>';
				html += '<div class="title">'+text+'</div>';
				html += '<iframe class="youtube-player" type="text/html" width="640" height="480" src="http://www.youtube.com/embed/'+YTID+'" frameborder="0"></iframe>';
				html += '</div>';
				
				jQuery('body').append(html);
				jQuery('#ico_close').click(function(){
					instance.LBVDestroy();
				});
			});
			
			return false;
		});
	}
	
	this.LBVDestroy = function() {
		jQuery('#video_overlay').remove();
		jQuery('#video_player').remove();
	}
	
}

var spolecznosci = new Spolecznosci();