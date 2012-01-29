jQuery().ready(function(){
	jQuery("#list").jqGrid({
		url:jQGajax.ajaxurl+'?action=jqga',
		datatype:"xml",
		colNames:['ИД','Заголовок','Автор','Инициатор','Ответственный','Срок/Дедлайн','Объект','Категория дел','Приоритет','Участники','Дата'],
		colModel:[
			{name:'id', index:'id', width:30},
			{name:'post_title', index:'post_title', width:200, formatter: 'link'},
			{name:'post_author', index:'post_author', hidden: true},
			{name:'initiator', index:'initiator', width:50},
			{name:'responsible', index:'responsible', width:50},
			{name:'date_end', index:'date_end', width:50, align:'center', sorttype:'date', formatter:'date', datefmt:'d.m.Y'},
			{name:'object', index:'object', width:50},
			{name:'cat', index:'cat', width:50},
			{name:'prioritet', index:'meta', width:30},
			{name:'tag', index:'tag', width:50},
			{name:'post_date', index:'post_date', width:50, align:'center'},
		],
		pager:jQuery('#pager'),
		mtype:'POST',
		rowNum:15,
		autowidth:true,
		rowList:[15,30,50],
		height: 'auto',
		sortname:'post_date',
		viewrecords:true,
		sortorder: "desc",
		grouping:false, 
			groupingView : { 
			groupField : ["prioritet"], 
			groupColumnShow : [false]
		},
		caption:jQGajax.caption,
    		footerrow: false,
    		userDataOnFooter: false
	}).navGrid('#pager',{edit:false,add:false,del:false});
	jQuery("#list").jqGrid('filterToolbar');
	
	jQuery("#chngroup").change(function(){
	var p = $('#chngroup').val();
	if(vl) {
		if(vl == "clear") {
			jQuery("#list").jqGrid('groupingRemove',true);
		} else {
			jQuery("#list").jqGrid('groupingGroupBy',vl);
		}
	}
	$('#list').trigger('reloadGrid');
	});
});