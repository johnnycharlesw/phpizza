<?php
namespace PHPizza\Updates;
use CzProject\GitPhp\GitRepository as GitRepository_;
use CzProject\GitPhp\IRunner;

class GitRepository extends GitRepository_ {
    public function __construct($repository, ?IRunner $runner = NULL)
    {
        return parent::__construct($repository, $runner);
    }

    public function getRemoteExists(string $name): bool {
        $branches = $this->getRemoteBranches();
        $remoteBranchId = $name . "/" . $this->getCurrentBranchName();
        if (array_search($remoteBranchId, $branches)) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchAll(): void{
        $this->fetch();
        $remotes = ["origin", "upstream"];
        foreach ($remotes as $remote) {
            if ($this->getRemoteExists($remote)) {
                $this->fetch($remote);
            }
        }
    }

    public function mergeRemote($name): GitRepository {
        return $this->merge($name . "/" . $this->getCurrentBranchName());
    }

    public function mergeAll(): void{
        $remotes = ["origin", "upstream"];
        foreach ($remotes as $remote) {
            if ($this->getRemoteExists($remote)) {
                $this->mergeRemote($remote);
            }
        }
    }

    public function pullAll(): void{
        $this->fetchAll();
        $this->mergeAll();
    }

    public function getImprecisePHPizzaVersion(): string{
        $lastCommit = $this->getLastCommit();
        $lastCommitTime = $lastCommit->getAuthorDate();
        $version = $lastCommitTime->format("Y.n.j");
        return $version;
    }

}