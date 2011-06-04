<html>
<head>
 <title>Project Neuro</title>
</head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.js"></script>

<style>
body {
  background-color: #000000;
  width: 100%;
  height: 100%;
  font-family: Georgia;
}
#back { 
  background-image: url('background.jpg');
  position: absolute;
  top: -600px;
  left: -700px;
  width: 3380px;
  height: 2152px;
  z-index: 1;
  -webkit-transition: -webkit-transform 300s linear;
}
#content {
  position: relative;
  width: 1000px;
  margin-left: auto;
  margin-right: auto;
  padding-top: 10px;
  color: #fff;
  z-index: 3;
}
.description {
  margin: 20px 35px;
  color: 9999FF;
  position: relative;
  z-index: 3;
  font-family: monospace;
]
</style>
<script>
$(document).ready(function() {
//  $('#back').style.webkitTransform = 'rotate(360deg)';
  $('#back').css('-webkit-transform', 'rotate(360deg)');
  $('#back').animate({left: -300, top: -300}, 120000, function(){});
});
</script>
<body>
 <div id="back" onclick="//this.style.webkitTransform = 'rotate(360deg)';"></div>
 <div id="content"> 
   <h1 style="text-align: center;">Project Neuro</h1>
 </div>
<div class="description"><?php echo nl2br(str_replace('  ', '&nbsp;&nbsp;', join('', file('README')))); ?></div>
  
</body>
</html>