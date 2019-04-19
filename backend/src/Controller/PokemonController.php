<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PokemonService;
use App\Entity\Pokemon;

/**
 * @Route("/api/pokemon")
 */
class PokemonController
{
    private $normalizer;
    private $entityManager;
    private $serializer;
    private $pokemonService;

    public function __construct(
        NormalizerInterface $normalizer,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PokemonService $pokemonService
    )
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->pokemonService = $pokemonService;
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function findAll(): JsonResponse
    {
        $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);

        $pokemons = $pokemonRepository->findAll();

        $response = $this->normalizer->normalize($pokemons, 'json');

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function findOne(string $id): JsonResponse
    {
        $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);

        $pokemon = $pokemonRepository->findOneById($id);

        $response = $this->normalizer->normalize($pokemon, 'json');

        return new JsonResponse($response);
    }


    /**
     * @Route("", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $pokemon = $this->serializer->deserialize($request->getContent(), Pokemon::class, 'json');

        $createdPokemon = $this->pokemonService->create($pokemon);

        $response = $this->normalizer->normalize($createdPokemon, 'json');

        return new JsonResponse($response);
    }
}
