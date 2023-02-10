//CKEDITOR.plugins.add('galleryinsert', {
//    init: function(editor) {
//        editor.ui.addButton("GalleryInsert", {
//            label: "GalleryInsert",
//            icon: this.path+'icon.png'
//        });
//    }
//});
CKEDITOR.plugins.add('galleryinsert', {
	init: function(editor) {
		//popup_params={'width':650,'height':670,'alwaysRaised':yes,'dependent':yes,'toolbar':yes,'menubar':no,'resizable':yes,'scrollbars':yes,'status':no};
		popup_params={'width':650,'height':670};
		open_url='plugin.php?p=GalleryInsert&popup=1';
		editor.addCommand('GalleryInsertCommand', {
			exec:function(editor){
				//$.toolbarPopup(open_url,popup_params);
				var p_win = window.open(open_url,'dc_popup',
				'alwaysRaised=yes,dependent=yes,toolbar=yes,height=670,width=650,'+
				'menubar=no,resizable=yes,scrollbars=yes,status=no');
				p_win.focus();
				//$.toolbarPopup(open_url,popup_params);
			}
		});
		editor.ui.addButton("GalleryInsert", {
			label: "GalleryInsert",
			icon: this.path+'icon.png',
			command: 'GalleryInsertCommand',
			toolbar: 'insert'
		});
	}
});
