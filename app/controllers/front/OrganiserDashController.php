<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\View;
use App\services\EventService;
use App\services\StatService;
use App\services\CreateService;


class OrganiserDashController extends Controller
{

    protected EventService $evsdn;
    protected StatService $statServ;
    protected CreateService $tagCatego;
    public int $id;

    public function __construct()
    {
        parent::__construct();
        $this->evsdn = new EventService();
        $this->statServ = new StatService();
        $this->tagCatego = new CreateService();
        $this->id = $this->session->get('user')->id;
    }


    public function index(): void
    {
        $this->ind($this->id);
    }


    public function ind($id)
    {

        $intValeur = (int) $id;

        $data = $this->statServ->getOwnerStatistic($intValeur);

        $csrfToken = $this->security->generateCsrfToken();


        echo $this->render('front/organiser/dashboard.twig', ['data' => $data, 'csrf_token' => $csrfToken]);
    }







    public function getEventTickets($eventIdzed)
    {
        $id = $eventIdzed[0];

        $this->getEv($id);
    }



    public function getEv($eventId)
    {
        if ($eventId === 'all') {
            // Redirect to the tickets page when 'all' is selected
            $intValeur =  (int)  $this->id;
            $event = $this->statServ->ticketsStaTs($intValeur);
            header("HTTP/1.1 200 OK");
            echo json_encode($event);
        } else {
            // Prepare and execute the query to get event details for a specific user and event
            $userId = $this->id;  // Assume the user ID is stored in the session

            $idR = (int) $eventId;
            $event = $this->statServ->getET(1, $idR);
            header("HTTP/1.1 200 OK");
            echo json_encode($event);
        }
    }



    public function tickets()
    {
        $this->tic($this->id);
    }

    public function tic($id)
    {

        $intValeur = (int) $id;

        $data = $this->statServ->ticketsStaTs($intValeur);

        // var_dump($data);

        echo $this->render('front/organiser/tickets.html.twig', ['data' => $data]);
    }

    public function delete($id)
    {
        $intValeur = (int) $id[0];

        $this->statServ->deleteE($intValeur);




        header("Location: /Organiser/dash");
    }















    public function createEve()
    {



        $tG = $this->tagCatego->getAllTagAndCatego();



        echo $this->render('front/organiser/createEvent.html.twig', ['data' => $tG]);
    }
    public function crealhjte()
    {

        $videoPath = null; // Ensure $videoPath is always set

        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $video = $_FILES['video'];

            // Absolute path to the upload directory
            $uploadDir = 'uploads/videos/';

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Sanitize the file name
            $fileName = uniqid() . "_" . basename($video['name']);
            $videoPath = $uploadDir . $fileName;

            // Validate file type (only allow videos)
            $allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/mov'];
            if (in_array($video['type'], $allowedTypes)) {
                // Check file size (max 50MB)
                if ($video['size'] <= 50 * 1024 * 1024) {
                    // Move the uploaded file to the desired location
                    if (move_uploaded_file($video['tmp_name'], $videoPath)) {
                        echo "Video uploaded successfully.<br>";
                        echo "Video Path: " . htmlspecialchars($videoPath) . "<br>";
                    } else {
                        echo "Failed to move the uploaded video.";
                        $videoPath = null; // Reset to null if upload fails
                    }
                } else {
                    echo "File size exceeds the 50MB limit.";
                    $videoPath = null;
                }
            } else {
                echo "Invalid file type. Only MP4, AVI, and MOV videos are allowed.";
                $videoPath = null;
            }
        } else {
            echo "Error with the uploaded video.";
            $videoPath = null;
        }

        // Ensure htmlspecialchars() doesn't receive null
        echo "Discount: " . htmlspecialchars($videoPath ?? "No video uploaded") . "<br>";
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Retrieve form data
            $videoPath = null;
            $title = $_POST['title'] ?? null;
            $description = $_POST['description'] ?? null;
            $type = $_POST['type'] ?? null;
            $date = $_POST['date'] ?? null;
            $location = $_POST['location'] ?? null;
            $total_num = $_POST['total_num'] ?? null;
            $discount = $_POST['discount'] ?? null;
            $vip_num = $_POST['vip_num'] ?? null;
            $vip_price = $_POST['vip_price'] ?? null;
            $regular_num = $_POST['Regular_num'] ?? null;
            $regular_price = $_POST['Regular_price'] ?? null;
            $student_num = $_POST['student_num'] ?? null;
            $student_price = $_POST['student_price'] ?? null;
            $categorie = $_POST['categorie'] ?? null;
            $tags = $_POST['tags'] ?? null;

            // Image upload handling
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];

                // Absolute path to the upload directory
                $uploadDir = 'uploads/';

                // Ensure the upload directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);  // Create directory if not exists
                }

                // Sanitize the file name
                $fileName = uniqid() . "_" . basename($image['name']);
                $filePath = $uploadDir . $fileName;

                // Validate file type (only allow images)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($image['type'], $allowedTypes)) {
                    // Check for valid image dimensions (optional)
                    list($width, $height) = getimagesize($image['tmp_name']);
                    if ($width > 0 && $height > 0) {
                        // Move the uploaded file to the desired location
                        if (move_uploaded_file($image['tmp_name'], $filePath)) {
                            echo "File is valid and was uploaded successfully.<br>";
                            echo "Image Path: " . htmlspecialchars($filePath) . "<br>";
                        } else {
                            echo "Failed to move the uploaded file.";
                        }
                    } else {
                        echo "Invalid image dimensions.";
                    }
                } else {
                    echo "Invalid file type. Only JPG, PNG, and GIF images are allowed.";
                }
            } else {
                echo "Error with the uploaded file.";
            }
            // Video upload handling
            if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
                $video = $_FILES['video'];
                // Absolute path to the upload directory
                $uploadDir = 'uploads/videos/';
                
                // Ensure the upload directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                
                // Sanitize file name
                $fileName = uniqid() . "_" . basename($video['name']);
                $videoPath = $uploadDir . $fileName;
               

                // Allowed video types
                $allowedTypes = ['video/mp4', 'video/avi', 'video/quicktime', 'video/mov'];

                // Log file type for debugging
                error_log("Uploaded video type: " . $video['type']);

                if (in_array($video['type'], $allowedTypes)) {
                    if ($video['size'] <= 50 * 1024 * 1024) {
                        if (move_uploaded_file($video['tmp_name'], $videoPath)) {
                            echo "Video uploaded successfully.<br>";
                            echo "Video Path: " . htmlspecialchars($videoPath) . "<br>";
                        } else {
                            echo "Failed to move the uploaded video.";
                        }
                    } else {
                        echo "File size exceeds the 50MB limit.";
                    }
                } else {
                    echo "Invalid video type. Only MP4, AVI, and MOV are allowed.";
                }
            } else {
                echo "No video uploaded or an error occurred.";
            }

            $evenmentData = [
                'title' => $title,
                'description' => $description,
                'visual_content' => $filePath,
                'lieu' => $location,
                'owner_id' => $this->id,
                'category_id' => $categorie,
                'video_path' => $videoPath,
                'date' => $date,
                'type' => $type
            ];
          
            $capacityData = [
                'total_tickets' => $total_num,
                'vip_tickets_number' => $vip_num,
                'vip_price' => $vip_price,
                'standard_tickets_number' => $regular_num,
                'standard_price' => $regular_price,
                'gratuit_tickets_number' => $student_num,
                'early_bird_discount' => $discount
            ];
            $tagIds = explode(",", $tags);


            $this->evsdn->createEvent($evenmentData, $capacityData, $tagIds);


            header("Location: /Organiser/dash");

            // echo  '<br>fvef<br>';
            // print_r($_FILES);
            // exit;

            //     echo "Total Tickets: " . htmlspecialchars($filePath) . "<br>";
            //     // echo "Discount: " . htmlspecialchars($videoPath) . "<br>";
            //     echo "VIP Tickets: " . htmlspecialchars($vip_num) . "<br>";
            //     echo "VIP Price: $" . htmlspecialchars($vip_price) . "<br>";
            //     echo "Regular Tickets: " . htmlspecialchars($regular_num) . "<br>";
            //     echo "Regular Price: $" . htmlspecialchars($regular_price) . "<br>";
            //     echo "Student Tickets: " . htmlspecialchars($student_num) . "<br>";
            //     echo "Tags: " . htmlspecialchars($tags) . "<br>";
            //     print_r (explode(",",$tags));

        }
    }















    public function serviceTest()
    {
        // $evenmentData = [
        //     'title' => 'Test',
        //     'description' => 'A conference about the latest trends in technology.',
        //     'visual_content' => 'tech_conference_image.jpg',
        //     'lieu' => 'New York City, USA',
        //     'owner_id' => 2,
        //     'category_id' => 2,
        //     'date' => '2025-05-20',
        //     'type' => 'public'
        // ];

        // $capacityData = [
        //     'total_tickets' => 1000,
        //     'vip_tickets_number' => 100,
        //     'vip_price' => 200.00,
        //     'standard_tickets_number' => 800,
        //     'standard_price' => 50.00,
        //     'gratuit_tickets_number' => 100,
        //     'early_bird_discount' => 10
        // ];
        // $tagIds = [1, 2, 3];

        $this->evsdn->createEvent($evenmentData, $capacityData, $tagIds);
    }
}
