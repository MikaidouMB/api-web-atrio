<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetJobsBetweenDatesAction
{
    #[Route('/people/jobs-between-dates', name: 'get_jobs_between_dates', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        
        $currentPerson = $request->attributes->get('data');

        $startDate = new \DateTime($request->query->get('start_date'));
        $endDate = new \DateTime($request->query->get('end_date'));


        $jobs = $currentPerson->getJobsBetweenDates($startDate, $endDate);

        $formattedJobs = [];

        foreach ($jobs as $job) {
            $formattedJobs[] = [
                'companyName' => $job->getCompanyName(),
                'position' => $job->getPosition(),
                'startDate' => $job->getStartDate()->format('Y-m-d'),
                'endDate' => $job->getEndDate()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($formattedJobs);
    }
}


