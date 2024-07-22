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
                            new OA\Property(
                                property: 'owner',
                                type: 'object',
                                properties: [
                                new OA\Property(property: 'account_id', type: 'integer', example: 963689),
                                new OA\Property(property: 'reputation', type: 'integer', example: 1614),
                                new OA\Property(property: 'user_id', type: 'integer', example: 987517),
                                new OA\Property(property: 'user_type', type: 'string', example: 'registered'),
                                new OA\Property(property: 'accept_rate', type: 'integer', example: 44),
                                new OA\Property(property: 'profile_image', type: 'string', format: 'uri', example: 'https://i.sstatic.net/CbYax.jpg?s=256'),
                                new OA\Property(property: 'display_name', type: 'string', example: 'anoop'),
                                new OA\Property(property: 'link', type: 'string', format: 'uri', example: 'https://stackoverflow.com/users/987517/anoop')
                            ],
                                example: [
                                    'account_id' => 963689,
                                    'reputation' => 1614,
                                    'user_id' => 987517,
                                    'user_type' => 'registered',
                                    'accept_rate' => 44,
                                    'profile_image' => 'https://i.sstatic.net/CbYax.jpg?s=256',
                                    'display_name' => 'anoop',
                                    'link' => 'https://stackoverflow.com/users/987517/anoop'
                                ]
                            ),
                            new OA\Property(property: 'is_answered', type: 'boolean', example: true),
                            new OA\Property(property: 'view_count', type: 'integer', example: 27500),
                            new OA\Property(property: 'answer_count', type: 'integer', example: 9),
                            new OA\Property(property: 'score', type: 'integer', example: 10),
                            new OA\Property(property: 'last_activity_date', type: 'integer', example: 1721658968),
                            new OA\Property(property: 'creation_date', type: 'integer', example: 1362682120),
                            new OA\Property(property: 'last_edit_date', type: 'integer', example: 1363677405),
                            new OA\Property(property: 'question_id', type: 'integer', example: 15279301),
                            new OA\Property(property: 'content_license', type: 'string', example: 'CC BY-SA 3.0'),
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
