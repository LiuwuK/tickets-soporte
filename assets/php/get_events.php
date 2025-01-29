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
    $query = "SELECT id, nombre, fecha_cierre_documental
                FROM proyectos";
    if($area != 4){
        $query .= " WHERE comercial_responsable = '".$userId."'";
    };

    $cierre = $con->query($query);
    while ($row = $cierre->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'title' => 'Cierre del proyecto #'.$row['id'].' '.$row['nombre'],
            'start' => $row['fecha_cierre_documental'] 
        ];
    };

    //obtener eventos de google
    if(isset($_SESSION['refresh_token'])){
        $client = new Google_Client();
        $client->setAccessToken($_SESSION['access_token']);
        $client->setAuthConfig('../js/json/credentials.json');
        if (isset($_SESSION['access_token']) && $client->isAccessTokenExpired()) {
            if (isset($_SESSION['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
                $token = $client->getAccessToken(); 
                $_SESSION['access_token'] = $token['access_token'];

                $query = "UPDATE user
                            SET access_token = ?
                            WHERE id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("si", $token['access_token'], $userId);
                $stmt->execute();

                $client->setAccessToken($_SESSION['access_token']);
                            
            } else {
                header('Location: oauth-init.php');
                exit;
            }
        }

        
        try {
            $service = new Google_Service_Calendar($client);


            $calendarId = 'primary';
            $events = $service->events->listEvents($calendarId, [
                //'timeMin' => date('c'), 
                'maxResults' => 200,  
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);

            // Depuración: Verificar si se obtienen eventos
            if (empty($events->getItems())) {
                echo json_encode($eventos);
                exit;
            }

            foreach ($events->getItems() as $event) {
                $eventos[] = [
                    'title' => $event->getSummary(),
                    'start' => $event->getStart()->getDateTime() ?: $event->getStart()->getDate(),
                    'end' => $event->getEnd()->getDateTime() ?: $event->getEnd()->getDate(),
                ];
            }

        
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al obtener eventos: ' . $e->getMessage()]);
        }
    }
    echo json_encode($eventos);
?>