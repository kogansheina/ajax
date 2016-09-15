var debug=false;
    function FWfield (a,b,d,e) 
	{
        if (!a) { document.write("Field name is null\n"); return;}
        if (!instead_regexp(a) && a.length)            
            alert("Field '"+a+"' is incorrect");
		this.name = a;
		this.size = b;
        if (d == null) this.ar = false;
        else 
        {
            if (d == "true") this.ar = true;
            else this.ar = false;
        }
        if (e == null) e = 1;
        this.len = e;
        if (a != 'none') 
        {
            if (d && (e == 1))
                alert("Field '"+a+"' defined as array of 1");
            if (!d && (e != 1)) 
                alert("Field '"+a+"' not defined as array, but has entries");
        }
        var intsize = parseInt(this.size);
        if ((intsize > 32) || (intsize <=0))  
            alert("Field '"+a+"' has incorrect size");
        if (this.ar && (intsize != 8) && (intsize != 16) && (intsize != 32)) 
            alert("Field '"+a+"' has incorrect size\nIt is an array and must have size 8/16/32");
        this.from=0;
        this.word=[];
        if (this.name == "none") this.add = true;
        else this.add = false; 
        if (debug) 
            FWfield_print(this);
	}
    function FWfield_print(f) 
    {
		document.write("Field : "+f.name+" from "+f.from+" has length = "+f.size+" has entries = "+f.len+" word = "+f.word.length+"<br>");
	}
    function FWentry (a,b) 
    {
        if (!a) { document.write("Entry name is null\n"); return;}
        if (!instead_regexp(a) && a.length)            
            alert("Entry "+a+" is incorrect");
        this.name = a;
		this.fields = [];
		this.i = 0;
        this.length = 0;
        if (this.name == "none") this.add = true;
        else this.add = false; 
        if (debug) FWentry_print(this);
    }
    function add_field (e,a) 
    {
        if (a)
            e.fields[e.i++] = a;
        else
            document.write("Error adding field to entry "+e.name+"<br>");
    }
    function FWentry_print (e) 
    {
        document.write("Entry : "+e.name+"<br>");
        var n = e.fields.length;
        if (n) 
        {
            for (var k=0; k < n; k++) {
                FWfield_print(e.fields[k]);
                }
        }
        else
            document.write("No fields<br>");
    }
    function FWtable (a,b,d,e,f,s,c,c2,c3,u,at,align) 
    {
        if (!a) { document.write("Table name is null\n"); return;}
        if (!instead_regexp(a) && a.length)            
            alert("Table "+a+" is incorrect");
        this.name = a;
        if (b==null) this.base = 0; 
        else if (b.length == 0)  this.base = 0;
        else if (b.substring(0,2).toLowerCase()=="0x") 
            this.base = parseInt(b.substring(2,b.length),16);
        else
            this.base = parseInt(b);

        if (d == null) this.anchor = "true";
        else this.anchor = d;
        if (s == null) this.same = "false";
        else this.same = s;
        if (e == null) this.shared = "0"; 
        else this.shared = e;
        if (c == null) this.len= "1"; 
        else this.len = c;
        if (c2 == null) this.len2= "1"; 
        else this.len2 = c2;
        if (c3 == null) this.len3= "1"; 
        else this.len3 = c3;
        if (f==null) this.module = "none";
        else this.module = f;
        if (u==null) this.union = "none";
        else this.union = u;
		this.entries = [];
		this.i = 0;
        if ((this.name == "none") || (this.name == "undef")) this.add = true;
        else this.add = false; 
        this.length = 0;
        if (at==null) this.at = "regular";
        else if (at == "table" || at == "cyclic" || at == "regular") 
            this.at = at;
        else {
            document.write("Table : "+t.name+" has align_type wrong"+"<br>");
            this.at = "regular";
        }
        if (align==null) this.align = 1;
        else
        {
            this.align = parseInt(align);
            if (this.align != 1) 
                if (this.align%2 != 0) {
                alert("Error !!! Table "+a+" has incorrect alignment value "+align+"<br>");
                this.align = 1;
            }
        }
        if (debug) 
            FWtable_print(this);
    }
    
    function add_entry (s,a) 
    {
        if (a)
            s.entries[s.i++] = a;
    }
    function FWtable_print (t) 
    {
        document.write("Table : "+t.name+" has address = "+t.base+" anchor = "+t.anchor+
                       " shared_id = "+t.shared+" module = "+t.module+" same = "+t.same+
                       " size = "+t.len+" size2 = "+t.len2+" size3 = "+t.len3+" union = "+t.union+" align = "+t.at+"<br>");
        var n = t.entries.length;
        if (n) 
        {
            for (var k=0; k < n; k++) {
                FWentry_print(t.entries[k]);
                }
        }
        else
            document.write("No entries<br>");
    }

    function FWsegment (a,end) 
    {
        if (!a) { document.write("Segment name is null\n"); return;}
        if (!instead_regexp(a) && a.length)            
            alert("Segment "+a+" is incorrect");
        this.name = a;
        if (end == null) 
            this.end = 0xffff;
        else 
        {
            if (end.substring(0,2).toLowerCase()=="0x") 
                this.end = parseInt(end.substring(2,end.length),16);
            else
                this.end = parseInt(end);
        }
        this.start = 0;
		this.tables = [];
		this.i = 0;
        if (this.name == "none") this.add = true;
        else this.add = false; 
        if (debug) FWsegment_print(this);
    }
    function add_table (s,a) 
    {
        if (a)
        {
            s.tables[s.i++] = a;
            if (s.i>1) 
            {
                var aa = [];
                for (var i=0; i < s.tables.length; i++) 
                {
                    aa.push(s.tables[i].base);
                }
                aa.sort(function(a,b){return a-b});
                var bb = s.tables;
                s.tables = [];
                for (var i=0; i < aa.length; i++) 
                { 
                    var bbindex = findIn(bb,aa[i]);
                    s.tables.push(bb[bbindex]);
                    bb[bbindex] = null;
                }
            }
            else
                s.start = a.base;
        }
    }
    function print_r(arr)
    {
        for(var i=0;i<arr.length;i++){
            document.write("<b>arr["+i+"] is </b>=>"+arr[i]+"<br>");
        }
        document.write("End Array<br>");
    }
    function findIn(a1,v)
    {
        for (var t=0; t < a1.length; t++) 
        {
            if (a1[t] != null) 
                if (a1[t].base == v) return t;
        }
        return -1;
    }
    function FWsegment_print (s) 
    {
        document.write("Segment : "+s.name+"<br>");
        var n = s.tables.length;
        if (n) 
        {
            for (var k=0; k < n; k++) {
                FWtable_print(s.tables[k]);
                }
        }
        else
            document.write("No tables<br>");
    }

