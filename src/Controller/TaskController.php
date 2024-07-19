<?php

namespace App\Controller;
use App\DTO\TaskDTO;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'get_task', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TaskController.php',
        ]);
    }

    #[Route('/task', name: 'create_task', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $taskDTO = $serializer->deserialize($data, TaskDTO::class, 'json'); // Assuming TaskDTO is used here

        $task = new Task();
        $task->setUserId($taskDTO->userId);
        $task->setTask($taskDTO->task);
        $task->setDate($taskDTO->date);

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->json($task, JsonResponse::HTTP_CREATED);
    }

    #[Route('/task/{userId}', name: 'get_task', methods: ['GET'])]
    public function getTask(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Task::class)->findOneBy(['userId' => $userId]);

        if (!$task) {
            return $this->json(['message' => 'Task not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($task);
    }
}
