<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
<title>Javascript Plotter</title>
<!--
Copyright 2006 Ben Falconer

Licenced under the Open Software License v. 2.1
The full terms of this license can be found at:
http://opensource.org/licenses/osl-2.1.php

I cannot enforce this but I would apprecitate that in addition
to the attribution notice below, which must be included if you
modify the code, you add your own name to it.

Atribution Notice.
The original version of this code was written by Ben Falconer (ben@falconers.eclipse.co.uk) and is here: 
http://zed0.uwcs.co.uk/J_Plotter
The original idea for this code came from Benjamin Joffe with his Canvascape project: http://www.abrahamjoffe.com.au/ben/canvascape/
-->
<style type="text/css"><!--

heading {
	padding: 5px;
	font-size: 16pt;
	font-style: italic;
	font-weight: bold;
}

top {
	float: left;
	padding: 5px;
	font-size: 12pt;
	font-weight: bold;
}

body {
	font-family: arial;
	font-size: 10pt;
	background-color: #000;
	color: #CCC;
}

axis {
	font-size:10pt;
	color:#F00;
}

h5 {
	text-align: right;
	font-size: 11px;
	margin: 0px;
}

a {
	color: #999;
}

a:hover {
	color: #FFF;
}

copy {
	font-weight: bolder;
	text-align: right;
	float: right;
	color: #FFF;
	font-size: 11px;
	margin: -5px;
}

img {
	position: absolute;
	z-index: -1;
}

#holder {
	clear: both;
	position: relative;
	width: 400px;
	height:400px;
	border: 2px solid #333;
	cursor: move;
}

#addpoint {
	position: relative;
	border: 2px solid #333;
	width: 200px;
	text-align: right;
}

#help {
	position: relative;
	border: 2px solid #333;
	width: 100%;
	height: 100%;
	overflow: auto;
}

#helptext {
	font-family: arial;
	font-size: 10pt;
	background-color: #000;
	color: #CCC;
	margin-left: 5px;
}

#canvas {
	position: absolute;
	top: 0;
	left: 0;

}

#labels {
	position: absolute;
	text-align: right;
	font-size: 8pt;
	color: #00FFFF;
	z-index: 1;
}

#cover {
	position: absolute;
	text-align: right;
	font-size: 8pt;
	color: #00FFFF;
	z-index: 2;
	width: 100%;
	height: 100%;
}

#angle{
	border: 2px solid #333;
	width: 200px;
	text-align: right;
}

--></style>

<script type="text/javascript"><!--
var pi=Math.PI;
var http_request = false;
var helpexample='Examples:<br /><br /> \
		<a href="javascript:load(example1);">Example 1</a><br /> \
		<a href="javascript:load(example2);">Example 2</a><br /> \
		<a href="javascript:load(example3);">Example 3</a><br /> \
		<a href="javascript:load(example4);">Example 4</a><br /> \
		<a href="javascript:load(example5);">Example 5</a><br /><br /> \
		<a href="javascript:load(uploaded);">Reload last uploaded file</a>';

var helpview='You can position the plot by either clicking \
and dragging it to rotate or by manually changing the values in \
the "View" box in the top center of the page.';

var helpplot='You can plot new points using the "Add Points" box \
(bottom centre).  To create a new point just type in the X,Y and \
Z coordinates and type in what label you would like to plot (if \
you do not want a label make it blank).  The point will not be \
visible until you plot another point with the "Add Point to Line" \
box checked, checking this box means that a line will be created \
between the previous plotted point and the one you are about to \
plot.';

var helpsave='Saving you can use the save button to copy the source for your graph to the text box below and save it for reploting later.<br />\
	Any Models you have uploaded can be accessed at any time from the same URL';

var helpcopy='Copyright 2006 Ben Falconer<br /><br />\
Licenced under the Open Software License v. 2.1<br />\
The full terms of this license can be found at:<br />\
<a href="http://opensource.org/licenses/osl-2.1.php">http://opensource.org/licenses/osl-2.1.php</a><br /><br />\
I cannot enforce this but I would apprecitate that in addition \
to the attribution notice below, which must be included if you \
modify the code, you add your own name to it.<br /<br />\
Atribution Notice.<br />\
The original version of this code was written by Ben Falconer \
(<a href="mailto:ben@falconers.eclipse.co.uk">ben@falconers.eclipse.co.uk</a>) and is here: \
<a href="http://zed0.uwcs.co.uk/J_Plotter.php">http://zed0.uwcs.co.uk/J_Plotter.php</a><br />\
The original idea for this code came from Benjamin Joffe with\
his Canvascape project: <a href="http://www.abrahamjoffe.com.au/ben/canvascape/">http://www.abrahamjoffe.com.au/ben/canvascape/</a>';

var uploaded;

var example1='[[[0, 0, 0, ""], [150, 0, 0, "<axis>X-Axis</axis>"]], [[0, 0, 0, ""], [0, 150, 0, "<axis>Y-Axis</axis>"]], [[0, 0, 0, ""], [0, 0, 150, "<axis>Z-Axis</axis>"]], [[50, 0, 0, "Point 1"], [50, 80, 0, "Point 2"], [0, 80, 0, "Point 3"], [0, 0, 0, ""], [50, 0, 0, ""]], [[100, 40, 10, "(100,40,10)"], [70, 80, 80, "(70,80,80)"]]]';

var example2='[[[0, 0, 0, ""], [150, 0, 0, "<axis>X-Axis</axis>"]], [[0, 0, 0, ""], [0, 150, 0, "<axis>Y-Axis</axis>"]], [[0, 0, 0, ""], [0, 0, 150, "<axis>Z-Axis</axis>"]], [[10, 10, 0, ""], [10, 90, 0, ""]], [[10, 50, 0, ""], [50, 50, 0, ""]], [[50, 90, 0, ""], [50, 10, 0, ""]], [[60, 10, 0, ""], [80, 10, 0, ""]], [[70, 10, 0, ""], [70, 90, 0, ""]], [[80, 90, 0, ""], [60, 90, 0, ""]], [[80, 0, 50, "From Ben"]]]';

var example3='[[[50, 50, 50, ""], [100, 100, 100, ""], [-100, 100, 100, ""], [-50, 50, 50, ""], [50, 50, 50, ""]], [[50, 50, 50, ""], [100, 100, 100, ""], [100, -100, 100, ""], [50, -50, 50, ""], [50, 50, 50, ""]], [[50, 50, 50, ""], [100, 100, 100, ""], [100, 100, -100, ""], [50, 50, -50, ""], [50, 50, 50, ""]], [[-50, -50, -50, ""], [-100, -100, -100, ""], [100, -100, -100, ""], [50, -50, -50, ""], [-50, -50, -50, ""]], [[-50, -50, -50, ""], [-100, -100, -100, ""], [-100, 100, -100, ""], [-50, 50, -50, ""], [-50, -50, -50, ""]], [[-50, -50, -50, ""], [-100, -100, -100, ""], [-100, -100, 100, ""], [-50, -50, 50, ""], [-50, -50, -50, ""]], [[50, -50, -50, ""], [100, -100, -100, ""], [100, -100, 100, ""], [50, -50, 50, ""], [50, -50, -50, ""]], [[-50, 50, -50, ""], [-100, 100, -100, ""], [100, 100, -100, ""], [50, 50, -50, ""], [-50, 50, -50, ""]], [[-50, 50, -50, ""], [-100, 100, -100, ""], [-100, 100, 100, ""], [-50, 50, 50, ""], [-50, 50, -50, ""]], [[-50, -50, 50, ""], [-100, -100, 100, ""], [-100, 100, 100, ""], [-50, 50, 50, ""], [-50, -50, 50, ""]], [[-50, -50, 50, ""], [-100, -100, 100, ""], [100, -100, 100, ""], [50, -50, 50, ""], [-50, -50, 50, ""]], [[50, 50, -50, ""], [100, 100, -100, ""], [100, -100, -100, ""], [50, -50, -50, ""], [50, 50, -50, ""]]]';

var example4='[[[0, 0, 20, ""], [0, 150, 0, "<axis>North</axis>"], [20, 20, 0, ""], [0, 0, 20, ""]], [[0, 0, 20, ""], [0, 150, 0, ""], [-20, 20, 0, ""], [0, 0, 20, ""]], [[0, 0, -20, ""], [0, 150, 0, ""], [20, 20, 0, ""], [0, 0, -20, ""]], [[0, 0, -20, ""], [0, 150, 0, ""], [-20, 20, 0, ""], [0, 0, -20, ""]], [[0, 0, 20, ""], [100, 0, 0, "East"], [20, 20, 0, ""], [0, 0, 20, ""]], [[0, 0, 20, ""], [100, 0, 0, ""], [20, -20, 0, ""], [0, 0, 20, ""]], [[0, 0, -20, ""], [100, 0, 0, ""], [20, 20, 0, ""], [0, 0, -20, ""]], [[0, 0, -20, ""], [100, 0, 0, ""], [20, -20, 0, ""], [0, 0, -20, ""]], [[0, 0, 20, ""], [0, -100, 0, "South"], [20, -20, 0, ""], [0, 0, 20, ""]], [[0, 0, 20, ""], [0, -100, 0, ""], [-20, -20, 0, ""], [0, 0, 20, ""]], [[0, 0, -20, ""], [0, -100, 0, ""], [20, -20, 0, ""], [0, 0, -20, ""]], [[0, 0, -20, ""], [0, -100, 0, ""], [-20, -20, 0, ""], [0, 0, -20, ""]], [[0, 0, 20, ""], [-100, 0, 0, "West"], [-20, 20, 0, ""], [0, 0, 20, ""]], [[0, 0, 20, ""], [-100, 0, 0, ""], [-20, -20, 0, ""], [0, 0, 20, "	"]], [[0, 0, -20, ""], [-100, 0, 0, ""], [-20, 20, 0, ""], [0, 0, -20, ""]], [[0, 0, -20, ""], [-100, 0, 0, ""], [-20, -20, 0, ""], [0, 0, -20, ""]]]';

var example5='[[[-92, 6, -5, ""], [-87, -11, 7, ""], [-80, -13, 2, ""], [-92, 6, -5, ""]], [[34, 28, 15, ""], [35, 25, -10, ""], [36, 25, 7, ""], [34, 28, 15, ""]], [[-87, -11, 7, ""], [-66, -31, 0, ""], [-66, -29, 4, ""], [-87, -11, 7, ""]], [[-90, 17, -5, ""], [-87, 18, 5, ""], [-53, 20, 3, ""], [-90, 17, -5, ""]], [[83, 26, -4, ""], [62, 0, -5, ""], [72, 20, -2, ""], [83, 26, -4, ""]], [[-6, 37, 6, ""], [0, 34, 1, ""], [3, 46, -5, ""], [-6, 37, 6, ""]], [[26, 26, 33, ""], [2, 28, 43, ""], [13, -16, 59, ""], [26, 26, 33, ""]], [[34, 28, 15, ""], [39, 26, -7, ""], [35, 25, -10, ""], [34, 28, 15, ""]], [[-12, 46, -10, ""], [-18, 46, 1, ""], [-13, 47, 9, ""], [-12, 46, -10, ""]], [[69, -19, -5, ""], [64, -11, -14, ""], [83, 26, -4, ""], [69, -19, -5, ""]], [[36, 25, 7, ""], [24, 25, 31, ""], [23, 28, 31, ""], [36, 25, 7, ""]], [[23, 28, 31, ""], [34, 28, 15, ""], [36, 25, 7, ""], [23, 28, 31, ""]], [[-86, -16, -6, ""], [-66, -31, 0, ""], [-93, -10, 4, ""], [-86, -16, -6, ""]], [[-16, -31, -59, ""], [13, -24, -60, ""], [0, -47, -44, ""], [-16, -31, -59, ""]], [[-48, -47, 14, ""], [35, -47, 13, ""], [0, -46, 45, ""], [-48, -47, 14, ""]], [[79, 25, 5, ""], [72, 20, -2, ""], [62, 0, -5, ""], [79, 25, 5, ""]], [[34, 28, 15, ""], [23, 28, 31, ""], [26, 26, 33, ""], [34, 28, 15, ""]], [[-83, 13, -5, ""], [-90, 17, -5, ""], [-53, 19, -5, ""], [-83, 13, -5, ""]], [[-45, 26, -12, ""], [-31, 26, -33, ""], [-39, 25, -23, ""], [-45, 26, -12, ""]], [[35, -31, 42, ""], [3, -31, 59, ""], [0, -46, 45, ""], [35, -31, 42, ""]], [[-13, 47, 9, ""], [-18, 46, 1, ""], [-12, 35, -1, ""], [-13, 47, 9, ""]], [[-66, -29, 4, ""], [-69, -21, 3, ""], [-87, -11, 7, ""], [-66, -29, 4, ""]], [[0, 34, 1, ""], [-5, 35, -5, ""], [3, 46, -5, ""], [0, 34, 1, ""]], [[-84, 14, 6, ""], [-87, 18, 5, ""], [-97, 11, 6, ""], [-84, 14, 6, ""]], [[37, -15, 44, ""], [13, -16, 59, ""], [3, -31, 59, ""], [37, -15, 44, ""]], [[69, -19, -5, ""], [67, -15, 12, ""], [45, -29, 4, ""], [69, -19, -5, ""]], [[-39, -46, 32, ""], [-21, -46, 42, ""], [-26, -24, 60, ""], [-39, -46, 32, ""]], [[100, 27, -1, ""], [79, 25, 5, ""], [80, 15, 5, ""], [100, 27, -1, ""]], [[-68, -22, -4, ""], [-66, -29, -4, ""], [-80, -16, -6, ""], [-68, -22, -4, ""]], [[64, -3, 11, ""], [67, -15, 12, ""], [80, 15, 5, ""], [64, -3, 11, ""]], [[24, 25, 31, ""], [0, 25, 43, ""], [2, 28, 43, ""], [24, 25, 31, ""]], [[2, 28, 43, ""], [23, 28, 31, ""], [24, 25, 31, ""], [2, 28, 43, ""]], [[62, 0, -5, ""], [64, -3, 11, ""], [79, 25, 5, ""], [62, 0, -5, ""]], [[25, -46, -32, ""], [0, -47, -44, ""], [13, -24, -60, ""], [25, -46, -32, ""]], [[25, -46, -32, ""], [38, -45, -7, ""], [35, -47, 13, ""], [25, -46, -32, ""]], [[62, 0, -5, ""], [64, -11, -14, ""], [45, -8, -12, ""], [62, 0, -5, ""]], [[-15, 26, 46, ""], [-40, 26, 33, ""], [-51, -15, 44, ""], [-15, 26, 46, ""]], [[2, 28, 43, ""], [26, 26, 33, ""], [23, 28, 31, ""], [2, 28, 43, ""]], [[-5, 35, -5, ""], [-12, 35, -1, ""], [-12, 46, -10, ""], [-5, 35, -5, ""]], [[13, -24, -60, ""], [-16, -31, -59, ""], [-7, -16, -62, ""], [13, -24, -60, ""]], [[-40, 26, 33, ""], [-47, 28, 17, ""], [-66, -16, 20, ""], [-40, 26, 33, ""]], [[-87, -11, 7, ""], [-93, -10, 4, ""], [-66, -31, 0, ""], [-87, -11, 7, ""]], [[-12, 35, -1, ""], [-6, 37, 6, ""], [-13, 47, 9, ""], [-12, 35, -1, ""]], [[-53, 20, 3, ""], [-53, 19, -5, ""], [-90, 17, -5, ""], [-53, 20, 3, ""]], [[-65, -31, 10, ""], [-67, -24, -20, ""], [-52, -45, -7, ""], [-65, -31, 10, ""]], [[-80, -13, 2, ""], [-87, -11, 7, ""], [-69, -21, 3, ""], [-80, -13, 2, ""]], [[45, -5, 5, ""], [45, -19, 16, ""], [64, -3, 11, ""], [45, -5, 5, ""]], [[-51, -15, 44, ""], [-26, -24, 60, ""], [-15, 26, 46, ""], [-51, -15, 44, ""]], [[32, 26, 12, ""], [17, 26, 33, ""], [25, 25, 23, ""], [32, 26, 12, ""]], [[-6, 25, -40, ""], [-19, 25, -38, ""], [-31, 26, -33, ""], [-6, 25, -40, ""]], [[0, 25, 43, ""], [-29, 25, 37, ""], [-27, 28, 38, ""], [0, 25, 43, ""]], [[-27, 28, 38, ""], [2, 28, 43, ""], [0, 25, 43, ""], [-27, 28, 38, ""]], [[80, 15, 5, ""], [79, 25, 5, ""], [64, -3, 11, ""], [80, 15, 5, ""]], [[64, -11, -14, ""], [69, -19, -5, ""], [45, -26, -11, ""], [64, -11, -14, ""]], [[80, 15, 5, ""], [82, 15, -2, ""], [100, 27, -1, ""], [80, 15, 5, ""]], [[45, -29, 4, ""], [45, -26, -11, ""], [69, -19, -5, ""], [45, -29, 4, ""]], [[2, 28, 43, ""], [-27, 28, 38, ""], [-15, 26, 46, ""], [2, 28, 43, ""]], [[35, -31, -42, ""], [52, -31, -10, ""], [38, -45, -7, ""], [35, -31, -42, ""]], [[90, 25, 2, ""], [80, 25, 0, ""], [80, 27, 2, ""], [90, 25, 2, ""]], [[53, -24, 20, ""], [37, -15, 44, ""], [35, -31, 42, ""], [53, -24, 20, ""]], [[-53, 19, -5, ""], [-56, 14, -3, ""], [-83, 13, -5, ""], [-53, 19, -5, ""]], [[0, -47, -44, ""], [35, -47, 13, ""], [-42, -47, -26, ""], [0, -47, -44, ""]], [[-87, -11, 7, ""], [-92, 6, -5, ""], [-90, 9, 2, ""], [-87, -11, 7, ""]], [[100, 27, -1, ""], [83, 26, -4, ""], [86, 25, -3, ""], [100, 27, -1, ""]], [[45, -19, 16, ""], [45, -29, 4, ""], [67, -15, 12, ""], [45, -19, 16, ""]], [[-92, 6, -5, ""], [-97, 11, -6, ""], [-90, 17, -5, ""], [-92, 6, -5, ""]], [[-90, 17, -5, ""], [-83, 13, -5, ""], [-92, 6, -5, ""], [-90, 17, -5, ""]], [[-40, 26, 33, ""], [-15, 26, 46, ""], [-27, 28, 38, ""], [-40, 26, 33, ""]], [[-39, 26, -33, ""], [-13, 26, -46, ""], [-26, -16, -59, ""], [-39, 26, -33, ""]], [[72, 20, -2, ""], [79, 25, 5, ""], [80, 27, 2, ""], [72, 20, -2, ""]], [[-29, 25, 37, ""], [-45, 25, 21, ""], [-47, 28, 17, ""], [-29, 25, 37, ""]], [[-47, 28, 17, ""], [-27, 28, 38, ""], [-29, 25, 37, ""], [-47, 28, 17, ""]], [[-40, 26, 24, ""], [-45, 26, -12, ""], [-47, 25, 0, ""], [-40, 26, 24, ""]], [[2, 28, 43, ""], [-15, 26, 46, ""], [-6, -16, 62, ""], [2, 28, 43, ""]], [[-69, -16, 0, ""], [-66, -16, 20, ""], [-47, 28, 17, ""], [-69, -16, 0, ""]], [[52, -31, -10, ""], [53, -24, 20, ""], [35, -47, 13, ""], [52, -31, -10, ""]], [[-27, 28, 38, ""], [-47, 28, 17, ""], [-40, 26, 33, ""], [-27, 28, 38, ""]], [[-40, 26, 24, ""], [-19, 26, 38, ""], [-8, 34, 6, ""], [-40, 26, 24, ""]], [[0, 34, 1, ""], [-8, 34, 6, ""], [17, 26, 33, ""], [0, 34, 1, ""]], [[-66, -31, 0, ""], [-86, -16, -6, ""], [-66, -29, -4, ""], [-66, -31, 0, ""]], [[0, 45, 9, ""], [3, 46, -5, ""], [-13, 47, 9, ""], [0, 45, 9, ""]], [[13, -24, -60, ""], [37, -15, -44, ""], [35, -31, -42, ""], [13, -24, -60, ""]], [[-97, 11, -6, ""], [-97, 11, 6, ""], [-90, 17, -5, ""], [-97, 11, -6, ""]], [[-8, 34, 6, ""], [-12, 35, -1, ""], [-40, 26, 24, ""], [-8, 34, 6, ""]], [[-53, 26, -8, ""], [-39, 26, -33, ""], [-51, -15, -44, ""], [-53, 26, -8, ""]], [[25, -46, 32, ""], [35, -47, 13, ""], [53, -24, 20, ""], [25, -46, 32, ""]], [[55, -16, 0, ""], [52, -16, -20, ""], [39, 26, -7, ""], [55, -16, 0, ""]], [[-12, 35, -1, ""], [-8, 34, 6, ""], [-6, 37, 6, ""], [-12, 35, -1, ""]], [[-42, -47, -26, ""], [-52, -45, -7, ""], [-67, -24, -20, ""], [-42, -47, -26, ""]], [[-26, -24, 60, ""], [3, -31, 59, ""], [-6, -16, 62, ""], [-26, -24, 60, ""]], [[-45, 25, 21, ""], [-50, 25, -7, ""], [-49, 28, -7, ""], [-45, 25, 21, ""]], [[-49, 28, -7, ""], [-47, 28, 17, ""], [-45, 25, 21, ""], [-49, 28, -7, ""]], [[-87, 18, 5, ""], [-84, 14, 6, ""], [-56, 14, 5, ""], [-87, 18, 5, ""]], [[-97, 11, 6, ""], [-97, 11, -6, ""], [-100, 1, -3, ""], [-97, 11, 6, ""]], [[82, 15, -2, ""], [83, 26, -4, ""], [100, 27, -1, ""], [82, 15, -2, ""]], [[-5, 35, -5, ""], [0, 34, 1, ""], [26, 26, -24, ""], [-5, 35, -5, ""]], [[-47, 28, 17, ""], [-49, 28, -7, ""], [-53, 26, -8, ""], [-47, 28, 17, ""]], [[-12, 46, -10, ""], [3, 46, -5, ""], [-5, 35, -5, ""], [-12, 46, -10, ""]], [[0, 45, 9, ""], [-13, 47, 9, ""], [-6, 37, 6, ""], [0, 45, 9, ""]], [[-67, -24, -20, ""], [-49, -31, -42, ""], [-42, -47, -26, ""], [-67, -24, -20, ""]], [[35, -47, 13, ""], [0, -47, -44, ""], [25, -46, -32, ""], [35, -47, 13, ""]], [[33, 25, 0, ""], [31, 25, -12, ""], [26, 26, -24, ""], [33, 25, 0, ""]], [[26, 26, -24, ""], [32, 26, 12, ""], [33, 25, 0, ""], [26, 26, -24, ""]], [[-80, -13, 2, ""], [-80, -16, -6, ""], [-92, 6, -5, ""], [-80, -13, 2, ""]], [[-26, -24, 60, ""], [-51, -15, 44, ""], [-49, -31, 42, ""], [-26, -24, 60, ""]], [[67, -15, 12, ""], [64, -3, 11, ""], [45, -19, 16, ""], [67, -15, 12, ""]], [[64, -3, 11, ""], [62, 0, -5, ""], [45, -5, 5, ""], [64, -3, 11, ""]], [[-6, 25, 40, ""], [5, 25, 38, ""], [17, 26, 33, ""], [-6, 25, 40, ""]], [[0, -47, -44, ""], [-28, -46, -40, ""], [-16, -31, -59, ""], [0, -47, -44, ""]], [[-28, -46, -40, ""], [-42, -47, -26, ""], [-49, -31, -42, ""], [-28, -46, -40, ""]], [[-50, 25, -7, ""], [-37, 25, -31, ""], [-37, 28, -31, ""], [-50, 25, -7, ""]], [[-37, 28, -31, ""], [-49, 28, -7, ""], [-50, 25, -7, ""], [-37, 28, -31, ""]], [[-67, -24, -20, ""], [-51, -15, -44, ""], [-49, -31, -42, ""], [-67, -24, -20, ""]], [[13, -24, -60, ""], [-7, -16, -62, ""], [-13, 26, -46, ""], [13, -24, -60, ""]], [[45, -26, -11, ""], [45, -8, -12, ""], [64, -11, -14, ""], [45, -26, -11, ""]], [[-26, -16, -59, ""], [-51, -15, -44, ""], [-39, 26, -33, ""], [-26, -16, -59, ""]], [[-49, 28, -7, ""], [-37, 28, -31, ""], [-39, 26, -33, ""], [-49, 28, -7, ""]], [[-39, 26, -33, ""], [-53, 26, -8, ""], [-49, 28, -7, ""], [-39, 26, -33, ""]], [[-86, -16, -6, ""], [-92, 6, -5, ""], [-80, -16, -6, ""], [-86, -16, -6, ""]], [[-100, 1, -3, ""], [-97, 11, -6, ""], [-86, -16, -6, ""], [-100, 1, -3, ""]], [[-66, -16, 20, ""], [-69, -16, 0, ""], [-65, -31, 10, ""], [-66, -16, 20, ""]], [[-69, -21, 3, ""], [-68, -22, -4, ""], [-80, -16, -6, ""], [-69, -21, 3, ""]], [[-16, -31, -59, ""], [-49, -31, -42, ""], [-51, -15, -44, ""], [-16, -31, -59, ""]], [[37, -15, -44, ""], [13, -24, -60, ""], [8, 28, -41, ""], [37, -15, -44, ""]], [[53, -24, 20, ""], [35, -31, 42, ""], [25, -46, 32, ""], [53, -24, 20, ""]], [[17, 26, 33, ""], [-19, 26, 38, ""], [-6, 25, 40, ""], [17, 26, 33, ""]], [[13, -24, -60, ""], [35, -31, -42, ""], [25, -46, -32, ""], [13, -24, -60, ""]], [[-7, -16, -62, ""], [-26, -16, -59, ""], [-13, 26, -46, ""], [-7, -16, -62, ""]], [[86, 25, -3, ""], [91, 26, 0, ""], [100, 27, -1, ""], [86, 25, -3, ""]], [[3, -31, 59, ""], [-26, -24, 60, ""], [-21, -46, 42, ""], [3, -31, 59, ""]], [[-86, -16, -6, ""], [-80, -16, -6, ""], [-66, -29, -4, ""], [-86, -16, -6, ""]], [[-17, 25, -42, ""], [-37, 28, -31, ""], [-37, 25, -31, ""], [-17, 25, -42, ""]], [[52, -16, -20, ""], [55, -16, 0, ""], [52, -31, -10, ""], [52, -16, -20, ""]], [[-18, 46, 1, ""], [-12, 46, -10, ""], [-12, 35, -1, ""], [-18, 46, 1, ""]], [[45, -8, -12, ""], [45, -5, 5, ""], [62, 0, -5, ""], [45, -8, -12, ""]], [[-21, -46, 42, ""], [0, -46, 45, ""], [3, -31, 59, ""], [-21, -46, 42, ""]], [[-37, 28, -31, ""], [-17, 25, -42, ""], [-13, 26, -46, ""], [-37, 28, -31, ""]], [[-13, 26, -46, ""], [-39, 26, -33, ""], [-37, 28, -31, ""], [-13, 26, -46, ""]], [[-13, 26, -46, ""], [8, 28, -41, ""], [13, -24, -60, ""], [-13, 26, -46, ""]], [[-56, 14, -3, ""], [-56, 14, 5, ""], [-84, 14, 6, ""], [-56, 14, -3, ""]], [[13, -16, 59, ""], [37, -15, 44, ""], [26, 26, 33, ""], [13, -16, 59, ""]], [[-6, -16, 62, ""], [13, -16, 59, ""], [2, 28, 43, ""], [-6, -16, 62, ""]], [[82, 15, -2, ""], [80, 15, 5, ""], [67, -15, 12, ""], [82, 15, -2, ""]], [[-87, 18, 5, ""], [-90, 17, -5, ""], [-97, 11, 6, ""], [-87, 18, 5, ""]], [[80, 27, 2, ""], [79, 25, 5, ""], [100, 27, -1, ""], [80, 27, 2, ""]], [[5, 26, -38, ""], [-31, 26, -33, ""], [-5, 35, -5, ""], [5, 26, -38, ""]], [[-19, 26, 38, ""], [17, 26, 33, ""], [-8, 34, 6, ""], [-19, 26, 38, ""]], [[-49, -31, 42, ""], [-65, -31, 10, ""], [-48, -47, 14, ""], [-49, -31, 42, ""]], [[-80, -16, -6, ""], [-80, -13, 2, ""], [-69, -21, 3, ""], [-80, -16, -6, ""]], [[-93, -10, 4, ""], [-100, 1, -3, ""], [-86, -16, -6, ""], [-93, -10, 4, ""]], [[-66, -16, 20, ""], [-51, -15, 44, ""], [-40, 26, 33, ""], [-66, -16, 20, ""]], [[8, 28, -41, ""], [-17, 25, -42, ""], [0, 25, -43, ""], [8, 28, -41, ""]], [[-51, -15, 44, ""], [-66, -16, 20, ""], [-65, -31, 10, ""], [-51, -15, 44, ""]], [[-51, -15, -44, ""], [-67, -24, -20, ""], [-53, 26, -8, ""], [-51, -15, -44, ""]], [[35, -47, 13, ""], [38, -45, -7, ""], [52, -31, -10, ""], [35, -47, 13, ""]], [[83, 26, -4, ""], [80, 27, 2, ""], [80, 25, 0, ""], [83, 26, -4, ""]], [[-48, -47, 14, ""], [-39, -46, 32, ""], [-49, -31, 42, ""], [-48, -47, 14, ""]], [[8, 28, -41, ""], [-13, 26, -46, ""], [-17, 25, -42, ""], [8, 28, -41, ""]], [[3, 46, -5, ""], [0, 45, 9, ""], [-6, 37, 6, ""], [3, 46, -5, ""]], [[-8, 34, 6, ""], [0, 34, 1, ""], [-6, 37, 6, ""], [-8, 34, 6, ""]], [[0, 25, -43, ""], [24, 25, -30, ""], [24, 28, -30, ""], [0, 25, -43, ""]], [[24, 28, -30, ""], [8, 28, -41, ""], [0, 25, -43, ""], [24, 28, -30, ""]], [[-26, -24, 60, ""], [-49, -31, 42, ""], [-39, -46, 32, ""], [-26, -24, 60, ""]], [[67, -15, 12, ""], [69, -19, -5, ""], [82, 15, -2, ""], [67, -15, 12, ""]], [[-47, 28, 17, ""], [-53, 26, -8, ""], [-69, -16, 0, ""], [-47, 28, 17, ""]], [[62, 0, -5, ""], [83, 26, -4, ""], [64, -11, -14, ""], [62, 0, -5, ""]], [[8, 28, -41, ""], [24, 28, -30, ""], [27, 26, -33, ""], [8, 28, -41, ""]], [[-52, -45, -7, ""], [-48, -47, 14, ""], [-65, -31, 10, ""], [-52, -45, -7, ""]], [[-12, 35, -1, ""], [-5, 35, -5, ""], [-31, 26, -33, ""], [-12, 35, -1, ""]], [[52, -31, -10, ""], [35, -31, -42, ""], [37, -15, -44, ""], [52, -31, -10, ""]], [[35, -47, 13, ""], [-48, -47, 14, ""], [-42, -47, -26, ""], [35, -47, 13, ""]], [[80, 25, 0, ""], [86, 25, -3, ""], [83, 26, -4, ""], [80, 25, 0, ""]], [[-48, -47, 14, ""], [0, -46, 45, ""], [-21, -46, 42, ""], [-48, -47, 14, ""]], [[-100, 1, -3, ""], [-93, -10, 4, ""], [-97, 11, 6, ""], [-100, 1, -3, ""]], [[-49, -31, -42, ""], [-16, -31, -59, ""], [-28, -46, -40, ""], [-49, -31, -42, ""]], [[-56, 14, 5, ""], [-53, 20, 3, ""], [-87, 18, 5, ""], [-56, 14, 5, ""]], [[53, -24, 20, ""], [52, -31, -10, ""], [55, -16, 0, ""], [53, -24, 20, ""]], [[0, -46, 45, ""], [25, -46, 32, ""], [35, -31, 42, ""], [0, -46, 45, ""]], [[-67, -24, -20, ""], [-69, -16, 0, ""], [-53, 26, -8, ""], [-67, -24, -20, ""]], [[-45, 26, -12, ""], [-40, 26, 24, ""], [-12, 35, -1, ""], [-45, 26, -12, ""]], [[13, -16, 59, ""], [-6, -16, 62, ""], [3, -31, 59, ""], [13, -16, 59, ""]], [[35, 25, -10, ""], [24, 28, -30, ""], [24, 25, -30, ""], [35, 25, -10, ""]], [[3, -31, 59, ""], [35, -31, 42, ""], [37, -15, 44, ""], [3, -31, 59, ""]], [[27, 26, -33, ""], [39, 26, -7, ""], [52, -16, -20, ""], [27, 26, -33, ""]], [[-90, 9, 2, ""], [-92, 6, -5, ""], [-83, 13, -5, ""], [-90, 9, 2, ""]], [[3, 46, -5, ""], [-12, 46, -10, ""], [-13, 47, 9, ""], [3, 46, -5, ""]], [[24, 28, -30, ""], [35, 25, -10, ""], [39, 26, -7, ""], [24, 28, -30, ""]], [[39, 26, -7, ""], [27, 26, -33, ""], [24, 28, -30, ""], [39, 26, -7, ""]], [[83, 26, -4, ""], [82, 15, -2, ""], [69, -19, -5, ""], [83, 26, -4, ""]], [[32, 26, 12, ""], [26, 26, -24, ""], [0, 34, 1, ""], [32, 26, 12, ""]], [[-84, 14, 6, ""], [-83, 13, -5, ""], [-56, 14, -3, ""], [-84, 14, 6, ""]], [[38, -45, -7, ""], [25, -46, -32, ""], [35, -31, -42, ""], [38, -45, -7, ""]], [[0, -46, 45, ""], [35, -47, 13, ""], [25, -46, 32, ""], [0, -46, 45, ""]], [[80, 27, 2, ""], [100, 27, -1, ""], [90, 25, 2, ""], [80, 27, 2, ""]], [[80, 27, 2, ""], [83, 26, -4, ""], [72, 20, -2, ""], [80, 27, 2, ""]], [[-97, 11, -6, ""], [-92, 6, -5, ""], [-86, -16, -6, ""], [-97, 11, -6, ""]], [[37, -15, -44, ""], [52, -16, -20, ""], [52, -31, -10, ""], [37, -15, -44, ""]], [[-48, -47, 14, ""], [-52, -45, -7, ""], [-42, -47, -26, ""], [-48, -47, 14, ""]], [[0, -47, -44, ""], [-42, -47, -26, ""], [-28, -46, -40, ""], [0, -47, -44, ""]], [[17, 26, 33, ""], [32, 26, 12, ""], [0, 34, 1, ""], [17, 26, 33, ""]], [[8, 28, -41, ""], [27, 26, -33, ""], [37, -15, -44, ""], [8, 28, -41, ""]], [[-93, -10, 4, ""], [-87, -11, 7, ""], [-97, 11, 6, ""], [-93, -10, 4, ""]], [[-19, 26, 38, ""], [-40, 26, 24, ""], [-30, 25, 32, ""], [-19, 26, 38, ""]], [[-26, -24, 60, ""], [-6, -16, 62, ""], [-15, 26, 46, ""], [-26, -24, 60, ""]], [[39, 26, -7, ""], [34, 28, 15, ""], [53, -24, 20, ""], [39, 26, -7, ""]], [[53, -24, 20, ""], [55, -16, 0, ""], [39, 26, -7, ""], [53, -24, 20, ""]], [[-26, -16, -59, ""], [-7, -16, -62, ""], [-16, -31, -59, ""], [-26, -16, -59, ""]], [[52, -16, -20, ""], [37, -15, -44, ""], [27, 26, -33, ""], [52, -16, -20, ""]], [[-67, -24, -20, ""], [-65, -31, 10, ""], [-69, -16, 0, ""], [-67, -24, -20, ""]], [[-90, 9, 2, ""], [-97, 11, 6, ""], [-87, -11, 7, ""], [-90, 9, 2, ""]], [[-51, -15, -44, ""], [-26, -16, -59, ""], [-16, -31, -59, ""], [-51, -15, -44, ""]], [[-31, 26, -33, ""], [5, 26, -38, ""], [-6, 25, -40, ""], [-31, 26, -33, ""]], [[91, 26, 0, ""], [90, 25, 2, ""], [100, 27, -1, ""], [91, 26, 0, ""]], [[-97, 11, 6, ""], [-90, 9, 2, ""], [-84, 14, 6, ""], [-97, 11, 6, ""]], [[34, 28, 15, ""], [26, 26, 33, ""], [37, -15, 44, ""], [34, 28, 15, ""]], [[37, -15, 44, ""], [53, -24, 20, ""], [34, 28, 15, ""], [37, -15, 44, ""]], [[26, 26, -24, ""], [5, 26, -38, ""], [-5, 35, -5, ""], [26, 26, -24, ""]], [[-83, 13, -5, ""], [-84, 14, 6, ""], [-90, 9, 2, ""], [-83, 13, -5, ""]], [[-65, -31, 10, ""], [-49, -31, 42, ""], [-51, -15, 44, ""], [-65, -31, 10, ""]], [[5, 26, -38, ""], [26, 26, -24, ""], [17, 25, -32, ""], [5, 26, -38, ""]], [[-21, -46, 42, ""], [-39, -46, 32, ""], [-48, -47, 14, ""], [-21, -46, 42, ""]], [[-47, 25, 0, ""], [-45, 25, 12, ""], [-40, 26, 24, ""], [-47, 25, 0, ""]], [[-31, 26, -33, ""], [-45, 26, -12, ""], [-12, 35, -1, ""], [-31, 26, -33, ""]]]';

function round(num) {
	return Math.round(num*100)/100;
}

//--></script>

</head>

<body>
<copy>Copyright &copy; 2006 Ben Falconer<br />Licenced under the <a href="http://opensource.org/licenses/osl-2.1.php">Open Software License v. 2.1</a></copy>
<heading>J-Plotter</heading>

<table width=100%>
	<tr>

		<td width="400" rowspan="2">
			<div id="holder"; onmousedown="MouseDown=true; time=0" onmouseup="MouseDown=false" onmousemove="coordinates(event)">
				<labels id="labels"></labels>
				<CANVAS id="canvas" width="400" height="400"></CANVAS>
				<cover id="cover"></cover>
			</div>
		</td>
		<td>
			<div id="angle">
				<top>View:</top><br /><br />

				X angle:
				<input type="button" name="xdecrease" value="-" onclick="document.getElementById('xangle').value-=1; change=1;"><input type="text" name="xangle" id="xangle" value="30" size="1"><input type="button" name="xincreace" value="+" onclick="document.getElementById('xangle').value=Number(document.getElementById('xangle').value)+Number(1); change=1;">
				<br />
				Y angle:
				<input type="button" name="ydecrease" value="-" onclick="document.getElementById('yangle').value-=1; change=1;"><input type="text" name="yangle" id="yangle" value="30" size="1"><input type="button" name="yincreace" value="+" onclick="document.getElementById('yangle').value=Number(document.getElementById('yangle').value)+Number(1); change=1;">
				<br />
				Z angle:
				<input type="button" name="zdecrease" value="-" onclick="document.getElementById('zangle').value-=1; change=1;"><input type="text" name="zangle" id="zangle" value="0" size="1"><input type="button" name="zincreace" value="+" onclick="document.getElementById('zangle').value=Number(document.getElementById('zangle').value)+Number(1); change=1;">
				<br />
				Up/Down:
				<input type="button" name="hdecrease" value="-" onclick="document.getElementById('hdistance').value-=5; change=1;"><input type="text" name="hdistance" id="hdistance" value="200" size="1"><input type="button" name="hincreace" value="+" onclick="document.getElementById('hdistance').value=Number(document.getElementById('hdistance').value)+Number(5); change=1;">
				<br />
				Left/Right:
				<input type="button" name="wdecrease" value="-" onclick="document.getElementById('wdistance').value-=5; change=1;"><input type="text" name="wdistance" id="wdistance" value="200" size="1"><input type="button" name="wincreace" value="+" onclick="document.getElementById('wdistance').value=Number(document.getElementById('wdistance').value)+Number(5); change=1;">
				<br />
				Zoom:
				<input type="button" name="zoomdecrease" value="-" onclick="document.getElementById('zoom').value=round(document.getElementById('zoom').value-5*document.getElementById('zoom').value/400); change=1;"><input type="text" name="zoom" id="zoom" value="400" size="1"><input type="button" name="zoomincreace" value="+" onclick="document.getElementById('zoom').value=round(Number(document.getElementById('zoom').value)+Number(5*document.getElementById('zoom').value/400)); change=1;">
				<br />
				Edge Rendering: <input type="checkbox" id="edgerender" value="edgerender" checked="true" /><br />
				Surface Rendering: <input type="checkbox" id="surfrender" value="surfrender" />
			</div>
		</td>
		<td width=100% height="400px" rowspan="2">

			<div id="help">
				<top>Help:</top><br /><br />
				<center><a href="javascript:changehelp(helpexample);">Examples</a>&nbsp;
					<a href='javascript:changehelp(helpview);'>View</a>&nbsp;
					<a href='javascript:changehelp(helpplot);'>Plotting</a>&nbsp;
					<a href='javascript:changehelp(helpsave);'>Saving</a>&nbsp;
					<a href='javascript:changehelp(helpcopy);'>Copyright</a>
				</center>
				<br>

				<div id="helptext">
					<script>
						document.write(helpexample);
					</script>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="addpoint"; style="clear:both"; >

				<top>Add Points:</top><br /><br />
				X Coordinate:
				<input type="text" id="newpointx" name="newpointx" value="0" size="1" />
				<br />
				Y Coordinate:
				<input type="text" id="newpointy" name="newpointy" value="0" size="1" />
				<br />
				Z Coordinate:
				<input type="text" id="newpointz" name="newpointz" value="0" size="1" />
				<br />

				Label:
				<input type="text" id="newlabel" name="newlabel" value="Label" size="8" />
				<br />
				Add Point to Line:
				<input type="checkbox" id="addline" name="addline" />
				<br />
				<br />
				<center>
					<input type="button" name="createpoint" value="Create Point" onclick="addpoint()" />
				</center>

			</div>
		</td>
	</tr>
</table>
<table width="100%">
	<td width="70%">
		<input type="button" id="save" style="width:15%" name="save" value="Save" onclick="save()" />
		<input type="button" id="save" style="width:15%" name="load" value="Load" onclick="load(document.getElementById('savedata').value)" />
		<!-- The data encoding type, enctype, MUST be specified as below -->
<?php		echo('<form enctype="multipart/form-data" action="./uploadparse.php" method="POST" style="display: inline;">') ?>
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<nobr><input type="hidden" name="MAX_FILE_SIZE" value="60000" />
		<!-- Name of input element determines name in $_FILES array -->
		<input type="submit" value="Upload .obj:" style="width:15%" /><input name="userfile" type="file" /></nobr>
		</form>
	</td>
	<td>

		<h5>Best Viewed in Firefox @ 1024x768</h5>
	</td>
</table>
<textarea id="savedata" style="width:100%;" wrap="logical">
</textarea>

<script type="text/javascript"><!--

//onerror=handleErr
var MouseDown=false;
var canvas=document.getElementById("canvas");
var labels;
//variables initiated at the bottom of the code...
var key=[0,0,0,0,0];		// left, right, up, down

//point[0]=[[1,2,3,"label1"],[4,5,6,"label2"]];
//point[1]=[[7,8,9,"label3"],[10,11,12,"label4"],[13,14,15,"label4"]];

var point;
var pointindex;
var anglex=Number(document.getElementById('xangle').value)+Number(180);
var angley=document.getElementById('yangle').value;
var anglez=document.getElementById('zangle').value;
var xoffset=document.getElementById('wdistance').value;
var yoffset=document.getElementById('hdistance').value;
var zoom=document.getElementById('zoom').value;
var time=0;
var total=0;
var mouseX;
var mouseY;
var realx;
var realy;
var firstx;
var finalx;
var firsty;
var finaly;
var firstz;
var finalz;
var current;
var change=0;
var surfrender;
var edgerender;

function drawCanvas() {
	canvas.clearRect(0,0,400, 400);
	canvas.strokeStyle="#ffffff";
	canvas.strokeWidth="0.1";
	for (var i=0; i<point.length; i++) {
		canvas.beginPath();
		rotate(i,0);
		canvas.moveTo(realx, realy);
		for (var j=0; j<point[i].length; j++) {
			rotate(i,j);
			current='label'+i+j;
			if (point[i].length!=1) {
				canvas.lineTo(realx, realy);
			}
			else {
				canvas.moveTo(realx+3, realy+3);
				canvas.lineTo(realx-3, realy-3);
				canvas.moveTo(realx-3, realy+3);
				canvas.lineTo(realx+3, realy-3);
			}
			if (realx < 3) realx = 3;
			if (realx > 400) realx = 400;
			if (realy < 0) realy = 0;
			if (realy > 385) realy = 385;

			if(point[i][j][3]!=""&&point[i][j][3]) {
				if (!total) {
					document.getElementById("labels").innerHTML+='<div id="label'+i+j+'" style="position:absolute; width:400px;">'+point[i][j][3]+'</div>';
				}
				document.getElementById(current).style.width=(realx-3)+"px";
				document.getElementById(current).style.paddingTop=realy+"px";
			}
		}
		if (surfrender) {
			shademod=2;
			canvas.globalCompositeOperation="destination-over";
			shade="rgba(100,100,100,0.5)";
			canvas.fillStyle=shade;
			canvas.fill();
		}
		if (edgerender) {
			canvas.globalCompositeOperation="source-over";
			canvas.stroke();
		}
	}
total++
}

function addpoint() {
	newpointx=Number(document.getElementById('newpointx').value);
	newpointy=Number(document.getElementById('newpointy').value);
	newpointz=Number(document.getElementById('newpointz').value);
	newlabel=document.getElementById('newlabel').value;
	addline=document.getElementById('addline').checked;
	if(!addline) {
		point[point.length]=[[newpointx,newpointy,newpointz,newlabel]];
		if(newlabel!=""&&newlabel) {
			document.getElementById("labels").innerHTML+='<div id="label'+(point.length-1)+'0'+'" style="position:absolute; width:400px;">'+newlabel+'</div>';
		}
	}
	else {
		point[point.length-1][point[point.length-1].length]=[newpointx,newpointy,newpointz,newlabel];
		if(newlabel!="") {
			document.getElementById("labels").innerHTML+='<div id="label'+(point.length-1)+(point[point.length-1].length-1)+'" style="position:absolute; width:400px;">'+newlabel+'</div>';
		}
	}
}

function rotate(i,j) {
	//rotate y
	firstx=Math.cos(angley*2*pi/360) * point[i][j][0] + Math.sin(angley*2*pi/360) * point[i][j][2];
	firstz=-Math.sin(angley*2*pi/360) * point[i][j][0] + Math.cos(angley*2*pi/360) * point[i][j][2];

	//rotate x
	firsty=Math.cos(anglex*2*pi/360) * point[i][j][1] - Math.sin(anglex*2*pi/360) * firstz;
	finalz=Math.sin(anglex*2*pi/360) * point[i][j][1] + Math.cos(anglex*2*pi/360) * firstz;

	//rotate z
	finalx=Math.cos(anglez*2*pi/360) * firstx - Math.sin(anglez*2*pi/360) * firsty;
	finaly=Math.sin(anglez*2*pi/360) * firstx + Math.cos(anglez*2*pi/360) * firsty;
		
	realx=finalx*Number(zoom)/(400+finalz) + Number(xoffset);
	realy=finaly*Number(zoom)/(400+finalz) + Number(yoffset);
}

function save() {
	document.getElementById("savedata").value=point.toSource();
}

function load(data) {
	eval("point="+data);
	document.getElementById("labels").innerHTML="";
	total=0;
}

function handleErr(msg,l)
{
	txt="There was an error on this page.\n\n"
	txt+="Error: " + msg + "\n"
	txt+="Line: " + l + "\n\n"
	txt+="Click OK to continue.\n\n"
	alert(txt)
	return true
}

function changehelp(page) {
	document.getElementById('helptext').innerHTML=page;
}

function coordinates(e) {
	if (MouseDown){
		if (!e) var e = window.event;
		if (e.pageX || e.pageY)
		{
			posx = e.pageX;
			posy = e.pageY;
		}
		else if (e.clientX || e.clientY)
		{
			posx = e.clientX + document.body.scrollLeft;
			posy = e.clientY + document.body.scrollTop;
		}
		if (!time){
			mouseX=posx;
			mouseY=posy;
			rotx=document.getElementById('xangle').value;
			roty=document.getElementById('yangle').value;
		}
		time++;
		// posx and posy contain the mouse position relative to the document
		document.getElementById('xangle').value=Number(rotx)-(Number(mouseY/2)-Number(posy/2));
		document.getElementById('yangle').value=Number(roty)-(Number(mouseX/2)-Number(posx/2));
	}
}

function update() {

	if (document.getElementById('xangle').value < 0) document.getElementById('xangle').value=Number(document.getElementById('xangle').value)+Number(360);
	if (document.getElementById('xangle').value >= 360) document.getElementById('xangle').value-=360;
	anglex=Number(document.getElementById('xangle').value)+Number(180);

	if (document.getElementById('yangle').value < 0) document.getElementById('yangle').value=Number(document.getElementById('yangle').value)+Number(360);
	if (document.getElementById('yangle').value >= 360) document.getElementById('yangle').value-=360;
	angley=document.getElementById('yangle').value;

	if (document.getElementById('zangle').value < 0) document.getElementById('zangle').value=Number(document.getElementById('zangle').value)+Number(360);
	if (document.getElementById('zangle').value >= 360) document.getElementById('zangle').value-=360;
	anglez=document.getElementById('zangle').value;

	xoffset=document.getElementById('wdistance').value;
	yoffset=document.getElementById('hdistance').value;
	zoom=document.getElementById('zoom').value;
	surfrender=document.getElementById('surfrender').checked;
	edgerender=document.getElementById('edgerender').checked;

	//if (change) {
		drawCanvas();
	//}
	change=0;
}

function makeRequest(url) {
	http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			//http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = recieveUpload;

	http_request.open('GET', url, true);

	http_request.send(null);
}

function recieveUpload() {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			uploaded=http_request.responseText;
			load(uploaded);
		} else {
			alert('There was a problem with the request.');
		}
	}
}

function changeKey(which, to) {
	switch (which){
		case 65: case 37: key[0]=to; break; // left
		case 87: case 38: key[2]=to; break; // up
		case 68: case 39: key[1]=to; break; // right
		case 83: case 40: key[3]=to; break;// down
		case 32: key[4]=to; break; // space bar;
		case 17: key[5]=to; break; // ctrl
	}
}

document.onkeydown=function(e){changeKey(e.keyCode, 1);}
document.onkeyup=function(e){changeKey(e.keyCode, 0);}

if (!canvas.getContext) {
	document.getElementById("holder").innerHTML='<img src="StaticGraph.jpg">';
	alert("BROWSER INCOMPATABILITY\n\nTo intereact with the graph you need a browser that supports the\n<canvas> HTML element with JavaScript enabled such as\nFirefox 1.5or above, Opera 9or above, or Safari 1.3 or above.");
}
else window.onload=function(){
	canvas=document.getElementById("canvas").getContext("2d");
<?php
if ($_GET['mod']) {
	echo "makeRequest(\"./models/".$_GET['mod']."\");\n";
}
else {
	echo "load(example1);";
	echo "drawCanvas();";
}
?>
	setInterval(update, 35);
}
//--></script>


</body>
</html>
