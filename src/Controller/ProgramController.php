<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;

/**
* @Route("/programs", name="program_")
*/

class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }
    /**
     * Getting a program by id
     *
     * @Route("/{id}", name="show")
     * @return Response
     */
    public function show(int $id):Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program_id' => $id]);


        return $this->render('program/show.html.twig', [
            'program' => $program, 'seasons' => $seasons
        ]);
    }
     /**
     * Getting a season by id
     *
     * @Route("/{programId}/seasons/{seasonId}", name="season_show")
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId ):Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programId]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$seasonId.' found in season\'s table.'
            );
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['id' => $seasonId]);

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season_id' => $seasonId]);

        


        return $this->render('program/season_show.html.twig', [
            'program' => $program, 'season' => $season, 'episodes' => $episodes
        ]);
    }
    
}