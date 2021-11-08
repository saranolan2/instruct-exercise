<?php
require_once 'config.php';
require_once 'classes/Service.php';
require_once 'classes/Response.php';

//Create an empty object for the return response
$response = new Response();

//Handle all errors so that the script fails gracefully
try {
    //Get service details from HTTP post request
    $postedService = new Service([$_POST['ref'], $_POST['centre'], $_POST['service'], $_POST['country']]);

    //Insert into database if it's a new service, update if it already exists
    $sql = 'replace into services (ref, centre, service, country) 
                values (ref, centre, service, country)';
    $params = [
        'ref' => $postedService->ref,
        'centre' => $postedService->centre,
        'service' => $postedService->service,
        'country' => $postedService->country
    ];
    $statement = $pdoConnection->prepare($sql);
    $result = $statement->execute($params);

    //Return result based on the outcome (any catchable errors should have been caught in the try/catch)
    if ($result !== FALSE) {
        //Success
        $response->success = true;
        $response->message = $statement->rowCount() > 0 ? 'New service inserted' : 'Service updated';
        $response->services[] = $postedService;
    } else {
        //Failure
        $response->success = false;
        $response->message = 'Update failed';
        $response->services[] = $postedService;
    }
       
} catch (PDOException $e) {
    //Errors relating to the database
    $response->success = false;
    $response->message = "PDOException: " . $e->getMessage();
} catch (Throwable $e) {
    //Any other error
    $response->success = false;
    $response->message = "Error: " . $e->getMessage();
}

//Output the response as JSON
echo json_encode($response);
