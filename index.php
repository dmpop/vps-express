<?php
$theme = "dark";
$title = "TinyVPS";
$intro = "This is a simple website running on a <a href='https://contabo.com/'>Contabo</a> VPS.";
$city = "Fürth";
$country = "DE";
$key = "f2871760abe7551904065759cf85bd3c";
$links = array(
	array('https://lilut.xyz', 'Lilut'),
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
	<link rel="stylesheet" href="css/themes.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<div style="text-align: center;">
		<img style="height: 3em; margin-bottom: 1em;" src="favicon.svg" alt="logo" />
		<h1 style="margin-top: 0em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<p>
			<?php echo $intro; ?>
		</p>
	</div>
	<h3>🌤️ Weather in <?php echo $city ?></h3>
	<hr>
	<p>
		<?php
		$request = "https://api.openweathermap.org/data/2.5/forecast?APPID=$key&q=$city,$country&units=metric&cnt=7&lang=en&units=metric&cnt=7";
		$response = file_get_contents($request);
		$data = json_decode($response, true);
		//Get current temperature in Celsius
		echo round($data['list'][0]['main']['temp'], 0) . "°C, ";
		//Get weather condition
		echo $data['list'][0]['weather'][0]['description'] . ", ";
		//Get wind speed
		echo $data['list'][0]['wind']['speed'] . " m/s";
		echo "<table style='margin-top: 1.5em;'>";
		// Today + 1
		echo "<tr>";
		echo "<td>";
		echo ' <span style="color: gray;">'. date("l", strtotime( "+ 1 day" )) . ':</span> ';
		echo "</td>";
		echo "<td>";
		echo round($data['list'][1]['main']['temp'], 0) . "°C, ";
		echo $data['list'][1]['weather'][0]['description'] . ", ";
		echo $data['list'][1]['wind']['speed'] . " m/s";
		echo "</td>";
		echo "</tr>";
		// Today + 2
		echo "<tr>";
		echo "<td>";
		echo ' <span style="color: gray;">'. date("l", strtotime( "+ 2 days" )) . ':</span> ';
		echo "</td>";
		echo "<td>";
		echo round($data['list'][2]['main']['temp'], 0) . "°C, ";
		echo $data['list'][2]['weather'][0]['description'] . ", ";
		echo $data['list'][2]['wind']['speed'] . " m/s";
		echo "</td>";
		echo "</tr>";
		// Today + 3
		echo "<tr>";
		echo "<td>";
		echo ' <span style="color: gray;">'. date("l", strtotime( "+ 3 days" )) . ':</span> ';
		echo "</td>";
		echo "<td>";
		echo round($data['list'][3]['main']['temp'], 0) . "°C, ";
		echo $data['list'][3]['weather'][0]['description'] . ", ";
		echo $data['list'][3]['wind']['speed'] . " m/s";
		echo "</td>";
		echo "</tr>";
		// Today + 4
		echo "<tr>";
		echo "<td>";
		echo ' <span style="color: gray;">'. date("l", strtotime( "+ 4 days" )) . ':</span> ';
		echo "</td>";
		echo "<td>";
		echo round($data['list'][4]['main']['temp'], 0) . "°C, ";
		echo $data['list'][4]['weather'][0]['description'] . ", ";
		echo $data['list'][4]['wind']['speed'] . " m/s";
		echo "</td>";
		echo "</tr>";
		// Today + 5
		echo "<tr>";
		echo "<td>";
		echo ' <span style="color: gray;">'. date("l", strtotime( "+ 5 days" )) . ':</span> ';
		echo "</td>";
		echo "<td>";
		echo round($data['list'][5]['main']['temp'], 0) . "°C, ";
		echo $data['list'][5]['weather'][0]['description'] . ", ";
		echo $data['list'][5]['wind']['speed'] . " m/s";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		?>
	</p>
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