<?php
$theme = "dark";
$title = "TinyVPS";
$intro = "This is a simple website running on a <a href='https://contabo.com/'>Contabo</a> VPS.";
$city = "Fürth";
$country = "DE";
$key = "f2871760abe7575931575759cf85bd3c";
$links = array(
	array('lilut/', 'Lilut'),
	array('https://tokyoma.de/', 'Tōkyō Made'),
	array('https://github.com/dmpop', 'GitHub')
);
$feeds = array(
	"https://lowendbox.com/feed/"

);
$footer = "I really 🧡 <a href='https://www.paypal.com/paypalme/dmpop'>coffee</a>";
?>

<html lang="en" data-theme="<?php echo $theme; ?>">
<!-- Author: Dmitri Popov, dmpop@linux.com
         License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title><?php echo $title; ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="css/classless.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<div style="text-align: center;">
		<img style="height: 3em; margin-bottom: 1em;" src="favicon.svg" alt="logo" />
		<h1 style="margin-top: 0em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<p>
			<?php echo $intro; ?>
		</p>
		<h4><span style="color: gray;">🌤️ <?php echo $city ?>:</span>
			<?php
			$request = "https://api.openweathermap.org/data/2.5/weather?APPID=$key&q=$city,$country&units=metric&cnt=7&lang=en&units=metric&cnt=7";
			$response = file_get_contents($request);
			$data = json_decode($response, true);
			//Get current Temperature in Celsius
			echo round($data['main']['temp'], 0) . "°C, ";
			//Get weather condition
			echo $data['weather'][0]['main'] . ", ";
			//Get wind speed
			echo $data['wind']['speed'] . "m/s";
			?>
		</h4>
	</div>
	<h3>🖥️ System info</h3>
	<hr>
	<?php
	$uname = shell_exec("uname -mnr");
	$cpuusage = 100 - shell_exec("vmstat | tail -1 | awk '{print $15}'");
	$mem = shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'");
	$mem = round($mem, 1);
	if (isset($uname)) {
		echo "<p>" . $uname . "</p>";
	}
	if (isset($cpuusage) && is_numeric($cpuusage)) {
		echo '<span style="color: gray;">CPU load:</span> <strong>' . $cpuusage . '%</strong> ';
	}
	if (isset($mem) && is_numeric($mem)) {
		echo '<span style="color: gray;">Memory:</span> <strong>' . $mem . '%</strong>';
	}
	?>
	<h3>🔗 Links</h3>
	<hr>
	<ul>
		<?php
		$array_length = count($links);
		for ($i = 0; $i < $array_length; $i++) {
			echo '<li><a href="' . $links[$i][0] . '">' . $links[$i][1] . '</a></li>';
		}
		?>
	</ul>
	<h3>🔥 Feeds</h3>
	<hr>
	<?php
	$array_length = count($feeds);
	for ($i = 0; $i < $array_length; $i++) {
		$rss = simplexml_load_file($feeds[$i]);
		echo '<h4>' . $rss->channel->title . '</h4>';
		echo "<ul>";
		foreach ($rss->channel->item as $item) {
			echo '<li style="font-size: 85%"><a href="' . $item->link . '">' . $item->title . "</a></li>";
		}
		echo "</ul>";
	}
	?>
	<hr>
	<p class="text-center"><?php echo $footer ?></p>
</body>

</html>