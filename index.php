<?php

if (!isset($_GET['action'])) {
	$_GET['action'] = '';
}
session_start();

switch($_GET['action']){
	case 'addTopping': 
		$result = array();
		$result['errormsg'] = '';
		$result['success'] = 0;

		if (isset($_GET['topping']) && strlen(str_replace(' ', '', $_GET['topping'])) > 0 ) {
			if (!isset($_SESSION['toppings'])) {
				$_SESSION['toppings'] = array();
			}
			if(array_key_exists($_GET['topping'], $_SESSION['toppings'])) {
				$result['success'] = 0;
				$result['errormsg'] = 'topping already exists.';
			} else {
				$newTopping = [
					"id" => trim(bin2hex(random_bytes(10))),
					"name" => $_GET['topping'],
					"price" => 10
				];
				$_SESSION['toppings'][] = $newTopping;
				$result['success'] = 1;
				$result['topping'] = $newTopping;
			}
		} else {
			$result['success'] = 0;
			$result['errormsg'] = 'No Topping Entered';
		}
		echo json_encode($result);
		exit;
	break;

	case 'getToppings'; 
		$result = array();
		$result['errormsg'] = '';
		$result['success'] = 1;
		$result['toppings'] = array();

		if (isset($_SESSION['toppings'])) {
			$result['toppings'] = $_SESSION['toppings'];
			$result['success'] = 1;
		}

		echo json_encode($result);
		exit;
	break;

	case 'deleteTopping':
		$result = array();
		$result['errormsg'] = '';
		$result['success'] = 0;

		$toppingsBefore = count($_SESSION['toppings']);
		$toppings = $_SESSION['toppings'];

		if(sizeof($_SESSION['toppings']) > 1) {
			$arrayToppings = array_filter($_SESSION['toppings'], function ($topping) {
				return $topping['id'] != $_GET['toppingId'];
			});
			$_SESSION['toppings'] = $arrayToppings;
		} else{
			unset($_SESSION['toppings']);
		}

		if(!isset($_SESSION['toppings'])){
			$result['success'] = 1;
			echo json_encode($result);
			return;
		}

		if(count($_SESSION['toppings']) < $toppingsBefore){
			$result['success'] = 1;
		}else{
			$result['errormsg'] = "The topping was not removed";
		}
		echo json_encode($result);
		exit;
	break;

	default: 
		printForm();
}


function printForm()
{ ?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">
		<title>Pizza Pizza!</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="styles.css">
		<!-- BOOTSTRAP -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
			integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
			crossorigin="anonymous"></script>
		<script src="jquery.min.js"></script>
	</head>

	<body>
		<header>
			<div class="brand">
				<span>Mendoza</span>
				<span>Corporation</span>
			</div>
			<div class="slogan">
				<span>Because every pizza should be possible!</span>
			</div>
			<div id="header-total">
				<button class="actions--button__pay" data-bs-toggle="modal" data-bs-target="#modal">Purchase</button>
				<span>Total:</span>
				<span id="total-price"></span>
			</div>
		</header>

		<div class="content">
			<div class="step first-step">
				<div class="first-step-container">
					<h4>Type of pizza</h4>
					<div class="step-option">
						<input type="radio" name="pizza-button" value="vegetarian" data-price="20">
						<div>Vegetarian &#128536;</div>
					</div>
					<div class="step-option">
						<input type="radio" name="pizza-button" value="traditional" data-price="20" checked>
						<div>Traditional &#128515;</div>
					</div>
					<div class="step-option">
						<input type="radio" name="pizza-button" value="exotic" data-price="20">
						<div>Exotic &#128527;</div>
					</div>
				</div>

				<div class="first-step-container">
					<h4>Size</h4>
					<div class="step-option">
						<input type="radio" name="size-button" value="small" data-price="3">
						<div>Small &#128077;</div>
						<span class="step-option__price">+3$</span>
					</div>
					<div class="step-option">
						<input type="radio" name="size-button" value="medium" data-price="5" checked>
						<div>Medium &#128077;</div>
						<span class="step-option__price">+5$</span>
					</div>
					<div class="step-option">
						<input type="radio" name="size-button" value="large" data-price="10">
						<div>Large &#128079;</div>
						<span class="step-option__price">+10$</span>
					</div>
					<div class="step-option">
						<input type="radio" name="size-button" value="extra-large" data-price="17">
						<div>XX large &#128080;</div>
						<span class="step-option__price">+17$</span>
					</div>
				</div>

				<!-- DISPLAY ONLY WEN SCREEN WIDTH IS UP 800px -->
				<div class="second-step-container" id="first-step-dough">
					<h4>Dough</h4>
					<div id="options-dough">
						<div class="step-option">
							<input type="radio" name="dough-button" id="crunchy" value="2" data-price="2">
							<div>Crunchy &#127770;</div>
							<span class="step-option__price">+2$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" id="original" value="4" data-price="4">
							<div>Original &#127773;</div>
							<span class="step-option__price">+4$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" id="double-chees" value="9" data-price="9">
							<div>Double cheese &#127773;&#127773;</div>
							<span class="step-option__price">+9$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" id="super-sexy" value="15" data-price="15">
							<div>No way! Super sexy extra cheese! &#128525;</div>
							<span class="step-option__price">+15$</span>
						</div>
					</div>
				</div>
			</div>

			<!-- DISPLAY ONLY FOR MOBILE -->
			<div class="step second-step">
				<div class="step-header">
					<span class="close-step" data-close="dough">hide</span>
				</div>
				<div class="second-step-container" id="dough-container">
					<h4>Dough</h4>
					<div id="mobile-dough-options">
						<div class="step-option">
							<input type="radio" name="dough-button" value="crunchy" data-price="2" checked>
							<div>Crunchy &#127770;</div>
							<span class="step-option__price">+2$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" value="original" data-price="4">
							<div>Original &#127773;</div>
							<span class="step-option__price">+4$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" value="double-cheese" data-price="9">
							<div>Double cheese &#127773;&#127773;</div>
							<span class="step-option__price">+9$</span>
						</div>
						<div class="step-option">
							<input type="radio" name="dough-button" value="super-sexy" data-price="15">
							<div id="step-option-short-super-sexy">Super sexy extra cheese! &#128525;</div>
							<div id="step-option-long-super-sexy">No way! Super sexy extra cheese! &#128525;</div>
							<span class="step-option__price">+15$</span>
						</div>
					</div>
				</div>
			</div>

			<div class="step">
				<div class="toppings">
					<div class="step-header">
						<span class="close-step" data-close="pizza">hide</span>
					</div>
					<h4>Toppings</h4>
					<div id="mobile-pizza-options">
						<div class="list" id="topping-list-container"></div>
					</div>
				</div>
			</div>

			<div class="step">
				<div class="toppings">
					<div class="step-header">
						<span class="close-step" data-close="new-topping">hide</span>
					</div>
					<h4>Custom toppings</h4>
					<span>If these are not enough for you, go ahead adding your own toppings here below.</span><br>
					<div id="mobile-new-topping-options">
						<div class="form-group">
							<input type="text" class="form-control" name="add-topping" id="add-topping-input" placeholder="New topic name">
							<em class="fa-solid fa-circle-plus" data-toggle="tooltip" data-placement="top" title="Add new topping" id="add-topping-button"></em>
						</div>
						<small class="ms-2 text-danger" id="add-topping-error"></small>
						<div class="list" id="own-topping-list-container"></div>
					</div>
					<br>
					<small class="text-muted">Note: custom topics prices are calculated automatically by a sofisticated tool.</small>
				</div>
			</div>

			<!-- MODAL -->
			<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<div class="modal-body">
						<span>You have purchased a very HOT pizza! 🍕🍕🍕 </span><br>
						<span>just for $<span id="buy-total"></span> &#128565;</span> That's crazy!
						<hr>
						<span>We are going to be with you in less than 30 minutes &#128520;... or it will be for free!</span>
					</div>
					</div>
				</div>
			</div>
			<!-- MODAL -->
		</div>

		<footer>
			<span>All rights reserved &copy; / <a target="_blank" href="https://unilinktransportation.com/">unilinktransportation.com</a> / <a target="_blank" href="https://twitter.com/ivan_delvalle10">Twitter</a> /
				<a target="_blank" href="https://github.com/ivandelvaller">GitHub</a> </span>
		</footer>

		<script src="./index.js"></script>
	</body>

	</html>
<?php
}
?>