<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Restaurant Details</title>
	<!-- Include your CSS stylesheets here -->
	<style>
		/* Reset some default styles for consistency */
		body,
		h1,
		h2,
		table {
			margin: 0;
			padding: 0;
		}

		/* Global Styles */
		body {
			font-family: Arial, sans-serif;
			background-color: #f5f5f5;
			color: #333;
			line-height: 1.6;
			margin: 0;
			padding: 0;
		}

		.container {
			max-width: 800px;
			margin: 0 auto;
			padding: 20px;
		}

		header {
			background-color: #f5f5f5;
			color: #333333;
			text-align: center;
			padding: 20px 0;
			position: relative;
		}

		h1 {
			font-size: 24px;
		}

		section {
			background-color: #fff;
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 20px;
			margin-top: 20px;
		}

		h2 {
			font-size: 20px;
			margin-bottom: 20px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th,
		td {
			padding: 10px;
			border-bottom: 1px solid #ddd;
		}

		th {
			background-color: #808090;
			color: #fff;
			text-align: left;
		}

		td {
			background-color: #e5e5e5;
			color: #000000;
			text-align: left;
		}

		/* Back button styles */
		.back-button {
			position: absolute;
			left: 20px;
			top: 50%;
			transform: translateY(-50%);
			background-color: #808090;
			color: #fff;
			border: none;
			padding: 10px;
			border-radius: 5px;
			cursor: pointer;
		}

		.back-button:hover {
			background-color: #666677;
		}

		/* Footer Styles (customize as needed) */
		footer {
			text-align: center;
			padding: 10px 0;
			background-color: #333;
			color: #fff;
		}

		/* Responsive Styles (optional) */
		@media (max-width: 768px) {
			.container {
				padding: 10px;
			}
		}

		.uppercase-text {
			text-transform: uppercase;
		}
	</style>
</head>

<body>
	<header>
		<a href="<?php echo admin_url('restaurant'); ?>"><button class="back-button">← Back</button></a>
		<h1>Restaurant Details</h1>
	</header>
	<section>
		<h2 class="uppercase-text "><?php echo $res[0]['business_name']; ?></h2>
		<table>
			<tr>
				<th>Business Plan</th>
				<td><?php echo $plan[0]['restaurant_plan']; ?></td>
			</tr>
			<tr>
				<th>Next Due Date</th>
				<td><?php echo $plan_duration[0]['nextduedate']; ?></td>
			</tr>
			<tr>
				<th>Email</th>
				<td><?php echo  $client[0]['client_email']; ?></td>
			</tr>
			<tr>
				<th>Phone Number</th>
				<td><?php echo  $client[0]['client_phoneNumber']; ?></td>
			</tr>
			<tr>
				<th>Address</th>
				<td><?php echo $res[0]['b_address']; ?></td>
			</tr>
			<tr>
				<th>Postcode</th>
				<td><?php echo $res[0]['b_postcode']; ?></td>
			</tr>
			<tr>
				<th>State</th>
				<td><?php echo $res[0]['b_state']; ?></td>
			</tr>
			<tr>
				<th>Country</th>
				<td><?php echo $res[0]['b_country']; ?></td>
			</tr>
			<tr>
				<th>Seating Capacity</th>
				<td><?php echo $res[0]['seating_capacity']; ?></td>
			</tr>
			<tr>
				<th>Cuisines Available</th>
				<td><?php for($i=0; $i <count($cuisine) ; $i++) { 
					echo ($i+1).'.' . $cuisine[$i]['cuisine_id'].'</br>';
				}?></td>
			</tr>
			<tr>
				<th>Sales Channel</th>
				<td><?php for($i=0; $i <count($sales_channel) ; $i++) { 
					echo ($i+1).'.' . $sales_channel[$i]['reach'].'</br>';
				}?></td>
			</tr>
		</table>
	</section>
	<footer>
	</footer>
</body>

</html>
