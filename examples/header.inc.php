<?php
// This example header.inc.php is intended to be modfied for your application.
use QCubed as Q;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php echo(QCUBED_ENCODING); ?>"/>
	<meta content="text/html"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<?php if (isset($strPageTitle)){ ?><title><?php _p($strPageTitle); ?></title><?php } ?>

	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="<?= QCUBED_PROJECT_CSS_URL ?>/font-awesome.min.css" rel="stylesheet"/>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="/qcubed-4/vendor/kukrik/bootstrap-filecontrol/assets/css/jquery.fileupload.css" rel="stylesheet" />
    <link href="/qcubed-4/vendor/kukrik/bootstrap-filecontrol/assets/css/jquery.fileupload-ui.css" rel="stylesheet" />
	<link href="../assets/css/scrollpad.css" rel="stylesheet"/>
	<link href="../assets/css/filemanager.css" rel="stylesheet"/>
	<link href="../assets/css/awesome-bootstrap-checkbox.css" rel="stylesheet"/>

	<style>
		.svg-icon {
			width: 1em;
			height: 1em;
			font-size: 24px;
			vertical-align: bottom;
			pointer-events: none
		}

		.svg-preloader {
			width: 18px;
			stroke: white;
			display: none
		}

		.svg-preloader-circle {
			display: inline-block;
			fill: transparent;
			stroke-linecap: round;
			stroke-dasharray: 100;
			stroke-dashoffset: 80;
			stroke-width: 2px;
			transform-origin: 50% 50%
		}

		.svg-preloader-active {
			display: inline-block;
			animation: 2s linear infinite spin
		}

		.svg-preloader-active .svg-preloader-circle {
			animation: 1.4s ease-in-out infinite both circle-anim
		}

		@keyframes spin {
			from {
				transform: rotate(0deg)
			}
			to {
				transform: rotate(360deg)
			}
		}

		@keyframes circle-anim {
			0%, 25% {
				stroke-dashoffset: 80;
				transform: rotate(0)
			}
			50%, 75% {
				stroke-dashoffset: 20;
				transform: rotate(45deg);
				stroke-width: 1px;
				stroke: var(--preloader-anim-stroke, inherit)
			}
			100% {
				stroke-dashoffset: 80;
				transform: rotate(360deg)
			}
		}

		.preloader-body {
			stroke: #B0BEC5;
			position: absolute;
			top: calc(50% - 48px / 2);
			left: calc(50% - 48px / 2);
			width: 48px
		}
	</style>
</head>
	<body>