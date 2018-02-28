var ur = "";
//Publisher autocomplete and location fetch
function bindPublisherAutocomplete(context, workURL) {							
	//publisher enable/disable fields
	$(".pub_locations", context).prop("disabled", "disabled");
	
	//Publisher autocomplete
	$(function() {
		$( "#pubName", context).autocomplete({
			//source: 'http://localhost<?=$this->url('get_work_details')?>?autofor=publisher',
			source: ur + workURL + '?autofor=publisher',
			autoFocus:true,
			select: function(event, ui){
                $('#pubName', context).val(ui.item.label);
                $('#pubId', context).val(ui.item.id);
				$(".pub_locations", context).prop("disabled", false);
                return false;
            }
		});
	});	
	$('#pubName', context).on('autocompleteselect', function (e, ui) {
		var publisher_Id = ui.item.id;
		//Resizing text field to make selected publisher visible
		var selected_val = ui.item.label.length;
		$('#pubName', context).css('width', selected_val + 'ch');
		i=0;
		//arr = [];
		$.ajax({
        method: 'post',
        //url: 'http://localhost<?=$this->url('get_work_details')?>',
		url: ur + workURL,
		data: {
			publisher_Id: publisher_Id
		},
		dataType: "json", 
        cache: false,
        success: function(data)
        {
			$(".pub_locations", context).html('');
			$.each(data.publoc, function (key, val) {
				//if(val.id !== null)
				//{
                $(".pub_locations", context).append('<option id="' + val.id + '" value="' + val.id + '">' + val.label + '</option>');
				$("#publoc_id", context).eq(i).val(val.id);
				$("#pubLocation", context).css('width', 'auto');
				//}
				//arr.push(val.id);
				i++;
            })
			//console.log(arr);
			//$("#publoc_id", context) = arr;
		},
		error: function () { 
            $(".pub_locations", context).html('<option id="-1">none available</option>');
        }
        });
    });	
}

//Agent Autocomplete
function bindAgentAutocomplete(context, workURL) 
{						
	//agent enable/disable fields
	$("#agent_FirstName", context).prop("disabled", "disabled");
	$("#agent_LastName", context).prop("disabled", "disabled");
	$("#agent_AlternateName", context).prop("disabled", "disabled");
	$("#agent_OrganizationName", context).prop("disabled", "disabled");
	
	$('#agent_type',context).on('change', function() {
		$("#agent_FirstName", context).prop("disabled", false);
		//agent first name autocomplete
		$(function() {
			$( "#agent_FirstName", context).autocomplete({
				//source: 'http://localhost<?=$this->url('get_work_details')?>?autofor=agent',
				source: ur + workURL + '?autofor=agent',
				autoFocus:true,
				select: function(event, ui){
					$('#agent_FirstName', context).val(ui.item.label);
					$('#agentId', context).val(ui.item.id);				
					return false;
				}
			});
		});	
	});
	$('#agent_FirstName', context).on('autocompleteselect', function (e, ui) {
		//Resizing text field to make selected agent first name visible
		var agent_fn = ui.item.fname.length + 5;
		var agent_ln = ui.item.lname.length + 5;
		$('#agent_FirstName', context).css('width', agent_fn + 'ch');
		if(ui.item.lname != '') {
			$("#agent_LastName", context).prop("disabled", false);
			$("#agent_LastName", context).val(ui.item.lname);
			//Resizing text field to make selected agent last name visible
			$('#agent_LastName', context).css('width', agent_ln + 'ch');
		}
		if(ui.item.alternate_name != '') {
			$("#agent_AlternateName", context).prop("disabled", false);
			$("#agent_AlternateName", context).val(ui.item.alternate_name);
		}
		if(ui.item.organization_name != '') {
			$("#agent_OrganizationName", context).prop("disabled", false);
			$("#agent_OrganizationName", context).val(ui.item.organization_name);
		}		
    });	
}

//WorkType Autocomplete
function bindWorkTypeAttributes(context,workURL)
{
	$('#Citation *').not('.ig').remove();
	//$(".content_box a").not(".button")
	var worktype_Id = $('#edit_work_type').val();
	$.ajax({
        method: 'post',
        //url: 'http://localhost<?=$this->url('get_work_details')?>',
		url: ur + workURL,
		data: {
			worktype_Id: worktype_Id
		},
		dataType: "json", 
        cache: false,
        success: function(data)
        {
			$.each(data.worktype_attribute, function (key, val) {				
				$("<div />").attr("class","form-group optiondiv").attr("id","optiondiv").appendTo("#Citation");
				$("<label />").attr("class","col-xs-2 control-label").text(val.field).appendTo("#Citation");
				//$("</label>").appendTo("#divforoptions");
				$("<div />").attr("id","optionfieldsdiv").appendTo("#Citation");
				// append input control at end of form
				if(val.type == 'Textarea')
				{
					$("<textarea />")
						.attr("class","form-control")
						.attr("id", val.field)
						.attr("name", 'wkatid,' + val.id)
						.appendTo("#Citation");
					//$("</textarea>").appendTo("#divforoptions");
				}
				if(val.type == 'Text')
				{
					$("<input />")
						.attr("type","text")
						.attr("class","form-control")
						.attr("id", val.field)
						.attr("name", 'wkatid,' + val.id)
						.appendTo("#Citation");
				}
				if(val.type == 'RadioButton')
				{
					$('<div class="radio">' +
					'<label class="radio"><input type="radio" name="wkatid,' + val.id + '" value="true" />True</label>' + 
					'<label class="radio"><input type="radio" name="wkatid,' + val.id + '" value="false" />False</label>' + 
					'</div>').appendTo("#Citation");
				}
				if(val.type == 'Select')
				{
					var container = $('<div/>');
					$("<input />")
						.attr("type","text")
						.attr("class","form-control Attributeoption")
						.attr("id", val.field + ':' + val.id)
						.attr("name", 'wkatid,' + val.id)
						.appendTo(container);
					$("<button data-toggle='modal' data-target='#pubLookup'/>")
						.attr("class","btn btn-default optionLookupBtn")
						.attr("id","optionLookupBtn")
						.attr("data-toggle","modal")
						.attr("data-target","optionsLookup")
						.attr("value","Lookup")
						.text("Lookup")
						.appendTo(container);	
					container.appendTo('#Citation');
				}	
            })			
			$(".optionLookupBtn").on('click', function(e){
				// show Modal
				var lookupBtn = this;
				attr_option_lookup = $(lookupBtn).prev();
				$("#optionsLookup").modal('show');
				$('#optionsLookup').on('shown.bs.modal', function () {
					$('#lookupOption').focus()
					$('.option_search').on('click', function(e) {					
						//var attribute_Id = $(lookupBtn).prev().attr('id');
						var attribute_Id = attr_option_lookup.attr('id');
						var option = $('#lookupOption').val();
						$.ajax({
							method: 'post',
							//url: 'http://localhost<?=$this->url('get_work_details')?>',
							url: ur + workURL,
							data: {
								option: option,
								attribute_Id: attribute_Id
							},
							dataType: "json", 
							cache: false,
							success: function(data)
							{
								$(".option_results",context).html('');
								$(".option_results",context).append('<p>Search Results</p>');
								$.each(data.attribute_options, function (key, val) {
									$(".option_results",context).append('<p><a name="'+val.id+'" href="'+ val.title +'" class="link_options">' + val.title + '</a></p>');
								})
							},
							error: function () { 
								$(".option_results",context).append('<p>None available</p>');
							}
						});	
						$(context).off('click', '.link_options');
						$(context).on("click", ".link_options", function(e) {
							var linkval = $(this).attr('href');
							attr_option_lookup.val(linkval);
							attr_option_lookup.attr('name', attr_option_lookup.attr('name') + 'optid,' + $(this).attr('name'));
							//console.log(attr_option_lookup.attr('name'));
							$('#lookupOption').val('');
							$(".option_results",context).html('');	
							$('.option_lookup_close').trigger('click');																
							return false;
						});
					});
				});
				return false;
			});
		},
		error: function (data) { 
            $("#Citation", context).html('<p>No Options</p>');
			//alert('error');
        }
    });
	return false;
}

//Add classification hierarchy
_select = '';
function bindSourceClassification(that, context, workURL)
{
	var to_add_row = $(that).closest("tr");
	//to_add_row.children('td',context).children('select',context).eq(0).css('background-color','#8ec252');
	fl_changed = $(that).val();
	var no_of_fl_parent = to_add_row.find('.select_source_fl',context).length;
	for(var i = 0;i < no_of_fl_parent; i++)
	{
		if(to_add_row.find('.select_source_fl',context).eq(i).val() === fl_changed)
		{
			change_idx = i;
			folder_Id = to_add_row.find('.select_source_fl',context).eq(i).val();
			break;
		}
	}
		
	if(folder_Id === "")
	{
		to_add_row.find('.source_fl_col',context).eq(0).nextAll('.source_fl_col',context).remove();
		to_add_row.find('.select_source_fl',context).eq(0).val('');
	}
	else
	{
		to_add_row.find('.source_fl_col',context).eq(change_idx).nextAll('.source_fl_col',context).remove();
	}
	
	//no_of_fl_parent = $('.select_source_fl',context).length;
	no_of_fl_parent = to_add_row.find('.select_source_fl',context).length;
	$.ajax({
		method: 'post',
		//url: 'http://localhost<?=$this->url('get_work_details')?>',
		url: ur + workURL,
		data: {
			folder_Id: folder_Id
		},
		dataType: "json", 
		cache: false,
		success: function(data)
		{						
			if(data.folder_children.length > 0)
			{
				to_add_row.find('.source_fl_col',context).eq(no_of_fl_parent-1).after('<td class="source_fl_col" name="source_fl_col" id="source_fl_col"/>');
				
				_select = $('<select class="form-control select_source_fl select2" name="select_source_fl[]">');
				to_append = $('<option value=""></option>');
				$.each(data.folder_children, function (key, val) {
					to_append += '<option value="'+val.id+'">'+val.text_fr+'</option>';
				});
				_select.append($('<option />'));
				_select.append(to_append);

				to_add_row.find('.source_fl_col',context).eq(no_of_fl_parent).append(_select);
				to_add_row.find('.source_fl_col',context).eq(no_of_fl_parent).append('</select>');
					
				_select.on('change',function(){
					bindSourceClassification(this,document,workURL);
					return false;
				});	
			}
		},
		error: function (data) { 
			//$("#Classification", context).html('<p>No Options</p>');
		}
	});	
	return false;	
}

function bindParentWork(context,workURL)
{
	$('.pr_work_results').html('');
	$('#parent_title',context).focus()
	   //.css('background', '#8ec252')
	   $('.parent_lookup_search',context).on('click', function(e) {	
	       //alert("parent lookup");
		   var lookup_title = $('#parent_title',context).val();
		   if (lookup_title != "") {
		       //alert("value is " + lookup_title);
			   $.ajax({
                   method: 'post',
                   //url: 'http://localhost<?= $this->url('get_work_details') ?>',
				   url: ur + workURL,
                   data: {
                       lookup_title: lookup_title
                   },
                   dataType: "json",
                   cache: false,
                   success: function(data) {
				       if ((data.prnt_lookup.length) > 0) {
                           $('#parent_title').val('');
                           var prworks_result_table = '<table style="font-size:10pt; border-collapse: separate; border-spacing: 10px;" id="prworks_result_table">' +
                                                      '<tr><th>Work Title</th><th>Type</th></tr>';
                           $.each(data.prnt_lookup, function (key, val) {
                               prworks_result_table += '<tr><input type="hidden" name="parent_work_id" id="parent_work_id" value="">' +
                                                       '<td><a name="' + val.id + '" href="" class="prwork_link" value="' + val.title + '">' + 
													   val.title + '</a></td><td>' + val.type + '</td></tr>';
                           });
                           prworks_result_table += '</table>';
						   $('.pr_work_results').append(prworks_result_table);
                       } else {
                             $('#parent_title',context).val('');
							 $('.pr_work_results').append('<p>No records found</p>');
                       }
                    
                   },
                   error: function(data) {

                   }
               });
			   //$(context).off('click', 'prwork_link');
			   $(context).on("click", ".prwork_link", function(e) {
			       var pr_linkval = $(this).attr('name');
				   var pr_labelval = $(this).attr('value');
				   $('.pr_work_div', context).text(pr_labelval);
				   $('#pr_work_lookup_id',context).val(pr_linkval);
				   $('.pr_work_div', context).append('<button type="button" class="btn btn-default parent_Chng_Btn" name="parent_changeBtn" id="parent_changeBtn" ' +
				                                     'data-toggle="modal" data-target="#parentLookup_modal">Change</button>')
                   $('.pr_work_div', context).append('<button type="button" class="btn btn-default parent_Rmv_Btn" name="parent_removeBtn" ' + 
				                                      'id="parent_removeBtn">Remove</button>')
				   $('.pr_work_results').html('');
				   $('.option_lookup_close').trigger('click');
				       return false;
		       });
		   }
		   return false;
       });
	   return false;
}