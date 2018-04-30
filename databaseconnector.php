<?php

$servername = "localhost";
$username = "root";
$password = "hodorhodor";
$dbname = "levykauppa";
$conn = null;

createConnection();

//initializeDataBase();
//addArtist("Stormic", null,"www.suomi24.fi");
//addArtist("IA");
//addAlbum('Stormic', 'Justice For All', '2007');
//addAlbum('Stormic', 'Justice For All 2', '2009');
//addAlbum('IA', 'Justice For All', '2009');
//getAlbumId('IA', "Justice For All");
//addTrack("Stormic", 'Justice For All', "ykkonen", 90);
//addTrack("Stormic", "Justice For All", "dfs", 90);
//addTrack("Stormic", "Justice For All", "sdfsdg", 90);
//addTrack("Stormic", "Justice For All", "aasdfsdf", 90);
//addTrack("Stormic", "Justice For All", "dfhdf", 90);
//addTrack("Stormic", "Justice For All", "sdfs", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//addTrack("Stormic", "Justice For All", "ssss", 90);
//getAlbumsOfArtist("Stormic");
//getTracksOfAlbum('Stormic', 'Justice For All');
//getArtists();
/*
 * Creates the connection to the database.
 */
function createConnection() {
    global $servername, $username, $password, $dbname, $conn;
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

/*
 * Creates the empty database.
 */
function initializeDataBase() {
    createConnection();

    global $conn;

    // INITIALIZE DATABASE AND TABLES

    // Drop existing database
    $sql = "DROP DATABASE IF EXISTS levykauppa;";
    if ($conn->query($sql) === TRUE) {
        echo "Database deleted successfully";
    } else {
        echo "Database not deleted: ";
    }
    echo('<br>');

    // Create database
    $sql = "CREATE DATABASE levykauppa;";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }
    echo('<br>');

    // Select database
    $sql = "USE levykauppa;";
    if ($conn->query($sql) === TRUE) {
        echo "Database selected successfully";
    } else {
        echo "Error selecting database: " . $conn->error;
    }
    echo('<br>');

    // SQL TABLE CREATION:
    $sql = array();

    $sql['artist'] = "CREATE TABLE artist
    (
      id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(75) NOT NULL,    
      link_to_logo VARCHAR(200),
      link_to_homepage VARCHAR(200),
      PRIMARY KEY (id)
    );";

    $sql['album'] = "CREATE TABLE album
    (
      id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(75) NOT NULL,
      release_year INT NOT NULL,
      link_to_cover_image VARCHAR(200),
      artist_id INT NOT NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (artist_id) REFERENCES artist(id)
    );";

    $sql['track'] = "CREATE TABLE track
    (
      id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(75) NOT NULL,
      link_to_original_mp3 VARCHAR(75),
      link_to_stripped_mp3 VARCHAR(75),
      length VARCHAR(8) NOT NULL,
      album_id INT NOT NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (album_id) REFERENCES album(id)
    );";

    foreach ($sql as $sqlquery => $id) {
        echo('<br>');
        if ($conn->query($id) === TRUE) {
            echo "Table '". $sqlquery . "' created successfully. ";
        } else {
            echo "Error creating table: " . $conn->error;
        }
        echo('<br>');
        echo($id);

    }
    $conn->close();
}


function addArtist($name, $link_to_logo=null, $link_to_homepage=null) {
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("INSERT INTO artist (name, link_to_logo, link_to_homepage) VALUES (?, ?, ?)");
    if ($stmt->bind_param("sss", $name, $link_to_logo, $link_to_homepage)) {
        echo('<br>');
        echo("Added artist '" . $name . "' to the database.");
    } else {
        echo('<br>');
        echo("Error: artist '" . $name . "' wasn't added to the database.");
    }

    // execute
    $stmt->execute();
    $stmt->close();
    $conn->close();


}

function getArtistId($name) {
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("SELECT * FROM artist WHERE name = ?");
    $stmt->bind_param("s", $name);

    // execute
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) exit('No rows');
    while($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
    $stmt->close();

    $conn->close();

    echo('<br>');
    echo("Searched for artist '" . $name . "' id from the database. ");
    echo("It was ". $ids[0]);
    echo('<br>');
    return $ids[0];
}

function addAlbum($artist_name, $album_name, $release_year=null, $link_to_cover_image=null) {
    $artist_id = getArtistId($artist_name);
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("INSERT INTO album (name, release_year, link_to_cover_image, artist_id) VALUES (?, ?, ?, ?)");
    if ($stmt->bind_param("sisi", $album_name, $release_year, $link_to_cover_image, $artist_id)) {
        echo('<br>');
        echo("Added album '" . $album_name . "' to the database.");
    } else {
        echo('<br>');
        echo("Error: album '" . $album_name . "' wasn't added to the database.");
    }

    // execute
    $stmt->execute();
    $stmt->close();
    $conn->close();

}

function getAlbumId($artist_name, $album_name) {
    $artist_id = getArtistId($artist_name);
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("SELECT album.id
                            FROM album
                            WHERE album.name = ?
                            AND album.artist_id = ?;");
    $stmt->bind_param("si", $album_name, $artist_id);

    // execute
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) exit('No rows');
    while($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
    $stmt->close();

    $conn->close();

    echo('<br>');
    echo("Searched for album '" . $album_name . "' of the artist '" . $artist_name . "' id from the database. ");
    echo("It was ". $ids[0]);
    echo('<br>');
    return $ids[0];
}

function addTrack($artist_name, $album_name, $track_name, $length, $link_to_original_mp3=null, $link_to_stripped_mp3=null) {
    $album_id = getAlbumId($artist_name, $album_name);
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("INSERT INTO track (name, link_to_original_mp3, link_to_stripped_mp3, length, album_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->bind_param("ssssi", $track_name, $link_to_original_mp3, $link_to_stripped_mp3, $length, $album_id)) {
        echo('<br>');
        echo("Added track '" . $track_name . "' of the album '" . $album_name . " to the database.");
    } else {
        echo('<br>');
        echo("Error: album '" . $track_name . "' of the album '" . $album_name . "' wasn't added to the database.");
    }

    // execute
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function addAllTracks($artist_name, $album_name, $tracks) {
    foreach ($tracks as $track) {
        addTrack($artist_name,
            $album_name,
            $tracks['name'],
            $track['length'],
            $track['link_to_original_mp3'],
            $track['link_to_stripped_mp3']);
    }
}

function getTracksOfAlbum($artist_name, $album_name) {
    $album_id = getAlbumId($artist_name, $album_name);
    $tracks = array();
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("SELECT name, link_to_stripped_mp3, length
                            FROM track
                            WHERE track.album_id = ?;");
    $stmt->bind_param("i", $album_id);

    // execute
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) exit('No rows');
    while($row = $result->fetch_assoc()) {
        array_push($tracks, $row);
//        $tracks[$row['id']]['name'] = $row['name'];
//        $tracks[$row['id']]['link_to_stripped_mp3'] = $row['link_to_stripped_mp3'];
//        $tracks[$row['id']]['length'] = $row['length'];
    }
    $stmt->close();

    $conn->close();

    echo('<br>');
    echo("Fetched the album '" . $album_name . "' of the artist '" . $artist_name . "' tracks from the database. ");
    echo("They were ");
    echo json_encode($tracks);
    echo('<br>');
    return ($tracks);

}

function getAlbumsOfArtist($artist_name) {
    $artist_id = getArtistId($artist_name);
    $albums = array();
    createConnection();
    global $conn;

    // prepare and bind
    $stmt = $conn->prepare("SELECT name, release_year, link_to_cover_image
                            FROM album
                            WHERE album.artist_id = ?;");
    $stmt->bind_param("i", $artist_id);

    // execute
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows === 0) exit('No rows');
    while($row = $result->fetch_assoc()) {
        array_push($albums, $row);
//        $albums[$row['id']]['name'] = $row['name'];
//        $albums[$row['id']]['release_year'] = $row['release_year'];
//        $albums[$row['id']]['link_to_cover_image'] = $row['link_to_cover_image'];
    }
    $stmt->close();

    $conn->close();

    echo('<br>');
    echo("Fetched album names of the artist '" . $artist_name . "' from the database. ");
    echo("They were ");
    echo json_encode($albums);
    echo('<br>');
    return $albums;
}

function getArtists() {
    $artists = array();
    createConnection();
    global $conn;

    // prepare and bind
    $result = $conn->query("SELECT name, link_to_logo, link_to_homepage FROM artist");

    while($row = $result->fetch_assoc()) {
        array_push($artists, $row);
    }

    $result->close();
    $conn->close();

    // echo('<br>');
    // echo("Fetched artists from the database. ");
    //echo("They were ");

    //echo('<br>');
    // return $artists;

    echo json_encode($artists);
}
?>