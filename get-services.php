<?php
require_once 'config.php';
require_once 'classes/Service.php';
require_once 'classes/Response.php';

//Create an empty object for the return response
$response = new Response();

//Handle all errors so that the script fails gracefully
try {
    //Get the sanitised country code from the HTTP request
    $countryCode = $_GET['ccode'];
    if (!$countryCode) {
        throw new Exception("Country code not supplied.");
    }

    //Build query to retrieve records based on country code
    $pdoConnection = new PDO($config['dsn'], $config['user'], $config['password']);
    $sql = 'select ref, centre, service, country 
            from services 
            where country = :country
            order by ref';
    $params = ['country' => $countryCode];
    $statement = $pdoConnection->prepare($sql);
    $statement->execute($params);
    $results = $statement->fetchAll();

    //Create array of Service objects
    $matchingServices = [];
    foreach($results as $result) {
        $matchingServices[] = new Service($result);
    }

    //Populate the response object with the results
    $response->success = true;
    $response->message = count($matchingServices) . " services found.";
    $response->services = $matchingServices;

} catch (PDOException $e) {
    //Errors relating to the database
    $response->success = false;
    $response->message = "PDOException: " . $e->getMessage();
} catch (Throwable $e) {
    //Any other error
    $response->success = false;
    $response->message = "Error: " . $e->getMessage();
}

//Format the results into a JSON string
echo json_encode($response);


