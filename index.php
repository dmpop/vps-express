<?php
include('config.php');
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
			<img style="margin-top: 1em; margin-bottom: 1em; border-radius: 5px;" src="https://source.unsplash.com/daily">
		</p>
	</div>
	<p id="geolocation"></p>
	<script>
		window.onload = getLocation();
		var x = document.getElementById("geolocation");

		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(currentPosition);
			} else {
				x.innerHTML = "Geolocation is not supported.";
			}
		}

		function currentPosition(position) {
			document.cookie = "posLat = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "posLon = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "posLat = " + position.coords.latitude;
			document.cookie = "posLon = " + position.coords.longitude;
		}
	</script>
	<p>
		<?php
		setcookie("posLat", "", time() - 3600);
		setcookie("posLon", "", time() - 3600);
		$lat = $_COOKIE['posLat'];
		$lon = $_COOKIE['posLon'];
		$request = "https://api.openweathermap.org/data/2.5/forecast/daily?lat=$lat&lon=$lon&units=metric&cnt=7&lang=en&units=metric&cnt=7&appid=$key";
		$response = file_get_contents($request);
		$data = json_decode($response, true);
		echo "<h3>🌤️ Weather forecast for " . $data['city']['name'] . "</h3>";
		echo "<hr>";
		echo "<table style='margin-top: 1.5em;'>";
		for ($i = 0; $i <= 6; $i++) {
			echo "<tr>";
			echo "<td>";
			echo ' <span style="color: gray;">' . date("l", strtotime("+ $i day")) . ':</span> ';
			echo "</td>";
			echo "<td style='text-align: left'>";
			echo "<span style='color: #03a9f4;'>" . round($data['list'][$i]['temp']['day'], 0) . "°C</span> ";
			echo $data['list'][$i]['weather'][0]['description'] . " ";
			echo "<span style='color: #26a69a;'>" . $data['list'][$i]['speed'] . " m/s</span> ";
			echo "<span style='color: #ff9800;'>&#8593;" . date("H:i", $data['list'][$i]['sunrise']) . " ";
			echo "&#8595;" . date("H:i", $data['list'][$i]['sunset']) . "</span>";
			echo "</td>";
			echo "</tr>";
		}
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