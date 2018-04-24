var ur = "";
$.fn.textWidth = function(text, font) {
					if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
					$.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));					
					return $.fn.textWidth.fakeEl.width();
};

//Publisher autocomplete and location fetch
function bindPublisherAutocomplete(context, workURL) {
	//publisher enable/disable fields
	$("#pubLocation", context).prop("disabled", "disabled");

	//Publisher autocomplete
	$("#pubName", context).autocomplete({
        source: function (request, response) {            
            $.ajax({
                url: ur + workURL + '?autofor=publisher',
                type: "get",
                dataType: "json",
                cache: false,
                data: {
                    term : $("#pubName", context).val(),
                },
				success: function (data) {
					if(!data.length){
						/*var to_add = $('<p>No matches found. </p>'+
						             '<a type="button" class="addNewPubLink" href="#addPublisherLookup" data-toggle="modal" ' + 
						                 'style="text-decoration: underline;">Add New</a>');*/
						var to_add = $('<p>No matches found. </p>'+
						             '<a type="button" class="addNewPubLink" ' + 
						                 'style="text-decoration: underline;">Add New</a>');
						$('#pubName', context).nextAll().remove()
						$('#pubName', context).after(to_add);
					}
					else{
						// normal response
						response($.map(data, function (item) {
							return {
								label: item.label,
                                value: item.value,
								id:    item.id
							}
						}));
					}
				},
			});
		},
		open: function(event, ui) {
			$('.ui-autocomplete').append('<a type="button" class="addNewItemPubLink" ' + 
										   'style="text-decoration: underline; color:blue;" data-value="'+ $(this).val() +'"' + 
										   'data-ele=""' + '>Add New</a>'); //Add new link at end of results
			
			$('.addNewItemPubLink').on('click',function(){
				var lnk = $(event.target);
				addNewPublisher(context,workURL,lnk);
				return false;
			});
		 },
        minLength: 3,
        select: function(event, ui) {
			if(ui.item.label == "No matches found") {
				$('#pubName', context).val(ui.item.value);
				//$('#pubName', context).after('<a>Add New</a>'); 				
				return false;
			}
			else{
				$('#pubName', context).val(ui.item.label);				
				//Resizing text field to make selected publisher visible
				var pbLen = $(this).textWidth(ui.item.label) + 35;
				$('#pubName', context).css('width',pbLen + 'px');				
				$('#pubId', context).val(ui.item.id);
				//$("#pubLocation", context).prop("disabled", false);
				$(".pub_locations", context).prop("disabled", false);
				return false;
			}
		}
    });
	$('#pubName', context).on('autocompleteselect', function(e, ui) {
		var publisher_Id = ui.item.id;
		i = 0;
		//arr = [];
		$.ajax({
			method: 'post',
			url: ur + workURL,
			data: {
				publisher_Id: publisher_Id
			},
			dataType: "json",
			cache: false,
			success: function(data) {
				$(".pub_locations", context).html('');
				$.each(data.publoc, function(key, val) {
					$(".pub_locations", context).append('<option id="' + val.id + '" value="' + val.id + '">' + val.label + '</option>');
					$("#publoc_id", context).eq(i).val(val.id);
					
					//Setting select to auto width to make selected publisher location visible
					$(this).closest(".pub_locations", context).css('width', 'auto');

					i++;
				})
			},
			error: function() {
				$("#pubLocation", context).html('<option id="-1">none available</option>');
			}
		});
	});
}

//add new publisher
function addNewPublisher(context,workURL,lnk) {
	//var lnk = $('.addNewPubLink', context);
	if (lnk == $('.addNewItemPubLink', context)) {
		$('.addNewPubLink', context).remove();
	}
	var typedText = lnk.closest('tr').find('#pubName').val();
	lnk.closest('tr').find('#pubName').val("");

	$('#addPublisherLookup').modal('show'); 
	$('#newpublisher').val(typedText);
	$('#addNewPub').unbind('click').on('click', function(e) {
		$.ajax({
			method: 'post',
			url: ur + workURL,
			data: {
				addAction: 'addNewPublisher',
				pubName: $('#newpublisher').val(),
				pubLocation: $('#addpublisherloc').val()
			},
			dataType: "json",
			cache: false,
			success: function(data) {
				if (Object.keys(data.newPublisher).length > 0) {
						$('#newpublisher').val('');
						$('#addpublisherloc').val('');
						$(".add_new_pub_close").click();
						lnk.closest('tr').find('#pubId', context).val(data.newPublisher.pub_id);
						lnk.closest('tr').find('#pubName', context).val(data.newPublisher.pub_name);
						//Resizing text field to make selected publisher visible
						var pbLen = lnk.closest('tr').find('#pubName', context).textWidth(data.newPublisher.pub_name) + 35;
						lnk.closest('tr').find('#pubName', context).css('width',pbLen + 'px');
						if(typeof(data.newPublisher.pubLoc_id) != "undefined" && data.newPublisher.pubLoc_id !== null) {
							lnk.closest('tr').find('#pubLocation', context).prop("disabled", false);
							lnk.closest('tr').find('.pub_locations', context).append('<option id="' + data.newPublisher.pubLoc_id + 
																			'" value="' + data.newPublisher.pubLoc_id + '">' + 
																			 data.newPublisher.pub_loc + '</option>');
							lnk.closest('tr').find('#publoc_id', context).eq(0).val(data.newPublisher.pubLoc_id);
							
							//Setting select to auto width to make selected publisher location visible
							lnk.closest('tr').find('.pub_locations', context).css('width', 'auto');
						}
						lnk.closest('tr').find('#pubName', context).nextAll().remove();
				}
			},
			error: function() {
				$(".option_results", context).append('<p>None available</p>');
			}
		});
		return false;
	});
}

//Agent Autocomplete
function bindAgentAutocomplete(context, workURL) {
	//agent enable/disable fields
	$("#agent_FirstName", context).prop("disabled", "disabled");
	$("#agent_LastName", context).prop("disabled", "disabled");
	$("#agent_AlternateName", context).prop("disabled", "disabled");
	$("#agent_OrganizationName", context).prop("disabled", "disabled");

	$('#agent_type', context).on('change', function() {
		$("#agent_LastName", context).prop("disabled", false);
		
		//Agent autocomplete
		$("#agent_LastName", context).autocomplete({
			source: function (request, response) {            
				$.ajax({
					url: ur + workURL + '?autofor=agent',
					autoFocus: true,
					type: "get",
					dataType: "json",
					cache: false,
					data: {
						term : $("#agent_LastName", context).val(),
					},
					success: function (data) {
						if(!data.length){
							var to_add = $('<p>No matches found. </p>'+
											'<a type="button" class="addNewAgLink" href="#addAgentLookup" data-toggle="modal" ' + 
											'style="text-decoration: underline;">Add New</a>');
							$('#agent_LastName', context).nextAll().remove()
							$('#agent_LastName', context).after(to_add);
						}
						else{
							// normal response
							response($.map(data, function (item) {
								return {
									label: item.label,
									lname: item.lname,
									fname: item.fname,
									alternate_name: item.alternate_name,
									organization_name: item.organization_name,
									id:    item.id
								}
							}));
						}
					},
				});
			},
			open: function(event, ui) {
				$('.ui-autocomplete').append('<li><a class="addNewItemAgLink" ' + 
						                 'style="text-decoration: underline; color:blue" data-value="'+ $(this).val() +'"' + 
										   'data-ele=""' + '>Add New</a></li>'); //Add new link at end of results
				$('.addNewItemAgLink').on('click',function(){
					var lnk = $(event.target);
					addNewAgent(context,workURL,lnk);
					return false;
				});
			},
			minLength: 3,
			select: function(event, ui) {
				if(ui.item.label == "No matches found") {
					$('#agent_LastName', context).val("");
					//$('#pubName', context).after('<a>Add New</a>'); 				
					return false
				}
				else{
					var arr = ui.item.label.split(' FN: ');
					ui.item.label = arr[0];
					$('#agent_LastName', context).val(ui.item.label);
					$('#agentId', context).val(ui.item.id);
					return false;
				}
			}
		});
	});
	$('#agent_LastName', context).on('autocompleteselect', function(e, ui) {	
		//Resizing text field to make selected agent last name visible
		var agent_ln = $('#agent_LastName').textWidth(ui.item.lname) + 25;	//ui.item.fname.length + 5;
		var agent_fn = $('#agent_FirstName').textWidth(ui.item.fname) + 25;	//ui.item.lname.length + 5;
		$('#agent_LastName', context).css('width', agent_ln + 'px');
		if (ui.item.fname != '') {
			$("#agent_FirstName", context).prop("disabled", false);
			$("#agent_FirstName", context).val(ui.item.fname);
			//Resizing text field to make selected agent first name visible
			$('#agent_FirstName', context).css('width', agent_fn + 'px');
		}
		if (ui.item.alternate_name != '') {
			$("#agent_AlternateName", context).prop("disabled", false);
			$("#agent_AlternateName", context).val(ui.item.alternate_name);
		}
		if (ui.item.organization_name != '') {
			$("#agent_OrganizationName", context).prop("disabled", false);
			$("#agent_OrganizationName", context).val(ui.item.organization_name);
		}
	});
}

//add new agent
function addNewAgent(context,workURL,lnk) {
	//var lnk = $('.addNewAgLink', context);
	if (lnk == $('.addNewItemAgLink', context)) {
		$('.addNewAgLink', context).remove();
	}
	var typedText = lnk.closest('tr').find('#agent_LastName').val();
	lnk.closest('tr').find('#agent_LastName').val("");
	
	$('#addAgentLookup').modal('show');
	$('#newagentlastname').val(typedText);
	$('#addNewAg').unbind('click').on('click', function(e) {
		$.ajax({
			method: 'post',
			url: ur + workURL,
			data: {
				addAction: 'addNewAgent',
				agFName: $('#newagentfirstname').val(),
				agLName: $('#newagentlastname').val(),				
				agAltName: $('#newagentaltname').val(),
				agOrgName: $('#newagentorgname').val(),
				agEmail: $('#newagentemail').val()
			},
			dataType: "json",
			cache: false,
			success: function(data) {
				if (Object.keys(data.newAgent).length > 0) {
						$('#newagentfirstname').val('');
						$('#newagentlastname').val('');
						$('#newagentaltname').val('');
						$('#newagentorgname').val('');
						$('#newagentemail').val('');
						$(".add_new_ag_close").click();
						
						lnk.closest('tr').find('#agentId', context).val(data.newAgent.ag_id);					
						lnk.closest('tr').find('#agent_LastName', context).val(data.newAgent.ag_lname);
						
						//Resizing text field to make selected agent last name visible
						var agent_ln = lnk.closest('tr').find('#agent_LastName', context).textWidth(data.newAgent.ag_lname) + 25; //data.newAgent.ag_lname.length + 5;
						var agent_fn = lnk.closest('tr').find('#agent_FirstName', context).textWidth(data.newAgent.ag_fname) + 25; //data.newAgent.ag_fname.length + 5;
						$('#agent_LastName', context).css('width', agent_ln + 'px');
						if (data.newAgent.ag_fname != '') {
							lnk.closest('tr').find("#agent_FirstName", context).prop("disabled", false);
							lnk.closest('tr').find("#agent_FirstName", context).val(data.newAgent.ag_fname);
							//Resizing text field to make selected agent first name visible
							lnk.closest('tr').find('#agent_FirstName', context).css('width', agent_fn + 'px');
						}
						if (data.newAgent.ag_altname != '') {
							lnk.closest('tr').find("#agent_AlternateName", context).prop("disabled", false);
							lnk.closest('tr').find("#agent_AlternateName", context).val(data.newAgent.ag_altname);
						}
						if (data.newAgent.ag_orgname != '') {
							lnk.closest('tr').find("#agent_OrganizationName", context).prop("disabled", false);
							lnk.closest('tr').find("#agent_OrganizationName", context).val(data.newAgent.ag_orgname);
						}
						
						lnk.closest('tr').find('#agent_LastName', context).nextAll().remove();
				}
			},
			error: function() {
				//$(".option_results", context).append('<p>None available</p>');
			}
		});
		return false;
	});
}

//WorkType Autocomplete
function bindWorkTypeAttributes(context, workURL) {
	$('#Citation *').not('.ig').remove();
	//$(".content_box a").not(".button")
	var worktype_Id = $('#work_type').val();
	$.ajax({
		method: 'post',
		url: ur + workURL,
		data: {
			worktype_Id: worktype_Id
		},
		dataType: "json",
		cache: false,
		success: function(data) {
			$.each(data.worktype_attribute, function(key, val) {
				// append input control at end of form
				if (val.type == 'Textarea') {
					$('<div class="form-group required">' +
						'<label class="col-xs-1">' + val.field + '</label>' + 
                        '<div class="col-xs-6">' + 
							'<textarea name="wkatid,' + val.id + '" id="' + val.field + '" rows="3" cols="50"/>' +
						'</div>' +
					'</div>').appendTo("#Citation");
				}
				if (val.type == 'Text') {
					$('<div class="form-group required">' +
						'<label class="col-xs-1">' + val.field + '</label>' + 
                        '<div class="col-xs-6">' + 
							'<input type="text" name="wkatid,' + val.id + '" id="' + val.field + '" size="50"/>' +
						'</div>' +
					'</div>').appendTo("#Citation");
				}
				if (val.type == 'RadioButton') {
					$('<div class="form-group required">' +
							'<label class="col-xs-1">'+val.field+'</label>' + 
							'<div class="col-xs-6">' + 
									'<input type="radio" name="wkatid,' + val.id + '" value="true" /> True<br>' +
									'<input type="radio" name="wkatid,' + val.id + '" value="false" /> False<br>' +
							'</div>' +
					'</div>').appendTo("#Citation");
				}
				if (val.type == 'Select') {
					$('<div class="form-group required">' +
							'<label class="col-xs-1">'+val.field+'</label>' + 
							'<div class="col-xs-6">' + 
								'<div>' +
									'<input type="text" class="Attributeoption" name="wkatid,' + val.id + '" id="' + val.field + ':' + val.id + '" size="50"/>' +
									'&nbsp;&nbsp;' + 
									'<button data-toggle="modal" data-target="#pubLookup" class = "btn btn-secondary btn-xs optionLookupBtn"' +
									         'id="optionLookupBtn" data-target="optionsLookup"  value="Lookup" >' + 
											 'Lookup' + '</button>' + 
								'</div>' +
							'</div>' +
					'</div>').appendTo("#Citation");
				}
			})
			$(".optionLookupBtn").on('click', function(e) {
				// show Modal
				var lookupBtn = this;
				attr_option_lookup = $(lookupBtn).prev();
				$("#optionsLookup").modal('show');
				$('#optionsLookup').on('shown.bs.modal', function() {
					$('#lookupOption').focus()
					$('.option_search').on('click', function(e) {
						//var attribute_Id = $(lookupBtn).prev().attr('id');
						var attribute_Id = attr_option_lookup.attr('id');
						var option = $('#lookupOption').val();
						$.ajax({
							method: 'post',
							//url: 'http://localhost<?= $this->url('get_work_details') ?>',
							url: ur + workURL,
							data: {
								option: option,
								attribute_Id: attribute_Id
							},
							dataType: "json",
							cache: false,
							success: function(data) {
								$(".option_results", context).html('');
								$(".option_results", context).append('<p>Search Results</p>');
								$.each(data.attribute_options, function(key, val) {
									$(".option_results", context).append('<p><a name="' + val.id + '" href="' + val.title + '" class="link_options">' + val.title + '</a></p>');
								})
							},
							error: function() {
								$(".option_results", context).append('<p>None available</p>');
							}
						});
						$(context).off('click', '.link_options');
						$(context).on("click", ".link_options", function(e) {
							var linkval = $(this).attr('href');
							attr_option_lookup.val(linkval);
							attr_option_lookup.attr('name', attr_option_lookup.attr('name') + 'optid,' + $(this).attr('name'));
							$('#lookupOption').val('');
							$(".option_results", context).html('');
							$('.option_lookup_close').trigger('click');
							return false;
						});
					});
				});
				return false;
			});
			},
			error: function(data) {
				$("#Citation", context).html('<p>No Options</p>');
			}
	});
	return false;
}

//Add classification hierarchy
_select = '';
function bindClassification(that, context, workURL, for_str) {
	var to_add_row = $(that).closest("tr");
	//to_add_row.children('td', context).children('select', context).eq(0).css('background-color', '#8ec252');
	
	//folder id of selected option
	fl_changed = $(that).val();

	//To set select dropdown width to the length of option selected
	fl_selected_text = "";
	fl_selected_text = $("option:selected", that).text();
	fl_len = fl_selected_text.length + 5;
	$(that).css('width', fl_len + 'ch');
	//
	var no_of_fl_parent = to_add_row.find('.select_' + for_str + '_fl', context).length;
	for (var i = 0; i < no_of_fl_parent; i++) {
		if (to_add_row.find('.select_' + for_str + '_fl', context).eq(i).val() === fl_changed) {
			change_idx = i;
			folder_Id = to_add_row.find('.select_' + for_str + '_fl', context).eq(i).val();
			break;
		}
	}

	if (folder_Id === "") {
		to_add_row.find('.' + for_str + '_fl_col', context).eq(0).nextAll('.' + for_str + '_fl_col', context).remove();
		to_add_row.find('.select_' + for_str + '_fl', context).eq(0).val('');
	} else {
		to_add_row.find('.' + for_str + '_fl_col', context).eq(change_idx).nextAll('.' + for_str + '_fl_col', context).remove();
	}

	//no_of_fl_parent = $('.select_source_fl',context).length;
	no_of_fl_parent = to_add_row.find('.select_' + for_str + '_fl', context).length;

	$.ajax({
		method: 'post',
		url: ur + workURL,
		data: {
			folder_Id: folder_Id
		},
		dataType: "json",
		cache: false,
		success: function(data) {
			if (data.folder_children.length > 0) {
				to_add_row.find('.' + for_str + '_fl_col', context).eq(no_of_fl_parent - 1).after('<td class="' + for_str + '_fl_col" ' + 
				                                                                                  'name="' + for_str + '_fl_col" ' +
																								  'id="' + for_str + '_fl_col" ');

				_select = $('<select class="select_' + for_str + '_fl select2" name="select_' + for_str + '_fl[]">');
				to_append = $('<option value=""></option>');
				$.each(data.folder_children, function(key, val) {
					to_append += '<option value="' + val.id + '">' + val.text_fr + '</option>';
				});
				_select.append($('<option />'));
				_select.append(to_append);

				to_add_row.find('.' + for_str + '_fl_col', context).eq(no_of_fl_parent).append(_select);
				to_add_row.find('.' + for_str + '_fl_col', context).eq(no_of_fl_parent).append('</select>');

				_select.on('change', function() {
					bindClassification(this, document, workURL, for_str);
					return false;
				});
			}
		},
		error: function(data) {
			//$("#Classification", context).html('<p>No Options</p>');
		}
	});
	return false;
}

//Parent lookup
function bindParentWork(context,workURL)
{
	$('.pr_work_results').html('');
	$('#parent_title',context).focus();
	   $('.parent_lookup_search',context).off('click').on('click', function(e) {	
		   var lookup_title = $('#parent_title',context).val();
		   if (lookup_title != "") {
			   $.ajax({
				   method: 'post',
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
					  alert("No results");
				   }
			   });
			   //$(context).off('click', '.prwork_link');
			   $(context).on("click", ".prwork_link", function(e) {
				   var pr_linkval = $(this).attr('name');
				   var pr_labelval = $(this).attr('value');
				   $('.pr_work_div', context).text(pr_labelval);
				   $('#pr_work_lookup_id',context).val(pr_linkval);
				   $('.pr_work_div', context).append('&nbsp;&nbsp;<button type="button" class="btn btn-secondary btn-xs parent_Chng_Btn" name="parent_changeBtn" id="parent_changeBtn" ' +
													 'data-toggle="modal" data-target="#parentLookup_modal">Change</button>')
				   $('.pr_work_div', context).append('&nbsp;&nbsp;<button type="button" class="btn btn-secondary btn-xs parent_Rmv_Btn" name="parent_removeBtn" ' + 
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

function mergeClassification(that, context, workURL, for_str)
{
	var to_add_row = $(that).closest("tr"); 
	if ($(that).val() == "") {
		to_add_row.find('.' + for_str + '_fl_col').eq(0).nextAll('.' + for_str + '_fl_col').remove();
		to_add_row.find('.' + for_str + '_fl_col').eq(0).val('');
		//return false;
	} else {
		fl_changed = $(that).val();

		var no_of_fl_parent = to_add_row.find('.select_' + for_str + '_fl').length;
		for (var i = 0; i < no_of_fl_parent; i++) {
			if (to_add_row.find('.select_' + for_str + '_fl').eq(i).val() === fl_changed) {
				change_idx = i;
				folder_Id = to_add_row.find('.select_' + for_str + '_fl').eq(i).val();
				break;
			}
		}
		to_add_row.find('.' + for_str + '_fl_col').eq(change_idx).nextAll('.' + for_str + '_fl_col').remove();

		no_of_fl_parent = to_add_row.find('.select_' + for_str + '_fl').length;

		$.ajax({
			method: 'post',
			//url: 'http://localhost<?= $this->url('get_work_details') ?>',
			url: ur + workURL,
			data: {
				folder_Id: folder_Id
			},
			dataType: "json",
			cache: false,
			success: function(data) {
				to_add_row.find('.' + for_str + '_fl_col').eq(no_of_fl_parent - 1).after('<td class="' + for_str + '_fl_col" ' + 
																							  'name="' + for_str + '_fl_col" ' + 
																							  'id="' + for_str + '_fl_col" ' + 
																							  '/>');

				_select = $('<select class="select_' + for_str + '_fl select2" name="select_' + for_str + '_fl[]">');
				to_append = $('<option value=""></option>');
				$.each(data.folder_children, function(key, val) {
					to_append += '<option value="' + val.id + '">' + val.text_fr + '</option>';
				});
				_select.append($('<option />'));
				_select.append(to_append);

				to_add_row.find('.' + for_str + '_fl_col').eq(no_of_fl_parent).append(_select);
				to_add_row.find('.' + for_str + '_fl_col').eq(no_of_fl_parent).append('</select>');

				_select.on('change', function() {
					bindClassification(this, document, workURL, for_str);
					return false;
				});
			},
			error: function(data) {
				$("#Classification", context).html('<p>No Options</p>');
			}
		});
		//return false;
	}
	return false;
}