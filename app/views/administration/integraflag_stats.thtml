<script src='/js/jquery.flotcomp.js'></script>
<script src='/js/jquery.flot.js'></script>

<br /><h3><a href='/administration/integraflag'>&laquo; Back to Integraflag</a></h3><br />

<div id='placeholder' style='width:750px; height: 300px'></div>

<div id='dates'>
    <form action='?' method='get'>
        Start date: <input type='date' name='start' value='<?php echo $sdate; ?>' />
        End date: <input type='date' name='end' value='<?php echo $edate; ?>' />
        <input type='submit' value='Go!' />
    </form>
</div>

<div id='choices'></div><br />

<script type='text/javascript'>
	jQuery.noConflict();
    datasets = JSON.parse('<?php echo $coords; ?>');
	var i = 0;
    jQuery.each(datasets, function(key, val) {
        val.color = i;
        ++i;
    });
    
    // insert checkboxes 
    var choiceContainer = jQuery("#choices");
    var cnt = 0;
    jQuery.each(datasets, function(key, val) {
        var checked = "";
        if(cnt < 3) {
            checked = " checked='checked'";
        }
        choiceContainer.append('<br/><input type="checkbox" name="' + key +
                               '" ' + checked + ' id="id' + key + '">' +
                               '<label for="id' + key + '">'
                                + val.label + '</label>');
        cnt++;
    });
    choiceContainer.find("input").click(plotAccordingToChoices);
    
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);
 
        return markings;
    }
 
    
    function plotAccordingToChoices() {
        var data = [];
 
        choiceContainer.find("input:checked").each(function () {
            var key = jQuery(this).attr("name");
            if (key && datasets[key])
                data.push(datasets[key]);
        });
        
        jQuery.plot(jQuery("#placeholder"), data, {
            xaxis: { mode: "time", timeformat: "%m/%d/%y"},
            yaxis: { tickDecimals: 0},
            points: { show: true },
            lines: { show: true },
            selection: { mode: "x" },
            grid: { markings: weekendAreas }
        });
    }
    jQuery(function() {
        plotAccordingToChoices();
    });
</script>
