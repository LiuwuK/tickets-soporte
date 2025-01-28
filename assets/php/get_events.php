<?php
    header('Content-Type: application/json');
    require_once '../../vendor/autoload.php';
    session_start();
    
    include("../../dbconnection.php");
    
    //obtener actividades
    $area = $_SESSION['cargo'];
    $query = "SELECT *
                FROM actividades
                WHERE area = '$area' ";
    $result = $con->query($query);
    $eventos = [];
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'title' => $row['nombre'],
            'start' => $row['fecha_inicio'],
            'end' => $row['fecha_termino']    
        ];
    };
    
    //obtener fecha de cierre de proyectos
    $userId = $_SESSION['id'];
    $query = "SELECT id, nombre, fecha_cierre
                FROM proyectos";
    if($area != 4){
        $query .= " WHERE comercial_responsable = '".$userId."'";
    };

    $cierre = $con->query($query);
    while ($row = $cierre->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'title' => 'Cierre del proyecto #'.$row['id'].' '.$row['nombre'],
            'start' => $row['fecha_cierre'] 
        ];
    };

    //obtener eventos de google
    $client = new Google_Client();
    $client->setAccessToken($_SESSION['access_token']);

    if (isset($_SESSION['access_token']) && $client->isAccessTokenExpired()) {
        if (isset($_SESSION['refresh_token'])) {
            $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
            $_SESSION['access_token'] = $client->getAccessToken(); 
        } else {
            header('Location: oauth-init.php');
            exit;
        }
    }

    
    try {
        $service = new Google_Service_Calendar($client);


        $calendarId = 'primary';
        $events = $service->events->listEvents($calendarId, [
            'timeMin' => date('c'), 
            'maxResults' => 200,  
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ]);

        // Depuración: Verificar si se obtienen eventos
        if (empty($events->getItems())) {
            echo json_encode(['error' => 'No se encontraron eventos.']);
            exit;
        }

        $result = [];
        foreach ($events->getItems() as $event) {
            $result[] = [
                'title' => $event->getSummary(),
                'start' => $event->getStart()->getDateTime() ?: $event->getStart()->getDate(),
                'end' => $event->getEnd()->getDateTime() ?: $event->getEnd()->getDate(),
            ];
        }

        echo json_encode($result);

    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener eventos: ' . $e->getMessage()]);
    }

    //echo json_encode($eventos);
?>