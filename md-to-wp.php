<?php

/**
 * md-to-wp
 * imports markdown blog posts to wordpress
 *
 * @author jenn schiffer
 * @url http://github.com/jennschiffer/md-to-wp
 */

// require these files to use wordpress things
define('WP_USE_THEMES', false);
require_once (dirname(dirname(__FILE__)) . '/wp-blog-header.php');
require_once (dirname(dirname(__FILE__)) . '/wp-includes/registration.php');

// in which directory are the markdown posts
$mdDir = '/posts-to-import';

// user import email domain (will do their first name @ $email)
$email = 'bocoup.com';

// script page header
echo '<h1>~*markdown to wordpress*~</h1>';
echo '<h2>good luck! xoxo jenn</h2>';

// require markdown extra if it hasn't been activated in a plugin like wp-markdown already
if ( !class_exists('Markdown_Parser')) {
  require('markdown-extra.php');
}

// get all files
$dir = new DirectoryIterator(dirname(__FILE__) . $mdDir);

// for each .md or .markdown file, import into db
foreach ($dir as $fileInfo) {
  if (!$fileInfo->isDot() && $fileInfo->isReadable() && (strtolower($fileInfo->getExtension()) === 'md' || strtolower($fileInfo->getExtension()) === 'markdown')) {
    importBlogPost($fileInfo->getPathname());
  }
}

/*
* parses date line
*
* @param string $date which is in the format `date: 'YYYY-MM-DD HH:MM:SS'`
* @return string
*/
function parseDate($date) {
  // remove the open and closing single quotes and label
  $quotedDate = strstr($date, "'");
  $newDate = substr($quotedDate, 1, strlen($quotedDate)-2);
  return $newDate;
}

/*
* parses title line
*
* @param string $title which is in the format `title: 'some title here'`
* @return string
*/
function parseTitle($title) {
  // remove the label
  $newTitle = trim(substr($title, 6, strlen($title)-6));

  // remove any quotes
  if ( substr($newTitle, 0, 1) === "'" ) {
    $quotedTitle = strstr($newTitle, "'");
    $newTitle = substr($quotedTitle, 1, strlen($quotedTitle)-2);
  }
  else if ( substr($newTitle, 0, 1) === '"' ) {
      $quotedTitle = strstr($newTitle, '"');
      $newTitle = substr($quotedTitle, 1, strlen($quotedTitle)-2);
    }

  return $newTitle;
}

/*
* parses author line, checks if author exists and returns user id
*
* @param string $author which is in the format `timothy branyen`
* @return integer
*/
function parseAuthor($author) {
  global $email;

  // remove hyphens and quotes
  $author = str_replace('-', ' ', $author);
  $author = str_replace("'", '', $author);
  $author = str_replace('"', '', $author);

  // if there are dupe authors, create error author for fixing later
  if ( strstr($author, ',') ){
    $author = 'MULTIPLE AUTHORS';
    echo '[MULTIPLE AUTHORS] ';
  }

  $existingUser = get_user_by('login', $author);

  // get existing user id or create new user and get its id
  if ( $existingUser ) {
    $authorId = $existingUser->ID;
  }
  else {
    $nameParts = explode(' ', $author);

    // make sure no dupe emails are attempted, add last names to them if a dupe
    $existingEmail = get_user_by('email', $nameParts[0] . '@' . $email);
    if ( $existingEmail ) {
      $userEmail = $nameParts[0] . $nameParts[1] . '@' . $email;
    }
    else {
      $userEmail = $nameParts[0] . '@' . $email;
    }

    $authorId = wp_create_user($author, 'password', $userEmail);
  }

  // return user id
  return $authorId;
}

/*
* parses category/tag values line
*
* @param array $linesArray
* @param integer $index
* @return array
*/
function parseTaxonomy($linesArray, $index) {
  // config taxonomy type for import to wordpress
  $typeTaxonomy = 'tags';
  $arrayTaxonomies = [];

  // check if current index starts with 'category' to be sure
  $thisLine = explode(':', $linesArray[$index]);

  if ($thisLine[0] === 'category') {
    $isTaxonomyList = true;

    // go through next lines to check if they start with - and if so strip whitespace and push category to array
    while ($isTaxonomyList) {
      $index++;

      if (substr(trim($linesArray[$index]),0,2) === '- ') {
        array_push($arrayTaxonomies, substr(trim($linesArray[$index]),2, strlen(trim($linesArray[$index])) - 2 ));
      }
      else {
        $isTaxonomyList = false;
      }
    }

    return $arrayTaxonomies;
  }
}

/*
* converts markdown post to wordpress ready blog and inserts post
*
* @param file $file
*/
function importBlogPost($file) {
  $content = file_get_contents($file);
  $lines = explode("\n", $content);

  $bodyArray = [];
  $isBody = false;
  $metaStarted = false;

  // parse each line for meta and then body
  for ($i = 0; $i <= sizeof($lines); $i++) {
    $thisLine = $lines[$i];

    if ($isBody === false) {
      // if line starts with --, meta has begun or ended
      if (substr($thisLine, 0, 2) === '--') {
        if ($metaStarted) {
          $isBody = true;
        }
        else {
          $metaStarted = true;
        }
      }
      else {
        $meta = explode(':', $thisLine);

        switch ($meta[0]) {
        case 'author':
          $author = parseAuthor(trim(strtolower($meta[1])));
          break;
        case 'date':
          $date = parseDate($thisLine);
          break;
        case 'slug':
          $slug = $meta[1];
          break;
        case 'title':
          $title = parseTitle($thisLine);
          break;
        case 'status':
          $status = $meta[1];
          break;
        case 'category':
          $category = parseTaxonomy($lines, $i);
          break;
        default:
          break;
        }
      }
    }
    else {
      array_push($bodyArray, $thisLine);
    }
  }

  $body_md = implode("\n", $bodyArray);
  $body_html = Markdown($body_md);

  $postToImport = array(
    'post_content'          => $body_html,
    'post_name'             => $slug,
    'post_title'            => $title,
    'post_status'           => $status,
    'post_author'           => $author,
    'post_content_filtered' => $body_md,
    'post_date'             => $date,
    'tags_input'            => $category,
  );

  // insert the post into the database!
  $imported = wp_insert_post($postToImport);

  if ( $imported === 0 ) {
    echo '<b style="color:red;">IMPORT FAILED: ' . $postToImport[post_title] . '</b><br />';
  }
  else {
    echo 'Successful Import: ' . $postToImport[post_title] . '<br />';
  }

}