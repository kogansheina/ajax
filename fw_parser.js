
	function parse_field(fields)
	{
		var fld = null;
        if (fields.tagName == "field") 
        {
            fld = new FWfield(fields.getAttribute("name"),
                              fields.getAttribute("size"),
                              fields.getAttribute("is_array"),
                              fields.getAttribute("array_num_entries"));
        }
		return fld;
	}
    function calculate_from_field(seg,tab,entry,disp)
    {
        var w = 0;
        var wd = 0;
        var ent = fwdata[seg].tables[tab].entries[entry];
        var n = ent.fields.length;
        var len = 0;
        var t = 0;
        for (var c=0; c<n; c++) 
        {
            len += parseInt(ent.fields[c].size)*parseInt(ent.fields[c].len);
            if (c==0) t = len;
        }
        ent.length = Math.ceil(len/8);
        var maxl = 32;
        switch (ent.length) 
        {
        case 1:
            maxl = 8;
            break;
        case 2:
            maxl = 16;
            break;
        case 3:
            maxl = 24;
            break;
        default:
            break;
        }
        if (n > 0)
        {
            ent.fields[0].from = maxl-t;
            if (ent.fields[0].from <= 0) 
            {
                ent.fields[0].from = maxl+ent.fields[0].from;
                if ((ent.fields[0].from < 0) || (ent.fields[0].from == maxl))
                    ent.fields[0].from = 0;
            }
            w = t;
            toadd=true;
            for (y=0; y<ent.fields[0].word.length;y++) 
            {
                if (ent.fields[0].word[y] == wd) {
                    toadd=false;
                    break;
                }
            }
            if (toadd) 
                ent.fields[0].word.push(wd);
        }
        for(var c=1; c<n; c++)
        {
            var t = parseInt(ent.fields[c].size) * parseInt(ent.fields[c].len);
            if (w == 32) 
            {
                wd ++;
                toadd=true;
                for (y=0; y<ent.fields[c].word.length;y++) 
                {
                    if (ent.fields[c].word[y] == wd) {
                        toadd=false;
                        break;
                    }
                }
                if (toadd) 
                    ent.fields[c].word.push(wd);
                w = t;
            }
            else if (w < 32) 
            {
                toadd=true;
                for (y=0; y<ent.fields[c].word.length;y++) 
                {
                    if (ent.fields[c].word[y] == wd) {
                        toadd=false;
                        break;
                    }
                }
                if (toadd) 
                    ent.fields[c].word.push(wd);
                if (w+t > 32) 
                {
                    wd++;
                    toadd=true;
                    for (y=0; y<ent.fields[c].word.length;y++) 
                    {
                        if (ent.fields[c].word[y] == wd) {
                            toadd=false;
                            break;
                        }
                    }
                    if (toadd) 
                        ent.fields[c].word.push(wd);
                    w = w + t - 32;
                }
                else w += t;
            }
            else document.write("Error !!! "+ent.fields[c].name+" w="+w.toString()+"<br>");
            if (parseInt(ent.fields[c].len) > 1) 
            {
                w=0;
                switch (parseInt(ent.fields[c].size)) 
                {
                case 8:
                    wd += parseInt(ent.fields[c].len)/4;
                    break;
                case 16:
                    wd += parseInt(ent.fields[c].len)/2;
                    break;
                case 32:
                    wd += parseInt(ent.fields[c].len);
                    break;
                default:
                    document.write("Error !!! "+ent.fields[c].name+" is an array with wrong size "+ent.fields[c].size.toString()+"<br>");
                    break;
                }
            }
            ent.fields[c].from = ent.fields[c-1].from - t;
            if (ent.fields[c].from <= 0) 
            {
                ent.fields[c].from = maxl+ent.fields[c].from;
                if ((ent.fields[c].from < 0) || (ent.fields[c].from == maxl))
                    ent.fields[c].from = 0;
            }
        }
        var ent = fwdata[seg].tables[tab];
        var calclen = fwdata[seg].tables[tab].entries[0].length;
        if (fwdata[seg].tables[tab].union == "none" ) 
        {
            for(var c=1; c<ent.entries.length; c++)
            {
                calclen += ent.entries[c].length;
            }
        }
        else
            calclen = maxlen(fwdata[seg].tables[tab]);
        fwdata[seg].tables[tab].length = calclen*fwdata[seg].tables[tab].len*
            fwdata[seg].tables[tab].len2*fwdata[seg].tables[tab].len3;
        if (disp) 
        {
            table_of_fields_from(seg,tab,entry);
        }
        //FWentry_print(ent);
    }
    function maxlen(tab)
    {
        var m = tab.entries[0].length;
        for(var c=1; c<tab.entries.length; c++)
        {
            m = Math.max(m,tab.entries[c].length);
        }
        return m;
    }
	function parse_entry(tab,entries)
	{
        var ent = null;
        if (entries.tagName == "entry") 
        {
            ts = fwdata[fwdata.length-1].tables;
            t=ts[ts.length-1]; // last table defined
            ts0 = fwdata[0].tables[0]; // suppose first segment is 'entries'
            if (fwdata[fwdata.length-1].name == 'entries') 
            {
                ent = new FWentry(entries.getAttribute("name"));
                if (ent)
                {
                    add_entry(tab,ent);
                    var tt = entries.childNodes;
                    var ntt = tt.length;
                    for (var k=0; k < ntt; k++) 
                    {
                        var fld = parse_field(tt[k]);
                        if (fld) 
                            add_field(ent,fld);
                    }
                }
            }
            else
            {
                for (ii=0; ii<ts0.entries.length; ii++) 
                {
                    if (ts0.entries[ii].name == entries.getAttribute("name")) 
                    {
                        ent = ts0.entries[ii];
                        break;
                    }
                }
                if (!ent) 
                {
                    ent = new FWentry(entries.getAttribute("name"));
                    if (ent)
                    {
                        add_entry(tab,ent);
                        var tt = entries.childNodes;
                        var ntt = tt.length;
                        for (var k=0; k < ntt; k++) 
                        {
                            var fld = parse_field(tt[k]);
                            if (fld) 
                                add_field(ent,fld);
                        }
                    }
                }
                else
                    add_entry(tab,ent);
            }
        }
		return ent;
	}

	function parse_table(seg,tables)
	{
        var tab = null;
        if (tables.tagName == "table") 
        {
		    tab = new FWtable(tables.getAttribute("name"),
                              tables.getAttribute("address"),
                              tables.getAttribute("anchor"),
                              tables.getAttribute("shared_id"),
                              tables.getAttribute("module_name"),
                              tables.getAttribute("same_table"),
                              tables.getAttribute("size"),
                              tables.getAttribute("size2"),
                              tables.getAttribute("size3"),
                              tables.getAttribute("union_name"),
                              tables.getAttribute("align_type"),
                              tables.getAttribute("alignment"));
            if (tab)
            {
                add_table(seg,tab);
                var tt = tables.childNodes;
                var ntt = tt.length;
                for (var j=0; j < ntt; j++) 
                {
                    parse_entry(tab,tt[j]);
                }
            }
        }
        else if (tables.tagName == "entry") 
        {
            if (seg.tables.length == 0)
            {
                tab = new FWtable("undef",null,"true","0",null,"false",null,null,null,null,null,null);
                if (tab)
                    add_table(seg,tab);
            }
            else
                 parse_entry(seg.tables[seg.tables.length-1],tables);
        }
	}
