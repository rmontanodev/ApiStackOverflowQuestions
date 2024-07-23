<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\GetQuestionsUseCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StackOverFlowController extends AbstractController
{
    private GetQuestionsUseCase $getQuestionsUseCase;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(GetQuestionsUseCase $getQuestionsUseCase, ValidatorInterface $validator,LoggerInterface $logger)
    {
        $this->getQuestionsUseCase = $getQuestionsUseCase;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    #[OA\Get(
        path: '/api/questions',
        summary: 'Get StackOverflow questions',
        parameters: [
        new OA\Parameter(
            name: 'tagged',
            in: 'query',
            description: 'Tag to filter questions by',
            required: true,
            schema: new OA\Schema(type: 'string', example: 'php')
        ),
        new OA\Parameter(
            name: 'todate',
            in: 'query',
            description: 'End date to filter questions',
            required: false,
            schema: new OA\Schema(type: 'string', format: 'date', example: '2024-01-01')
        ),
        new OA\Parameter(
            name: 'fromdate',
            in: 'query',
            description: 'Start date to filter questions',
            required: false,
            schema: new OA\Schema(type: 'string', format: 'date', example: '2023-01-01')
        )
    ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string'), example: ['php', 'windows', 'imagemagick']),
                            new OA\Property(property: 'link', type: 'string', format: 'uri', example: 'https://stackoverflow.com/questions/15279301/imagemagick-supported-formats-no-value'),
                            new OA\Property(property: 'title', type: 'string', example: 'ImageMagick supported formats no value')
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            )
        ]
    )]
    #[Route('/api/questions', name: 'get_questions', methods: ['GET'])]
    public function getQuestions(Request $request): JsonResponse
    {
        $this->logger->info('Received request for /api/questions', $request->query->all());
        $constraints = new Assert\Collection([
            'tagged' => new Assert\NotBlank(['message' => 'The tagged field is required.']),
            'todate' => new Assert\Optional(new Assert\Date()),
            'fromdate' => new Assert\Optional(new Assert\Date()),
        ]);

        $violations = $this->validator->validate($request->query->all(), $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        try{
            $filters = $request->query->all();
            $questions = $this->getQuestionsUseCase->execute($filters);
            $this->logger->info('Returning questions', ['count' => count($questions)]);
        }catch (\Exception $e){
            $this->logger->error('Error in GetQuestionsUseCase', ['exception' => $e->getMessage()]);
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
        $questionsArray = array_map(fn($question) => [
            'title' => $question->getTitle(),
            'link' => $question->getLink(),
            'tags' => $question->getTags(),
        ], $questions);
        return new JsonResponse($questionsArray, JsonResponse::HTTP_OK);
    }
}
