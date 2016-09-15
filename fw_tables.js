    function updatePHP(data,action)
    {
        //document.write("file_output="+file_output+"<br>");
        var f = document.createElement("form");
        f.setAttribute("action", 'fw.php');     
        f.setAttribute("id", "frm");  
        f.setAttribute("target", "_blank");  
        var inp = document.createElement("input");
        inp.setAttribute("type", "hidden");     
        inp.setAttribute("name", "filen");     
        inp.setAttribute("value", file_output);     
        f.appendChild(inp);
        inp = document.createElement("input");
        inp.setAttribute("type", "hidden");     
        inp.setAttribute("name", "action");     
        inp.setAttribute("value", action);     
        f.appendChild(inp);
        if (data != null) 
        {
            inp = document.createElement("input");
            inp.setAttribute("type", "hidden");     
            inp.setAttribute("name", "data");     
            inp.setAttribute("value", data);     
            f.appendChild(inp);
        }
        try
        {
            document.appendChild(f);
        }
        catch(err)
        {
            var txt="There was an error on this page.\n\n";
            txt+="Error description: " + err.message + "\n\n";
            txt+="Click OK to continue.\n\n";
            alert(txt);
        }
        f.submit();
    }
    // transform DataBase (array) to a JSON string
     function array2json(what,s,t,e,f,action) 
    {
        var id = s+"&"+t+"&"+e+"&"+f+"&";
        var json = '{';
        switch (what) 
        {
        case 0: // segment
            json += '"'+id+'":{"name":"'+fwdata[s].name+'","end_address":"'+fwdata[s].end+'"}';
            break;
        case 1: // table
            json += '"'+id+'":{"name":"'+fwdata[s].tables[t].name+'","base":"'+
                fwdata[s].tables[t].base+'","anchor":"'+
                fwdata[s].tables[t].anchor+'","shared_id":"'+
                fwdata[s].tables[t].shared+'","size":"'+ 
                fwdata[s].tables[t].len+'","size2":"'+ 
                fwdata[s].tables[t].len2+'","size3":"'+ 
                fwdata[s].tables[t].len3+'","module":"'+
                fwdata[s].tables[t].module+'","same":"'+
                fwdata[s].tables[t].same+'","union":"'+
                fwdata[s].tables[t].union+'","align_type":"'+
                fwdata[s].tables[t].at+'","alignment":"'+
                fwdata[s].tables[t].align+'"}';
            break;
        case 2: // entry
            json += '"'+id+'":{"name":"'+fwdata[s].tables[t].entries[e].name+'"}';
            break;
        case 3: // field
            json += '"'+id+'":{"name":"'+fwdata[s].tables[t].entries[e].fields[f].name
                +'","is_array":"'+fwdata[s].tables[t].entries[e].fields[f].ar+
                '","array_num_entries":"'+fwdata[s].tables[t].entries[e].fields[f].len+
                '","size":"'+fwdata[s].tables[t].entries[e].fields[f].size+'"}';
            break;
        default:
            json += '"'+id+'"';
            break;
        }
        json += '}';//Return associative JSON
        //document.write(json+"<br>");
        updatePHP(json,action);
    }
    function retrieve_parameters(ii)
    {
        var p = new Array(4);
        for (var t=0; t<4; t++) 
        {
            p[t]=-1;
        }
        var tt = ii.split("&");
        // tt is an array of strings by form 'a'='b'
        for (var x=0; x<tt.length; x++) 
        {
            var w = tt[x].split('=');
            // w is an array
            for (var y=0; y<w.length; y+=2) 
            {
                switch (w[y]) 
                {
                case 'seg':
                    p[0] =  parseInt(w[y+1]);
                    break;
                case 'tab':
                    p[1] =  parseInt(w[y+1]);
                    break;
                case 'ent':
                    p[2] =  parseInt(w[y+1]);
                    break;
                case 'fld':
                    p[3] =  parseInt(w[y+1]);
                    break;
                }
            }
        }
        return p;
    }
    function chgSegName(y)
    {
        var x = document.getElementById('segname'+y);
        if (!x.value.length) {
            alert("Name is empty");
            return;
        }
        var f = fwdata[y];
        if (!instead_regexp(x.value))            
        {
            alert("chgSegName: Incorrect name : "+x.value);
            x.value = f.name;
        }
        else 
        {
            f.name = x.value;
            //array2json(0,y,-1,-1,-1,2)
        }
    }
    function chgSegEnd(y)
    {
        var x = document.getElementById('segend'+y);
        if (!x.value.length) {
            alert("End is empty");
            return;
        }
        var f = fwdata[y];
        var rr = instead_reghex(x.value);
        if (rr != true)            
        {
            alert("chgSegEnd: Incorrect end : "+rr);
            x.value = f.end;
        }
        else 
        {
            var b = x.value;
            if (b.substring(0,2).toLowerCase()=="0x") 
                f.end = parseInt(b.substring(2,b.length-2),16);
            else
                f.end = parseInt(b);
            //array2json(0,y,-1,-1,-1,2)
        }
    }
    function chgTabSame(y)
    {
        var x = document.getElementById('tabsame'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.same = x.value;
        //array2json(0,y,-1,-1,-1,2)
    }
    function chgTableName(y)
    {
        var x = document.getElementById('tabname'+y);
        if (!x.value.length) {
            alert("Name is empty");
            return;
        }
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        if (!instead_regexp(x.value))            
        {
            alert("chgTableName: Incorrect name : "+x.value);
            x.value = f.name;
        }
        else 
        {
            f.name = x.value;
            //array2json(1,p[0],p[1],-1,-1,2)
        }
    }
    function chgTableBase(y)
    {
        var x = document.getElementById('tabadr'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        var b = x.value;

        var rr = instead_reghex(x.value);
        if (rr != true)            
        {
            alert("chgTableBase: Incorrect end : "+rr);
            x.value = f.base;
        }
        if (b.substring(0,2).toLowerCase()=="0x") 
            f.base = parseInt(b.substring(2,b.length),16);
        else
            f.base = parseInt(b);
        //var y=document.getElementById('tables').rows[p[1]+1].cells;
        //y[1].innerHTML = f.base;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableAnchor(y)
    {
        var x = document.getElementById('tabanch'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.anchor = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableShare(y)
    {
        var x = document.getElementById('tabshare'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.shared = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableModule(y)
    {
        var x = document.getElementById('tabmod'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.module = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableLen(y)
    {
        var x = document.getElementById('tablen'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.len = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableLen2(y)
    {
        var x = document.getElementById('tablen2'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.len2 = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableLen3(y)
    {
        var x = document.getElementById('tablen3'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.len3 = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableAlignType(y)
    {
        var x = document.getElementById('tabat'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.at = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgTableUnion(y)
    {
        var x = document.getElementById('tabunion'+y);
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]];
        f.union = x.value;
        //array2json(1,p[0],p[1],-1,-1,2)
    }
    function chgFieldName(y)
    {
        var x = document.getElementById('fldname'+y);
        if (!x.value.length) {
            alert("Name is empty");
            return;
        }
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]].entries[p[2]].fields[p[3]];
        if (!instead_regexp(x.value))            
        {
            alert("chgFieldName: Incorrect name : "+x.value);
            x.value = f.name;
        }
        else 
        {
            f.name = x.value;
            //array2json(3,p[0],p[1],p[2],p[3],2)
        }
        //calculate_from_field(p[0],p[1],p[2],true);
    }
    function chgFieldLength(y)
    {
        var x = document.getElementById('fldlen'+y);
        if (isNaN(x.value)) {
            alert("Length is not numeric");
            return;
        }
        var intlen = parseInt(x.value);
        if (intlen <=0) 
        {
            alert("Length is > 0");
            return;
        }
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]].entries[p[2]].fields[p[3]];
        var intsize = parseInt(f.size);
        if (x.value > 1) 
        {
            if ((intsize != 8) && (intsize != 16) && (intsize != 32)) 
                alert("Field has incorrect size\nIt is an array and must have size 8/16/32");
            else 
            {
                f.len = x.value;
                f.ar = true;
                calculate_from_field(p[0],p[1],p[2],true);
                //array2json(3,p[0],p[1],p[2],p[3],2)
            }
        }
        else 
        {
            f.ar = false;
            f.len = x.value;
            calculate_from_field(p[0],p[1],p[2],true);
            //array2json(3,p[0],p[1],p[2],p[3],2)
        }
    }
    function chgFieldSize(y)
    {
        var x = document.getElementById('fldsize'+y);
        if (isNaN(x.value)) {
            alert("Size is not numeric");
            return;
        }
        var intsize = parseInt(x.value);
        if ((intsize > 32) || (intsize <=0))  
        {
            alert("Field has incorrect size");
            return;
        }
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]].entries[p[2]].fields[p[3]];
        if (f.ar && (intsize != 8) && (intsize != 16) && (intsize != 32))
        {
            alert("Field has incorrect size\nIt is an array and must have size 8/16/32");
            return;
        }
        f.size = x.value;
        calculate_from_field(p[0],p[1],p[2],true);
        //array2json(3,p[0],p[1],p[2],p[3],2)
    }
    function chgEntryName(y)
    {
        var x = document.getElementById('entname'+y);
        if (!x.value.length) {
            alert("Name is empty");
            return;
        }
        var p = retrieve_parameters(y);
        var f = fwdata[p[0]].tables[p[1]].entries[p[2]];
        if (!instead_regexp(x.value))            
        {
            alert("chgEntryName: Incorrect name : "+x.value);
            x.value = f.name;
        }
        else 
        {
            f.name = x.value;
            //array2json(2,p[0],p[1],p[2],-1,2)
        }
    }
    function chgTableAlign(y)
    {
        var p = retrieve_parameters(y);
        var x = document.getElementById('tabalign'+y);
        if (isNaN(x.value)) {
            alert("Align is not numeric");
            return;
        }
        var align = parseInt(x.value);
        var f = fwdata[p[0]].tables[p[1]];
        if (align != 1) 
            if (align%2 != 0) {
            document.Write("Error !!! Table "+f.name+" has incorrect alignment value "+align+"<br>");
            align = 1;
            }
        f.align = align;
        //array2json(2,p[0],p[1],p[2],-1,2)
    }
	function delete_table(name)
	{
		var x = document.getElementById(name);
		while (x.rows.length>1) //deletes all rows of a table, besides the header
			x.deleteRow(1) 
	}
    function go(aEvent)
    {
        var ii; 
        var browser;


        if (whichBrowser() == 'f')
            browser = aEvent.target; // mozilla ???
        else browser = window.event.srcElement; // IE

        ii = browser.getAttribute('id');
        var p = retrieve_parameters(ii);
        if (p[0] != -1) // segment level
        {
            if (p[1] != -1) // table level
            {
                if (p[2] != -1) // entry level
                { 
					delete_table("fields");
					table_of_fields(p[0],p[1],p[2]);
                }
                else
                {
                    delete_table("entries");
                    delete_table("fields");
                    table_of_entries(p[0],p[1]);
                    table_of_fields(p[0],p[1],0);
                }
            }
            else
            {
                delete_table("tables");
                delete_table("entries");
                delete_table("fields");
                table_of_tables(p[0]);
                table_of_entries(p[0],0);
                table_of_fields(p[0],0,0);
            }
        }
    }
    function add_field_row(seg,tab,ent,fld)
    {
        fwdata[seg].tables[tab].entries[ent].fields[fld+1] = new FWfield("none","32","0","1");
        var x=document.getElementById('fields');
        var row = x.insertRow(fld+2);
        row_field(row,seg,tab,ent,fld+1)
    }
    function add_entry_row(seg,tab,ent)
    {
        fwdata[seg].tables[tab].entries[ent+1] = new FWentry("none");
        var x=document.getElementById('entries');
        var row = x.insertRow(ent+2);
        row_entry(row,seg,tab,ent+1)
    }
    function add_table_row(seg,tab)
    {
        fwdata[seg].tables[tab+1] = new FWtable("none",null,'false','0',null,'false',null,null,null,null,null);
        var x=document.getElementById('tables');
        var row = x.insertRow(tab+2);
        row_table(row,seg,tab+1);
    }
    function add_segment_row(seg)
    {
        fwdata[seg+1] = new FWsegment("none",null);
        var x=document.getElementById('segments');
        var row = x.insertRow(seg+2);
        row_segment(row,seg+1);
    }
    function del_field(seg,tab,ent,fld)
    {
        if (fwdata[seg].tables[tab].entries[ent].fields[fld] == 'none') 
        {
            alert("the field is null; cannot be deleted.")
            return;
        }
        var x=document.getElementById('fields');
        if (x)
        {
            x.deleteRow(fld+1);
            fwdata[seg].tables[tab].entries[ent].fields.splice(fld,1);
            calculate_from_field(seg,tab,ent,true);
            array2json(2,seg,tab,ent,fld,3)
        }
        return;
    }
    function del_entry(seg,tab,ent)
    {
        if (fwdata[seg].tables[tab].entries[ent].name == 'none') 
        {
            alert("the entry is null; cannot be deleted.")
            return;
        }
        var flds = fwdata[seg].tables[tab].entries[ent].fields;
        // delete all its fields
        while (flds.length>0) 
            flds.splice(0,1);
        delete_table('fields');
        var x=document.getElementById('entries');
        if (x)
        {
            x.deleteRow(ent+1);
            fwdata[seg].tables[tab].entries.splice(ent,1);
            array2json(2,seg,tab,ent,-1,3);
        }
        return;
    }
    function del_table(seg,tab)
    {
        if (fwdata[seg].tables[tab].name == 'none') 
        {
            alert("the table is null; cannot be deleted.")
            return;
        }
        var ents = fwdata[seg].tables[tab].entries;
        // delete all its entries
        while(ents.length>0) 
            ents.splice(0,1);
        delete_table('entries');
        delete_table('fields');
        var x=document.getElementById('tables');
        if (x)
        {
            x.deleteRow(tab+1);
            fwdata[seg].tables.splice(tab,1);
            array2json(2,seg,tab,-1,-1,3);
        }
        return;
    }
    function del_segment(seg)
    {
        if (fwdata[seg].name == 'none') 
        {
            alert("the segment is null; cannot be deleted.")
            return;
        }
        var tabs = fwdata[seg].tables;
        // delete all its tables
        while (tabs.length>0) 
            tabs.splice(0,1); 
        delete_table('tables');
        delete_table('entries');
        delete_table('fields');
        var x=document.getElementById('segments');
        if (x) 
        {
            x.deleteRow(seg+1);
            fwdata.splice(seg,1);
            array2json(2,seg,-1,-1,-1,3)
        }
        return;
    }
    function add(aEvent)
    {
        var browser;
        if (whichBrowser() == 'f')
            browser = aEvent.target; // mozilla ???
        else browser = window.event.srcElement; // IE
        var ii = browser.getAttribute('id');
        var p = retrieve_parameters(ii);
        if (p[0] != -1) 
        {
            if (p[1] != -1)
            {
                if (p[2] != -1)
                {
                    if (p[3] != -1)
                    {   // add field
                        if (fwdata[p[0]].tables[p[1]].entries[p[2]].fields[p[3]].add) 
                            add_field_row(p[0],p[1],p[2],p[3]);
                        array2json(3,p[0],p[1],p[2],p[3],2);
                    }
                    else 
                    {// add entry
                        if (fwdata[p[0]].tables[p[1]].entries[p[2]].add) 
                            add_entry_row(p[0],p[1],p[2]);
                        array2json(2,p[0],p[1],p[2],-1,2);
                    }
                }
                else 
                {// add table
                    if (fwdata[p[0]].tables[p[1]].add) 
                        add_table_row(p[0],p[1]);
                    array2json(1,p[0],p[1],-1,-1,2);
                }
            }
            else // add segment
            {
                if (fwdata[p[0]].add) 
                    add_segment_row(p[0]);
                array2json(0,p[0],-1,-1,-1,2);
            }
        }
    }
    function del(aEvent)
    {
        var browser;
        if (whichBrowser() == 'f')
            browser = aEvent.target; // mozilla ???
        else browser = window.event.srcElement; // IE
        var ii = browser.getAttribute('id');
        var p = retrieve_parameters(ii);
        if (p[0] != -1) 
        {
            if (p[1] != -1)
            {
                if (p[2] != -1)
                {
                    if (p[3] != -1)
                        // delete field
                        del_field(p[0],p[1],p[2],p[3]);
                    else // delete entry
                        del_entry(p[0],p[1],p[2]);
                }
                else // delete table
                    del_table(p[0],p[1]);
            }
            else // delete segment
                del_segment(p[0]);
        }
    }
    function row_segment(row,c)
    {
        //FWsegment_print(fwdata[c]);
		var cell = [];
        // create form element
        var f = document.createElement("form");
        // segment name
        cell[0]=document.createElement('td');
        cell[0].setAttribute("align","left");
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 50);     
        inp.setAttribute("value", fwdata[c].name);     
        inp.setAttribute("id", 'segname'+c); 
        inp.onchange = function() { chgSegName(c);};
        cell[0].appendChild(inp);
        cell[1]=document.createElement('td');
        cell[1].setAttribute("align","left");
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 10);  
        var base = "0x"+fwdata[c].end.toString(16).toLowerCase();
        inp.setAttribute("value", base);
        inp.onchange = function() { chgSegEnd(c);};
        cell[1].appendChild(inp);
        // segment address
        cell[2]=document.createElement('td');
        inp = document.createElement("select");
        inp.setAttribute("size", 1); 
        // segment open (img)
        cell[2]=document.createElement('td');
        cell[2].setAttribute("align","middle");
        var img = document.createElement('img');
        img.setAttribute('src', '../common/fill.gif');
        img.setAttribute('id', 'seg='+c);
        if (whichBrowser() == 'f')
        img.addEventListener("click", go, false);
        else img.onclick = function() { go(event);}; 
        cell[2].appendChild(img);
        // segment delete command
        cell[3]=document.createElement('td');
        cell[3].setAttribute("align","middle");
        var btn = document.createElement("input");       
        btn.setAttribute("type", "button");     
        btn.setAttribute('id', 'seg='+c);
        btn.setAttribute("value", "change");     
        if (whichBrowser() == 'f')
        btn.addEventListener("click", add, false);
        else btn.onclick = function() { add(event);}; 
        cell[3].appendChild(btn);
        var btn1 = document.createElement("input");       
        btn1.setAttribute("type", "button");     
        btn1.setAttribute('id', 'seg='+c);
        btn1.setAttribute("value", "delete");     
        if (whichBrowser() == 'f')
        btn1.addEventListener("click", del, false);
        else btn1.onclick = function() { del(event);}; 
        cell[4]=document.createElement('td');
        cell[4].setAttribute("align","middle");
        cell[4].appendChild(btn1);
        // add to row cell commands
        row.appendChild(cell[0]);
        row.appendChild(cell[1]);
        row.appendChild(cell[2]);
        row.appendChild(cell[3]);
        row.appendChild(cell[4]);
        // add to row form
        row.appendChild(f);
    }
    function table_of_segments()
	{
        var n = fwdata.length;
		var row;
        var x = document.getElementById('segments');
		for(var c=0;c<n;c++)
		{
            row = x.insertRow(c+1);
            row_segment(row,c);
		}
        var y = x.createCaption();
		y.setAttribute("align","middle");
		y.setAttribute("style","color:crimson");
		y.innerHTML="<h1>"+"FW Segments"+"</h1>";
	}
    function row_table(row,segment,c)
    {
		var cell = [];
        var tables = fwdata[segment].tables;
        var id = 'seg='+segment+'&tab=';
        // create form element
        var f = document.createElement("form");
        // table name
        cell[0]=document.createElement('td');
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", "50");
        inp.setAttribute("value", tables[c].name);     
        inp.setAttribute("id", 'tabname'+id+c); 
        inp.onchange = function() { chgTableName(id+c);};
        cell[0].appendChild(inp);
        cell[0].setAttribute("align","left");
        // table address
        cell[1]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 10); 
        var base = "0x"+tables[c].base.toString(16).toLowerCase();
        inp.setAttribute("value", base);     
        inp.setAttribute("id", 'tabadr'+id+c); 
        inp.onchange = function() { chgTableBase(id+c);};
        cell[1].appendChild(inp);
        cell[1].setAttribute("align","left");
        // table anchor
        cell[2]=document.createElement('td');
        inp = document.createElement("select");
        inp.setAttribute("size", 1); 
        if (tables[c].anchor == 'true') 
        {
            inp.options[0] = new Option("true", "true", true, true);
            inp.options[1] = new Option("false", "false", false, false);
        }
        else
        {
            inp.options[0] = new Option("true", "true", true, false);
            inp.options[1] = new Option("false", "false", false, true);
        }
        inp.setAttribute("id", 'tabanch'+id+c); 
        inp.onchange = function() { chgTableAnchor(id+c);};
        cell[2].appendChild(inp);
        cell[2].setAttribute("align","middle");
        cell[3]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 5);     
        inp.setAttribute("value", tables[c].shared);     
        inp.setAttribute("id", 'tabshare'+id+c); 
        inp.onchange = function() { chgTableShare(id+c);};
        cell[3].appendChild(inp);
        cell[3].setAttribute("align","middle");
        cell[4]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 50);     
        inp.setAttribute("value", tables[c].module);     
        inp.setAttribute("id", 'tabmod'+id+c); 
        inp.onchange = function() { chgTableModule(id+c);};
        cell[4].appendChild(inp);
        cell[4].setAttribute("align","middle");
        inp = document.createElement("select");
        inp.setAttribute("size", 1);
        if (tables[c].same == 'true') 
        {
            inp.options[0] = new Option("true", "true", true, true);
            inp.options[1] = new Option("false", "false", false, false);
        }
        else
        {
            inp.options[0] = new Option("true", "true", true, false);
            inp.options[1] = new Option("false", "false", false, true);
        }
        cell[5]=document.createElement('td');
        cell[5].setAttribute("align","middle");
        inp.setAttribute("id", 'tabsame'+c); 
        inp.onchange = function() { chgTabSame(c);};
        cell[5].appendChild(inp);
        cell[6]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 1);     
        inp.setAttribute("value", tables[c].len);     
        inp.setAttribute("id", 'tablen'+id+c); 
        inp.onchange = function() { chgTableLen(id+c);};
        cell[6].appendChild(inp);
        cell[6].setAttribute("align","middle");
        cell[7]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 1);     
        inp.setAttribute("value", tables[c].len2);     
        inp.setAttribute("id", 'tablen2'+id+c); 
        inp.onchange = function() { chgTableLen2(id+c);};
        cell[7].appendChild(inp);
        cell[7].setAttribute("align","middle");
        cell[8]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 1);     
        inp.setAttribute("value", tables[c].len3);     
        inp.setAttribute("id", 'tablen3'+id+c); 
        inp.onchange = function() { chgTableLen3(id+c);};
        cell[8].appendChild(inp);
        cell[8].setAttribute("align","middle");
        // table union
        cell[9]=document.createElement('td');
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", "50");
        inp.setAttribute("value", tables[c].union);     
        inp.setAttribute("id", 'tabunion'+id+c); 
        inp.onchange = function() { chgTableUnion(id+c);};
        cell[9].appendChild(inp);
        cell[9].setAttribute("align","left");
        cell[10]=document.createElement('td');


        inp = document.createElement("select");
        inp.setAttribute("size", 1);
        // text, value, defaultSelected, selected 
        inp.options[0] = new Option("regular", "regular", false, false);
        inp.options[1] = new Option("table", "table", false, false);
        inp.options[2] = new Option("cyclic", "cyclic", false, false);
        switch (tables[c].at) 
        {
        case "regular":
            // text, value, defaultSelected, selected 
            // will be calculated for h file
            inp.options[0].defaultSelected = true;
            inp.options[0].selected = true;
            break;
        case "table":
            // text, value, defaultSelected, selected 
            inp.options[1].defaultSelected = true;
            inp.options[1].selected = true;
            break;
        case "cyclic":
            inp.options[2].defaultSelected = true;
            inp.options[2].selected = true;
            break;
        }
        inp.setAttribute("id", 'tabat'+id+c); 
        inp.onchange = function() { chgTableAlignType(id+c);};
        cell[10].appendChild(inp);
        cell[10].setAttribute("align","left");

        cell[11]=document.createElement('td');
        inp = document.createElement("input");
        cell[11].setAttribute("align","middle");
        inp.setAttribute("id", 'tabalign'+id+c); 
        inp.setAttribute("value", tables[c].align);  
        inp.onchange = function() { chgTableAlign(id+c);};
        cell[11].appendChild(inp);

        cell[12]=document.createElement('td');
        var inp = document.createTextNode(tables[c].length);
        cell[12].appendChild(inp);
        cell[12].setAttribute("align","left");

        // table open command
        cell[13]=document.createElement('td');
        var img = document.createElement('img');
        img.setAttribute('src', '../common/fill.gif');
        img.setAttribute('id', id+c);
        if (whichBrowser() == 'f')
        img.addEventListener("click", go, false);
        else img.onclick = function() { go(event);}; 
        cell[13].appendChild(img);
        cell[13].setAttribute("align","middle");
        // table change command
        cell[14]=document.createElement('td');
        cell[14].setAttribute("align","middle");
        var btn1 = document.createElement("input");       
        btn1.setAttribute("type", "button");     
        btn1.setAttribute('id', id+c);
        btn1.setAttribute("value", "change");     
        if (whichBrowser() == 'f')
        btn1.addEventListener("click", add, false);
        else btn1.onclick = function() { add(event);}; 
        cell[14].appendChild(btn1);
        cell[15]=document.createElement('td');
        cell[15].setAttribute("align","middle");
        var btn = document.createElement("input");       
        btn.setAttribute("type", "button");     
        btn.setAttribute('id', id+c);
        btn.setAttribute("value", "delete");     
        if (whichBrowser() == 'f')
        btn.addEventListener("click", del, false);
        else btn.onclick = function() { del(event);}; 
        cell[15].appendChild(btn);
        // add to row form
        row.appendChild(f);
        // add to row cell commands
        row.appendChild(cell[0]);
        row.appendChild(cell[1]);
        row.appendChild(cell[2]);
        row.appendChild(cell[3]);
        row.appendChild(cell[4]);
        row.appendChild(cell[5]);
        row.appendChild(cell[6]);
        row.appendChild(cell[7]);
        row.appendChild(cell[8]);
        row.appendChild(cell[9]);
        row.appendChild(cell[10]);
        row.appendChild(cell[11]);
        row.appendChild(cell[12]);
        row.appendChild(cell[13]);
        row.appendChild(cell[14]);
        row.appendChild(cell[15]);
    }
	function table_of_tables(segment)
	{
		var row;
		var n = fwdata[segment].tables.length;
		var x = document.getElementById('tables');
		for(var c=0;c<n;c++)
		{
            row = x.insertRow(c+1);
            row_table(row,segment,c)
		}
        var y = x.createCaption();

		y.setAttribute("align","middle");
		y.setAttribute("style","color:crimson");
        var z = "Tables of Segment ";
        if (fwdata[segment].name.length) 
            z += fwdata[segment].name;
		y.innerHTML="<h1>"+z+"</h1>";
	}
    function row_entry(row,segment,table,c)
    {
		var cell = [];
        var entries = fwdata[segment].tables[table].entries;
        var id = 'seg='+segment+'&tab='+table+'&ent=';
        // create form element
        var f = document.createElement("form");
        // entry name
        cell[0]=document.createElement('td');
        var inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", 50);     
        inp.setAttribute("value", entries[c].name);     
        inp.setAttribute("id", 'entname'+id+c); 
        inp.onchange = function() { chgEntryName(id+c);};
        cell[0].appendChild(inp);
        cell[0].setAttribute("align","left");
        cell[1]=document.createElement('td');
        var inp = document.createTextNode(entries[c].length);
        cell[1].appendChild(inp);
        cell[1].setAttribute("align","left");

        // entry open command
        cell[2]=document.createElement('td');
        var img = document.createElement('img');
        img.setAttribute('src', '../common/fill.gif');
        img.setAttribute('id', id+c);
        if (whichBrowser() == 'f')
        img.addEventListener("click", go, false);
        else img.onclick = function() { go(event);}; 
        cell[2].appendChild(img);
        cell[2].setAttribute("align","middle");
        // entry delete command
        cell[3]=document.createElement('td');
        cell[3].setAttribute("align","middle");
        var btn = document.createElement("input");       
        btn.setAttribute("type", "button");     
        btn.setAttribute('id', id+c);
        btn.setAttribute("value", "change");     
        if (whichBrowser() == 'f')
        btn.addEventListener("click", add, false);
        else btn.onclick = function() { add(event);}; 
        cell[3].appendChild(btn);
        cell[4]=document.createElement('td');
        cell[4].setAttribute("align","middle");
        var btn1 = document.createElement("input");       
        btn1.setAttribute("type", "button");     
        btn1.setAttribute('id', id+c);
        btn1.setAttribute("value", "delete");     
        if (whichBrowser() == 'f')
        btn1.addEventListener("click", del, false);
        else btn1.onclick = function() { del(event);}; 
        cell[4].appendChild(btn1);
        // add to row form
        row.appendChild(f);
        // add to row cell commands
        row.appendChild(cell[0]);
        row.appendChild(cell[1]);
        row.appendChild(cell[2]);
        row.appendChild(cell[3]);
        row.appendChild(cell[4]);
    }
	function table_of_entries(segment,table)
	{
		var row;
        if (fwdata[segment].tables.length) 
        {
            var tabname = fwdata[segment].tables[table].name;
            var n = fwdata[segment].tables[table].entries.length;
            var x = document.getElementById('entries');
            for(var c=0;c<n;c++)
            {
                row = x.insertRow(c+1);
                row_entry(row,segment,table,c);
            }
            var y = x.createCaption();

            y.setAttribute("align","middle");
            y.setAttribute("style","color:crimson");
            var z = 'Entries of Table ';
            if (fwdata[segment].name.length) 
                z += fwdata[segment].name;
            if (tabname.length) 
                z += "."+tabname;
            y.innerHTML="<h1>"+z+"</h1>";
        }
	}
    function row_field(row,segment,table,entry,c)
    {
		var cell = [];
        var fields = fwdata[segment].tables[table].entries[entry].fields;
        var id = 'seg='+segment+'&tab='+table+'&ent='+entry+'&fld=';
        // create form element
        var f = document.createElement("form");
        // field name
        cell[0]=document.createElement('td');
        var inp = document.createElement("input");
        inp.setAttribute("type", "text"); 
        inp.setAttribute("size","50");
        inp.setAttribute("value", fields[c].name);     
        inp.setAttribute("id", 'fldname'+id+c); 
        inp.onchange = function() { chgFieldName(id+c);};
        cell[0].appendChild(inp);
        // field word
        cell[1]=document.createElement('td');
        cell[1].setAttribute("align","middle");
        if (fields[c].word.length > 0) 
        {
            var txt = fields[c].word[0].toString();
            for (var ji=1; ji < fields[c].word.length; ji++) 
            {
               txt += ", "+fields[c].word[ji].toString();
            }
        }
        else  txt = "unknown";
        inp = document.createTextNode(txt);
        cell[1].appendChild(inp);
        // field start
        cell[2]=document.createElement('td');
        cell[2].setAttribute("align","middle");
        inp = document.createTextNode(fields[c].from);
        //inp.setAttribute("id", 'fldfrom'+id+c); 
        cell[2].appendChild(inp);
        // field length
        cell[3]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", "5");     
        inp.setAttribute("value", fields[c].size);     
        inp.setAttribute("id", 'fldsize'+id+c); 
        inp.onchange = function() { chgFieldSize(id+c);};
        cell[3].appendChild(inp);
        // field size
        cell[4]=document.createElement('td');
        inp = document.createElement("input");
        inp.setAttribute("type", "text");     
        inp.setAttribute("size", "5");     
        inp.setAttribute("value", fields[c].len);     
        inp.setAttribute("id", 'fldlen'+id+c); 
        inp.onchange = function() { chgFieldLength(id+c);};
        cell[4].appendChild(inp);
        // field delete command
        cell[5]=document.createElement('td');
        cell[5].setAttribute("align","middle");
        var btn = document.createElement("input");       
        btn.setAttribute("type", "button"); 
        btn.setAttribute('id',id+c );
        btn.setAttribute("value", "change");     
        if (whichBrowser() == 'f')
        btn.addEventListener("click", add, false);
        else btn.onclick = function() { add(event);}; 
        cell[5].appendChild(btn);
        cell[6]=document.createElement('td');
        cell[6].setAttribute("align","middle");
        var btn1 = document.createElement("input");       
        btn1.setAttribute("type", "button"); 
        btn1.setAttribute('id',id+c );
        btn1.setAttribute("value", "delete");     
        if (whichBrowser() == 'f')
        btn1.addEventListener("click", del, false);
        else btn1.onclick = function() { del(event);}; 
        cell[6].appendChild(btn1);
        // add to form name cell
        row.appendChild(cell[0]);
        row.appendChild(cell[1]);
        row.appendChild(cell[2]);
        row.appendChild(cell[3]);
        // add to row form
        row.appendChild(f);
        // add to row cell commands
        row.appendChild(cell[4]);
        row.appendChild(cell[5]);
        row.appendChild(cell[6]);
    }

	function table_of_fields_from(segment,table,entry)
	{
		var row ;
        var id = 'seg='+segment+'&tab='+table+'&ent='+entry+'&fld=';
        if (fwdata[segment].tables.length) 
        {
            if (fwdata[segment].tables[table].entries.length) 
            {
                var n = fwdata[segment].tables[table].entries[entry].fields.length;
                for(var c=0;c<n;c++)
                {
                    var x=document.getElementById('fields').rows[c+1].cells;
                    x[1].innerHTML = fwdata[segment].tables[table].entries[entry].fields[c].from;
                }
            }
        }
    }
	function table_of_fields(segment,table,entry)
	{
		var row ;
        if (fwdata[segment].tables.length) 
        {
            if (fwdata[segment].tables[table].entries.length) 
            {
                var tabname = fwdata[segment].tables[table].name;
                var entname = fwdata[segment].tables[table].entries[entry].name;
                var n = fwdata[segment].tables[table].entries[entry].fields.length;
                var x = document.getElementById('fields');
                for(var c=0;c<n;c++)
                {
                    // add field row
                    row = x.insertRow(c+1)
                    row_field(row,segment,table,entry,c);
                }
                var y = x.createCaption();

                y.setAttribute("align","middle");
                y.setAttribute("style","color:crimson");
                var z ='Fields of Entry ';
                if (fwdata[segment].name.length) 
                    z += fwdata[segment].name;
                if (tabname.length) 
                    z += "."+tabname;
                if (entname.length) 
                    z += "."+entname;
                y.innerHTML="<h1>"+z+"</h1>";
            }
        }
	}
    function whichBrowser()
    {
        var nAgt = navigator.userAgent;
        if (nAgt.indexOf("Firefox")!=-1) return "f";
        else if (nAgt.indexOf("MSIE")!=-1) return "w";
        else if (nAgt.indexOf("Chrome")!=-1) return "c";
        return " ";
    }
