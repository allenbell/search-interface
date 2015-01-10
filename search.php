<?php

//connect to db
require "/export/home/b/bellma/private/dbconnect-pdo.php";

//include html head and opening tags for table
include ('header.php');

//initialize variables
$title = '';
$keyword = '';
$fulltext = '';
$fromyear = '';
$toyear = '';
$fromseconds = '';
$toseconds = '';
$withsound = '';
$withcolor = '';
$fieldscheck = '';


//filter user input
if (get_magic_quotes_gpc()) {
   $title = htmlentities(strip_tags(stripslashes($_POST['titleSearch'])));
   $keyword = htmlentities(strip_tags(stripslashes($_POST['keywordSearch'])));
   $fulltext = htmlentities(strip_tags(stripslashes($_POST['fullSearch'])));
   $fromyear = htmlentities(strip_tags(stripslashes($_POST['fromYear'])));
   $toyear = htmlentities(strip_tags(stripslashes($_POST['toYear'])));
   $fromseconds = htmlentities(strip_tags(stripslashes($_POST['fromSeconds'])));
   $toseconds = htmlentities(strip_tags(stripslashes($_POST['toSeconds'])));
   $withsound = htmlentities(strip_tags(stripslashes($_POST['withSound'])));
   $withcolor = htmlentities(strip_tags(stripslashes($_POST['withColor'])));
} else {
   $title = htmlentities(strip_tags($_POST['titleSearch']));
   $keyword = htmlentities(strip_tags($_POST['keywordSearch']));
   $fulltext = htmlentities(strip_tags($_POST['fullSearch']));
   $fromyear = htmlentities(strip_tags($_POST['fromYear']));
   $toyear = htmlentities(strip_tags($_POST['toYear']));
   $fromseconds = htmlentities(stripslashes($_POST['fromSeconds']));
   $toseconds = htmlentities(strip_tags($_POST['toSeconds']));
   $withsound = htmlentities(strip_tags($_POST['withSound']));
   $withcolor = htmlentities(strip_tags($_POST['withColor']));
}
//if form is submitted, assign $_POST values to variables
if (isset($_POST['submit'])) {
   $title = $_POST['titleSearch'];
   $keyword = $_POST['keywordSearch'];
   $fulltext = $_POST['fullSearch'];
   $fromyear = $_POST['fromYear'];
   $toyear = $_POST['toYear'];
   $fromseconds = $_POST['fromSeconds'];
   $toseconds = $_POST['toSeconds'];
   $withsound = $_POST['withSound'];
   $withcolor = $_POST['withColor'];
}

//create default query (returns all rows if user doesn't specify anything)
$query = "SELECT keyframeurl, keywords, videoid, title, creationyear, sound, color, 
            durationsec, genre FROM openvideo";

//create array of fields to check to see if user put anything in any of them
$fields = array('titleSearch', 'keywordSearch', 'fullSearch', 'fromYear', 'toYear',
    'fromSeconds', 'toSeconds', 'withSound', 'withColor');

//check to see if any fields have user input
foreach ($fields as $field) {
   if (isset($_POST[$field]) && $_POST[$field] != '') {
      // create a new condition, using a prepared statement
      $fieldscheck[] = '';
   }
}

// if the user entered something into at least one field...
if (count($fieldscheck) > 0) {
   // append WHERE to the query...
   $query .= " WHERE ";
   //or else display an error message
} else {
   echo 'Please enter at least one search term.';
}

//create array to know whether any if statements have generated a condition yet
$conditions = array();

//display the query back to the user in plain English - starts here and has conditions 
//appended to it over the course of the if statements below
echo "You searched for ";

/* I'm including a lot of comments in this first if statement. I won't repeat comments in
 * future if statements where the logic is the same as in this first code block. */

//begin if statements to append conditions to the WHERE clause
//search by title
if ((isset($_POST['titleSearch'])) && $_POST['titleSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {

//if there's already a condition, start this condition with AND...
   $query .= " AND title LIKE '%$title%'";

//print this in plain English
   echo "AND a title containing '$title' ";

   /* and add an element to the $conditions array so later if statements will know to 
    * append an "AND" at the start of their condition... */
   array_push($conditions, 'blah');
} elseif ((isset($_POST['titleSearch'])) && $_POST['titleSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {

//but if there's not already a condition, don't add AND
   $query .= " title LIKE '%$title%' ";

//print this in plain English   
   echo "AND a title containing '$title'";

   /* and add an element to the $conditions array so later if statements will know to 
    * append an "AND" at the start of their condition */
   array_push($conditions, 'blah');
} elseif ((isset($_POST['titleSearch'])) && $_POST['titleSearch'] != '' &&
        (count($fieldscheck) > 0)) {

//but if there's not already a condition, don't add AND
   $query .= " title LIKE '%$title%'";

//print this in plain English   
   echo "a title containing '$title' ";

   /* and add an element to the $conditions array so later if statements will know to 
    * append an "AND" at the start of their condition */
   array_push($conditions, 'blah');
}

//search by keyword - using the same logic as 'search by title' - see comments above
if ((isset($_POST['keywordSearch'])) && $_POST['keywordSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND keywords LIKE '%$keyword%'";
   echo "AND the keyword '$keyword' ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['keywordSearch'])) && $_POST['keywordSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " keywords LIKE '%$keyword%'";
   echo "AND the keyword '$keyword' ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['keywordSearch'])) && $_POST['keywordSearch'] != '' && (count($fieldscheck) > 0)) {
   $query .= " keywords LIKE '%$keyword%'";
   echo "the keyword '$keyword' ";
   array_push($conditions, 'blah');
}

//search by keyword, title, and description - using the same logic as 'search by title' -
//see comments above. The only difference here: using MATCH...AGAINST a FULLTEXT index
if ((isset($_POST['fullSearch'])) && $_POST['fullSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND MATCH (title, description, keywords) AGAINST ('$fulltext')";
   echo "AND the word(s) '$fulltext' anywhere ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fullSearch'])) && $_POST['fullSearch'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " MATCH (title, description, keywords) AGAINST ('$fulltext')";
   echo "AND the word(s) '$fulltext' anywhere ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fullSearch'])) && $_POST['fullSearch'] != '' && (count($fieldscheck) > 0)) {
   $query .= " MATCH (title, description, keywords) AGAINST ('$fulltext')";
   echo "the word(s) '$fulltext' anywhere ";
   array_push($conditions, 'blah');
}

//search for films AFTER year yyyy - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['fromYear'])) && $_POST['fromYear'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND creationyear > $fromyear";
   echo "AND films produced after $fromyear ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fromYear'])) && $_POST['fromYear'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " creationyear > $fromyear";
   echo "AND films produced after $fromyear ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fromYear'])) && $_POST['fromYear'] != '' && (count($fieldscheck) > 0)) {
   $query .= " creationyear > $fromyear";
   echo "films produced after $fromyear ";
   array_push($conditions, 'blah');
}

//search for films BEFORE year yyyy - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['toYear'])) && $_POST['toYear'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND creationyear < $toyear";
   echo "AND films produced before $toyear ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['toYear'])) && $_POST['toYear'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " creationyear < $toyear";
   echo "AND films produced before $toyear ";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['toYear'])) && $_POST['toYear'] != '' && (count($fieldscheck) > 0)) {
   $query .= " creationyear < $toyear";
   echo "films produced before $toyear ";
   array_push($conditions, 'blah');
}

//search for films LONGER than x seconds - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['fromSeconds'])) && $_POST['fromSeconds'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND durationsec > $fromseconds";
   echo "AND films longer than $fromseconds seconds";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fromSeconds'])) && $_POST['fromSeconds'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " durationsec > $fromseconds";
   echo "AND films longer than $fromseconds seconds";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['fromSeconds'])) && $_POST['fromSeconds'] != '' && (count($fieldscheck) > 0)) {
   $query .= " durationsec > $fromseconds";
   echo "films longer than $fromseconds seconds";
   array_push($conditions, 'blah');
}

//search for films SHORTER than x seconds - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['toSeconds'])) && $_POST['toSeconds'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND durationsec < $toseconds";
   echo "AND films shorter than $toseconds seconds";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['toSeconds'])) && $_POST['toSeconds'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " durationsec < $toseconds";
   echo "AND films shorter than $toseconds seconds";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['toSeconds'])) && $_POST['toSeconds'] != '' && (count($fieldscheck) > 0)) {
   $query .= " durationsec < $toseconds";
   echo "films shorter than $toseconds seconds";
   array_push($conditions, 'blah');
}

//search for films with sound - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['withSound'])) && $_POST['withSound'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND sound = '$withsound'";
   echo "AND films with sound";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['withSound'])) && $_POST['withSound'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " sound = '$withsound'";
   echo "AND films with sound";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['withSound'])) && $_POST['withSound'] != '' && (count($fieldscheck) > 0)) {
   $query .= " sound = '$withsound'";
   echo "films with sound";
   array_push($conditions, 'blah');
}

//search for films with color - using the same logic as 'search by title' - 
//see comments above
if ((isset($_POST['withColor'])) && $_POST['withColor'] != '' && (count($fieldscheck) > 1) && (count($conditions) > 0)) {
   $query .= " AND color = '$withcolor'";
   echo "AND films with color";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['withColor'])) && $_POST['withColor'] != '' && (count($fieldscheck) > 1) && (count($conditions) == 0)) {
   $query .= " color = '$withcolor'";
   echo "AND films with color";
   array_push($conditions, 'blah');
} elseif ((isset($_POST['withColor'])) && $_POST['withColor'] != '' && (count($fieldscheck) > 0)) {
   $query .= " color = '$withcolor'";
   echo "films with color";
   array_push($conditions, 'blah');
}

//order by video id as the default
$query .= " ORDER BY videoid";

//make a little space between the plain English query and the results table
echo "<br />";


/*
 * generate table for printing search results************************************
 */

//whitelist the values allowed for $_GET query
$sortby = array('videoid', 'title', 'year', 'sound', 'color', 'duration', 'genre');

//default: order by videoid
$order = 'videoid';

//only $_GET a value if it's included in the $sortby array
if (isset($_GET['sortby']) && in_array($_GET['sortby'], $sortby)) {
   $order = $_GET['sortby'];
}

//link table headers to above sorting query 
echo "<tr>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=videoid\">Video ID</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=title\">Title</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=year\">Year</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=sound\">Sound</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=color\">Color</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=duration\">Duration</a></th>";
echo "<th><a href=\"{$_SERVER['PHP_SELF']}?sortby=genre\">Genre</a></th>";

echo "</tr>";
echo "</thead>";
echo "<tbody>";

//print query results
foreach ($dbh->query($query) as $row) {
   echo "<tr>";
   echo "<td><a href=\"" . $row['keyframeurl'] . "\">" . $row['videoid'] . "</a></td>";
   echo "<td>" . $row['title'] . "</td>";
   echo "<td>" . $row['creationyear'] . "</td>";
   echo "<td>" . $row['sound'] . "</td>";
   echo "<td>" . $row['color'] . "</td>";
   echo "<td>" . $row['durationsec'] . "</td>";
   echo "<td>" . $row['genre'] . "</td>";
   echo "</tr>";
}

//close table elements
echo "</tbody>";
echo "</table>";

//link to go back to the search form
echo "<a href='http://ils.unc.edu/~bellma/p4/search.html'>Revise your search</a>";
?>

