var main_container;

$(document).ready(function()
{
	main_container = document.getElementById("main_container");
	$("#add_text").click(function (){new_text();});
	$("#add_table").click(function (){new_table();});
	$("#add_picture").click(function (){new_picture();});
	$("#add_code").click(function (){new_code();});
	$("#add_video").click(function (){new_video();});
});

function create_element()
{
	var container = document.createElement("div");
	container.className = "div_objects";
	
	var div_header = document.createElement("div");
	div_header.className = "div_header";
	var div_header_number = document.createElement("div");
	var div_header_input = document.createElement("div");
	var font_number = document.createElement("font");
	$(font_number).html(($(".div_objects").length - 3) + ". ");
	div_header_number.appendChild(font_number);
	var input_text = document.createElement("input");
	input_text.type = "text";
	div_header_input.appendChild(input_text);
	div_header.appendChild(div_header_number);
	div_header.appendChild(div_header_input);
	
	var div_main_content = document.createElement("div");
	div_main_content.className = "div_main_content";
	
	var div_controls = create_control_buttons();
	div_controls.className = "div_controls";

	
	container.appendChild(div_header);
	container.appendChild(div_main_content);
	container.appendChild(div_controls);
	return container;
}

function create_control_buttons()
{
	var div_con = document.createElement("DIV");
	var div_delete = document.createElement("DIV");
	var img_delete = document.createElement("img");
	img_delete.width = 16;
	img_delete.height = 16;
	img_delete.src = "./img/delete.png";
	$(img_delete).click(function ()
	{
		$(this).parent().parent().parent().remove();
	});
	div_delete.appendChild(img_delete);
	var div_up = document.createElement("DIV");
	var img_up = document.createElement("img");
	img_up.width = 16;
	img_up.height = 37;
	img_up.src = "./img/up.png";
	$(img_up).click(function ()
	{
		var el = $(this).parent().parent().parent();
		el.after(el.prev());   
	});
	div_up.appendChild(img_up);
	var div_down = document.createElement("DIV");
	var img_down = document.createElement("img");
	img_down.width = 16;
	img_down.height = 37;
	img_down.src = "./img/down.png";
	$(img_down).click(function ()
	{
		var el = $(this).parent().parent().parent();
		$(el).before($(el).next());  
	});
	div_down.appendChild(img_down);
	div_con.appendChild(div_delete);
	div_con.appendChild(div_up);
	div_con.appendChild(div_down);
	return div_con;
}

function new_video()
{
	var header = create_element();
	main_container.appendChild(header);
}

function new_text()
{
	var content = create_element();
	var content_place = $(content).children(".div_main_content")[0];

	var input = document.createElement("textarea");
	input.className = "text_object";
	content_place.appendChild(input);
	main_container.appendChild(content);
	/*
	var cont = document.createElement("DIV");
	cont.className = "div_objects";
	var input = document.createElement("textarea");
	input.className = "text_object";
	var controls = create_control_buttons();
	cont.appendChild(input);
	cont.appendChild(controls);
	main_container.appendChild(cont);
	*/
}

function new_table()
{
	var cont = document.createElement("DIV");
	cont.className = "div_objects";
	var input = document.createElement("textarea");
	input.type = "text";
	input.className = "text_object";
	var controls = create_control_buttons();
	cont.appendChild(input);
	cont.appendChild(controls);
	main_container.appendChild(cont);
}

function new_picture()
{
	var cont = document.createElement("DIV");
	cont.className = "div_objects";
	var input_file = document.createElement("input");
	input_file.type = "file";
	input_file.className = "hiden";
	input_file.accept = "image/*";
	
	input_file.onchange = function (evt) {
		var tgt = evt.target || window.event.srcElement,
        files = tgt.files;
		if (FileReader && files && files.length) {
			var fr = new FileReader();
			fr.onload = function () {
				$(input_file).parent().children("img").attr("src",  fr.result);
			}
			fr.readAsDataURL(files[0]);
		}
		else 
		{
			img.src = "./img/load_image.png";
		}
	}
	var cont1 = document.createElement("DIV");
	cont1.className = "image_container";
	var br = document.createElement("br");
	var img = document.createElement("img");
	img.src = "./img/load_image.png";
	$(img).click(function (){
		$(this).parent().children("input[type=file]").trigger("click");
	});	
	var input = document.createElement("input");
	input.type = "text";
	input.className = "input_text_one_line";
	var controls = create_control_buttons();
	cont1.appendChild(input_file);
	cont1.appendChild(img);
	cont1.appendChild(br);
	cont1.appendChild(input);
	cont.appendChild(cont1);
	cont.appendChild(controls);
	main_container.appendChild(cont);
}

function new_code()
{
	var cont = document.createElement("DIV");
	cont.className = "div_objects";
	var input = document.createElement("textarea");
	input.className = "text_object";
	var controls = create_control_buttons();
	cont.appendChild(input);
	cont.appendChild(controls);
	main_container.appendChild(cont);
}