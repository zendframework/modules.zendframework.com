<?php

namespace Application\Controller;

use Application\Entity;
use Application\Service;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContributorsController extends AbstractActionController
{
    /**
     * @var Service\RepositoryRetriever
     */
    private $repositoryRetriever;

    /**
     * @var Entity\Repository
     */
    private $repository;

    /**
     * @param Service\RepositoryRetriever $repositoryRetriever
     * @param Entity\Repository $repository
     */
    public function __construct(Service\RepositoryRetriever $repositoryRetriever, Entity\Repository $repository)
    {
        $this->repositoryRetriever = $repositoryRetriever;
        $this->repository = $repository;
    }

    public function indexAction()
    {
        $contributors = $this->repositoryRetriever->getContributors(
            $this->repository->owner(),
            $this->repository->name()
        );

        shuffle($contributors);

        $metadata = $this->repositoryRetriever->getUserRepositoryMetadata(
            $this->repository->owner(),
            $this->repository->name()
        );

        return new ViewModel([
            'contributors' => $contributors,
            'metadata' => $metadata,
        ]);
    }
}
