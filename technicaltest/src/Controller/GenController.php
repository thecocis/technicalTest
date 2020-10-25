<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Todo;

class GenController extends AbstractController
{

    public function index(): Response
    {
        $todo_repo = $this->getDoctrine()->getRepository(Todo::class);

        $todos = $todo_repo->findAll();
        $todo = $todo_repo->find(1);

        $data = [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/GenController.php',
        ];

        //die();
        return $this->json($todos);
    }

    //Function that creates a new task
    public function create(Request $request){

        $json = $request->get('json', null);
        $params = json_decode($json);
        $data = [ 
            'status' => 'error',
            'code' => 400,
            'message' => 'Task could not be added',
            'json' => $params,
        ];

        if($json != null){
            $title = (!empty($params->title)) ? $params->title : null;
            $description = (!empty($params->description)) ? $params->description : null;

            if(!empty($title)){

                $todo = new Todo();
                $todo->setTitle($title);
                $todo->setDescription($description);
                $todo->setCreatedAt(new \Datetime ('now'));
                $todo->setStatus(0);

                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();
                $todo_repo = $doctrine->getRepository(Todo::class);

                $em->persist($todo);
                $em->flush();

                $data = [ 
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Task has been successfully added',
                    'json' => $todo,
                ];
            }
        }
        return $this->json($data);
    }

    //Function to done / undone a task
    public function edit($id){
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'Task status has NOT been changed'
        ];

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $task = $doctrine->getRepository(Todo::class)->findOneBy([
            'id' => $id
        ]);

        if ($task != null){       
            
            $status = $task->getStatus();
            if ($status != 0) {
                $status = 0;
            } else {
                $status = 1;
            }
            $task->setStatus($status);
    
            $em->persist($task);
            $em->flush();

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Task status has been changed',
                'task' => $task
            ];
        }
        return $this->json($data);
    }

    //Function to list all tasks
    public function listAll(){
        
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $alldata = $doctrine->getRepository(Todo::class)->findAll();

        return $this->json($alldata);

    }

    //Function to show only finished tasks
    public function listDone(){
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'Not possible to list'
        ];

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();

        $tasks = $doctrine->getRepository(Todo::class)->findBy([
            'status' => '1'
        ]);

        if ($tasks != null){        
            $data = [
                'status' => 'success',
                'code' => 200,
                'list' => $tasks
            ];
        }

        return $this->json($data);
    }

    public function remove($id){
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'Task does not exist'
        ];

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $task = $doctrine->getRepository(Todo::class)->findOneBy([
            'id' => $id
        ]);

        if ($task != null){       
            $em->remove($task);
            $em->flush();

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Task has been removed',
                'task' => $task
            ];
        }
        return $this->json($data);
    }
}
