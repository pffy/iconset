#!/usr/bin/php
<?php

## name     : iconset.php
## author   : The Pffy Authors https://pffy.dev
## git      : https://github.com/pffy/iconset
## license  : https://opensource.org/licenses/MIT

date_default_timezone_set('America/Los_Angeles');
setlocale(LC_ALL,'en_US.UTF-8');

process_arguments_flags();

// infiles
$files = [];
process_arguments_files();

if($files) {

  foreach($files as $file) {
    resize_icon_from_file($file);
  }

  exit();
}


$regex_rgb = '/rgb-(\d{1,3})-(\d{1,3})-(\d{1,3})$/';

preg_match_all($regex_rgb, $argv[1], $p);

$r = $g = $b = 0;
if(isset($p[0][0])){

  $r = min(255, $p[1][0]);
  $g = min(255, $p[2][0]);
  $b = min(255, $p[3][0]);

  $rgb = [
    'r' => $r,
    'g' => $g,
    'b' => $b
  ];

  make_icon_set(2048, $rgb);
  exit();
}

preg_match_all('/([[:xdigit:]]){3,6}$/', $argv[1], $m);

if(!isset($m[0][0])){
  msg('Invalid hexadecimal color. Exiting.');
  exit();
}

$moo = strtoupper($m[0][0]);

switch(strlen($moo)) {

  case 3:

    $arr = str_split($moo);

    $r = $arr[0];
    $g = $arr[1];
    $b = $arr[2];

    $hex = sprintf('%s%s%s',
      $r.$r, $g.$g, $b.$b);

    break;

  case 6:
    $hex = $moo;
    break;

  default:
    exit('Invalid color. Exiting.');
    break;
}

$rgb = [
  'r' => (int)hexdec(substr($hex,0,2)),
  'g' => (int)hexdec(substr($hex,2,2)),
  'b' => (int)hexdec(substr($hex,4,2))
];

make_icon_set(2048, $rgb);


## --------------------------------
## functions
## --------------------------------

function resize_icon_from_file($file) {

  check_image_dims($file);

  $fileimg = imagecreatefrompng($file);
  $dir = 'iconsfor-file-' . str_replace('.', '-', $file);

  if(!is_dir($dir)){
      mkdir($dir);
  }

  chdir($dir);
  msg(sprintf('Saving in directory: %s/', $dir));
  complete_resize_cadence($fileimg);
}

function check_image_dims($file) {

  $arr = getimagesize($file);

  $w = $arr[0];
  $h = $arr[1];

  if($w !== $h) {
    msg('Icons must be square. Exiting.');
    exit();
  }
}

function make_icon_set(int $size, array $rgb) {

  $created = imagecreatetruecolor($size, $size);

  $color = imagecolorallocate($created, $rgb['r'], $rgb['g'], $rgb['b']);
  imagefill($created, 0, 0, $color);

  $dir = 'iconsfor-rgb-' . join('-', $rgb);

  if(!is_dir($dir)){
    mkdir($dir);
  }

  chdir($dir);
  msg(sprintf('Saving in directory: %s/', $dir));

  save_file($created, $size);
  complete_resize_cadence($created);
}

function resize_icon($src, $s) {
  $w = imagesx($src);

  if($s > $w) {
    return null;
  }

  $resized = imagecreatetruecolor($s, $s);
  imagecopyresized($resized, $src, 0, 0, 0, 0, $s, $s, $w, $w);
  save_file($resized, $s);
}

function save_file($img, $size) {
  $outfile = sprintf('icon%s.png', $size);
  imagepng($img, $outfile);
  msg(sprintf('Created new icon: %s', $outfile));
}

function complete_resize_cadence($img) {

  resize_icon($img, 1024);
  resize_icon($img, 512);
  resize_icon($img, 256);
  resize_icon($img, 128);
  resize_icon($img, 120);
  resize_icon($img, 96);
  resize_icon($img, 64);
  resize_icon($img, 48);
  resize_icon($img, 38);
  resize_icon($img, 36);
  resize_icon($img, 32);
  resize_icon($img, 19);
  resize_icon($img, 16);

  msg('done.');
}

function process_arguments_files() {

  global $argv;
  global $files;

  $arr = $argv;
  array_shift($arr);
  sort($arr);

  foreach($arr as $a) {
    if( file_exists($a) && !is_dir($a) ) {
      $files[] = $a;
    }
  }
}

// processes command line arguments in $argv
function process_arguments_flags() {

  global $argv;

  if(count($argv) < 2) {
    display_help();
  }

  if(array_search("--version", $argv)) {
    display_version();
  }

  if(array_search("-v", $argv)) {
    display_version();
  }

  if(array_search("--help", $argv)) {
    display_help();
  }

  if(array_search("-h", $argv)) {
    display_help();
  }
}

// displays help info, then exits
function display_help() {

  msg(get_help_text());
  exit();
}


// displays version info, then exits
function display_version() {

  msg(get_version_text());
  exit();
}


function get_version() {
  return '0.5';
}

function get_name() {
  return 'iconset';
}

function get_version_text() {
  return sprintf(<<<MOO

%s v%s - makes a set of icons
The Pffy Authors (C) 2022
Learn more: https://pffy.dev
MOO, get_name(), get_version());
}


function get_help_text() {
  return sprintf(<<<MOO

Try these commands:

`$ iconset icon.png`
`$ iconset rgb-255-148-22`
`$ iconset ff6633`
`$ iconset f63`


MOO);
}


// prints variable types as strings, whenever possible
function msg($var = "", $showType = false) {

  if(is_object($var)) {
    $label = $showType ? '[object]: ' : '';
    echo sprintf($label . PHP_EOL);
    var_dump($var);
  } else if(is_array($var)) {
    $label = $showType ? '[array]: ' : '';
    echo sprintf($label . PHP_EOL);
    print_r($var);
  } else {
    if(is_bool($var)) {
      $label = $showType ? '[boolean]: ' : '';
      echo sprintf($label . '%s ', $var);
    } else if(is_integer($var)) {
      $label = $showType ? '[integer]: ' : '';
      echo sprintf($label . '%s ', $var);
    } else if(is_string($var)) {
      $label = $showType ? '[string]: ' : '';
      echo sprintf($label . '%s ', $var);
    } else {
      // whatever
      echo $var;
    }
  }

  echo PHP_EOL;
}
