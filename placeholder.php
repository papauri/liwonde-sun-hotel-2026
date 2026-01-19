<?php
// Placeholder image generator for Liwonde Sun Hotel website
// This script generates placeholder images when actual images are not available

// Set content type to JPEG
header('Content-Type: image/jpeg');

// Get dimensions from GET parameters, default to 800x600
$width = isset($_GET['w']) ? intval($_GET['w']) : 800;
$height = isset($_GET['h']) ? intval($_GET['h']) : 600;
$text = isset($_GET['text']) ? $_GET['text'] : 'Liwonde Sun Hotel';

// Create image canvas
$image = imagecreate($width, $height);

// Define colors
$bg_color = imagecolorallocate($image, 26, 71, 42); // Primary green
$text_color = imagecolorallocate($image, 212, 175, 55); // Gold accent
$border_color = imagecolorallocate($image, 255, 255, 255); // White

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Draw border
imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);

// Calculate text size and position
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;

// Add text to center
imagestring($image, $font_size, $x, $y, $text, $text_color);

// Add dimensions text
$dimensions = "{$width}x{$height}";
$dim_width = imagefontwidth(3) * strlen($dimensions);
$dim_x = ($width - $dim_width) / 2;
imagestring($image, 3, $dim_x, $y + 20, $dimensions, $text_color);

// Output image
imagejpeg($image);

// Free memory
imagedestroy($image);
?>