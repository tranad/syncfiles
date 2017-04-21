<html>

<head>

<title>
<?php
$cwd = explode("/",getcwd());
$folder = array_pop($cwd);
echo $folder;
?>
</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<style>

body {
    font-family: sans-serif;
}

.box {
float:left;
padding: 5px; /* space between image and border */
border-radius: 5px;
}

/* /1* can remove if you don't want hover zoom *1/ */
/* .box:hover */
/* { */
/*     box-shadow: 0px 0px 50px #000; */
/*     z-index: 2; */
/*     background-color: #fff; */
/*     transition: all 200ms ease-in; */
/*     -webkit-transition-delay: 800ms; */
/*     -moz-transition-delay: 800ms; */
/*     -o-transition-delay: 800ms; */
/*     transition-delay: 800ms; */
/*     transform: scale(2.1); */
/* } */

/* can remove if you don't want hover zoom */
.box:hover
{
    box-shadow: 0px 0px 50px #000;
    z-index: 2;
    background-color: #fff;
    -moz-transition:-moz-transform 0.5s ease-out; 
    -webkit-transition:-webkit-transform 0.5s ease-out; 
    -o-transition:-o-transform 0.5s ease-out;
    transition:transform 0.5s ease-out;
    -webkit-transition-delay: 1.0s;
    -moz-transition-delay: 1.0s;
    -o-transition-delay: 1.0s;
    transition-delay: 1.0s;
    transform: scale(2.3);
    /* transform: scale(1.0); */
    -moz-transform-origin: 0 0;
    -webkit-transform-origin: 0 0;
    -o-transform-origin: 0 0;
    transform-origin: 0 0;
}

#images {
position:relative;
top: 30px;
}

legend {
font-weight: bold;
}

</style>

<?php

// get flat list with parent references
$data = array();
function fillArrayWithFileNodes( DirectoryIterator $dir , $theParent="#") {
    global $data;
    foreach ( $dir as $node ) {
        if (strpos($node->getFilename(), '.php') !== false) continue;
        if( $node->isDot() ) continue;
        if ( $node->isDir()) fillArrayWithFileNodes( new DirectoryIterator( $node->getPathname() ), $node->getPathname() );

        $tmp = array(
            "id" => $node->getPathname(),
            "parent" => $theParent,
            "text" => $node->getFilename(),
        );
        if ($node->isFile()) $tmp["icon"] = "file"; // can be path to icon file
        $data[] = $tmp;
    }
}
fillArrayWithFileNodes( new DirectoryIterator( '.' ) );

// get all files in flat list
$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST,
    RecursiveIteratorIterator::CATCH_GET_CHILD
);
$paths = array('.');
foreach ($iter as $path => $dir) $paths[] = $path;

// get number of directories
$num_directories = 0;
foreach ( (new DirectoryIterator('.')) as $node ) {
    if( $node->isDot() ) continue;
    if ( $node->isDir()) $num_directories += 1;
}
?>

<script type="text/javascript">

function contains_any(str, substrings) {
    for (var i = 0; i != substrings.length; i++) {
       var substring = substrings[i];
       if (str.indexOf(substring) != - 1) {
         return substring;
       }
    }
    return null; 
}

function draw_objects(file_objects) {
    $("#images").html("");
    for (var ifo = 0; ifo < file_objects.length; ifo++) {
        var fo = file_objects[ifo];
        var name_noext = fo["name_noext"];
        var name = fo["name"];
        var path = fo["path"];
        var color = fo["color"];
        var pdf = fo["pdf"] || fo["name"];
        var txt_str = (fo["txt"].length > 0) ? " <a href='"+fo["txt"]+"'>[text]</a>" : "";
        var extra_str = (fo["extra"].length > 0) ? " <a href='"+fo["extra"]+"'>[extra]</a>" : "";
        $("#images").append(
            "<div class='box' id='"+name_noext+"'>"+
                "    <fieldset style='border:2px solid "+color+"'>"+
                "        <legend>"+name_noext+txt_str+extra_str+"</legend>"+
                "        <a href='"+pdf+"'>"+
                "            <img src='"+path+"/"+name+"' height='300px' />"+
                "        </a>"+
                "    </fieldset>"+
                "</div>"
        );
    }
}

function draw_filtered(filter_paths) {
        var temp_filelist = filelist.filter(function(value) {
            return contains_any(value, filter_paths);
        });

        var temp_objects = make_objects(temp_filelist);
        draw_objects(temp_objects);
}

function make_objects(filelist) {
    var file_objects = [];
    for (var i = 0; i < filelist.length; i++) {
        var f = filelist[i];
        var ext = f.split('.').pop();
        if (ext != "png") continue;
        var color = "";
        if (f.indexOf("HH") != -1) color = "#B03A2E";
        else if (f.indexOf("HL") != -1) color = "#2874A6";
        else if (f.indexOf("LL") != -1) color = "#32CD32";
        var name = f.split('/').reverse()[0];
        var path = f.replace(name, "");
        var name_noext = name.replace("."+ext,"");
        var pdf = (filelist.indexOf(path+name_noext + ".pdf") != -1) ? path+name_noext+".pdf" : "";
        var txt = (filelist.indexOf(path+name_noext + ".txt") != -1) ? name_noext+".txt" : "";
        var extra = (filelist.indexOf(path+name_noext + ".extra") != -1) ? name_noext+".extra" : "";
        file_objects.push({
            "path": path,
            "name_noext": name_noext,
            "name":name,
            "ext": ext,
            "pdf": pdf,
            "txt": txt,
            "extra": extra,
            "color": color,
        });
    }
    return file_objects;
}

// ultimately this will be a master filelist with all files recursively in this directory
// then we will filter for files we want to show
var obj = <?php echo json_encode($data); ?>;
var filelist = <?php echo json_encode($paths); ?>;


$(function() {

    if (<?php echo $num_directories?> > 0) {
        $('#jstree_demo_div')
            .on('changed.jstree', function(e,data) {
                draw_filtered(data.selected);
            })
            .jstree( {
                "core": {
                    'multiple': true,
                    'themes' : {
                       'stripes' : true
                    },
                    "data": 
                        obj
                    // test_data
                    // test_data2
                    
                }
            }); 
    }



        // filelist = filelist.filter(function(value) {
            // return value.indexOf("./plots/qcdEstimateData_2016_ICHEP_SNT/f_jets_mc/HT450to575_j2toInf_b0toInf") != -1;
        // });
        var file_objects = make_objects(filelist);
        draw_objects(file_objects);


    // drag images and hover over others to overlay

    $( ".box" ).draggable({
        opacity: 0.50,
        helper: "clone",
        snap: true,
        revert: true,
    });

    // make map from title of each plot to the html of the title
    var titleMap = {};
    var elems = $(".box").filter(function() {
        var legendTitle = $(this).find("fieldset > legend");
        titleMap[legendTitle.text()] = legendTitle.html();
    });

    $( "input[id='filter']" ).on('keyup', function() {
        $("#message").html("");
        var pattern = $(this).val();
        var modifier = "";
        if(pattern.toLowerCase() == pattern) modifier = "i"; // like :set smartcase in vim (case-sensitive if there's an uppercase char)
        var elems = $(".box").filter(function() {
            try {
            var regex = new RegExp(pattern,modifier);
            } catch(e) {
                return [];
            }
            var matches = this.id.match(regex);  
            if(matches) {
                var legendTitle = $(this).find("fieldset > legend");
                var to_replace =  titleMap[legendTitle.text()];
                to_replace = to_replace.replace(matches[0],"<font style='color:#F00'>"+matches[0]+"</font>") ;
                legendTitle.html(to_replace);
            }
            return matches;
        });
        if(pattern.length < 1) {
            $('.box').show();
            return;
        }
        $('.box').hide();
        if(elems.length == 0) {
            $("#message").html("No matching images!");
        } else {
            elems.show();
        }
    });

    // if page was loaded with a parameter for search, then simulate a search
    // ex: http://uaf-6.t2.ucsd.edu/~namin/dump/plots_isfr_Aug26/?HH$
    if(window.location.href.indexOf("?") != -1) {
        var search = unescape(window.location.href.split("?")[1]);
        $("#filter").val(search);
        $("#filter").trigger("keyup");
    }
});

// vimlike incsearch: press / to focus on search box
$(document).keydown(function(e) {
    if(e.keyCode == 191) {
        console.log(e.keyCode); 
        e.preventDefault();
        $("#filter").focus().select();
    }
});

function copyToClipboard(text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
}

function getQueryURL() {
    var query = escape($('#filter').val());
    var queryURL = "http://"+location.hostname+location.pathname+"?"+query;
    console.log(queryURL);
    copyToClipboard(queryURL)
}

</script>

</head>

<body>

  <div id="jstree_demo_div"> </div>

<input type="text" class="inputbar" id="filter" placeholder="Search/wildcard filter" />
<a href="javascript:;" onClick="getQueryURL();">copy as URL</a>
<span id="message"></span>
<div id="images"></div>


</body>
</html>
