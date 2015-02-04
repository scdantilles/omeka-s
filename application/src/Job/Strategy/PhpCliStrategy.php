<?php
namespace Omeka\Job\Strategy;

use Omeka\Installation\Task\CheckEnvironmentTask;
use Omeka\Job\Exception;
use Omeka\Model\Entity\Job;

class PhpCliStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function send(Job $job)
    {
        $script = OMEKA_PATH . '/data/scripts/perform-job.php';

        $command = sprintf(
            '%s %s -j %s',
            escapeshellcmd($this->getPhpcliPath()),
            escapeshellarg($script),
            escapeshellarg($job->getId())
        );

        exec(sprintf('%s > /dev/null 2>&1 &', $command));
    }

    /**
     * Get the path to the PHP-CLI binary.
     *
     * @return string
     */
    public function getPhpcliPath()
    {
        $config = $this->getServiceLocator()->get('Config');
        if (isset($config['jobs']['phpcli_path']) && $config['jobs']['phpcli_path']) {
            $phpcliPath = $config['jobs']['phpcli_path'];
        } else {
            $phpcliPath = $this->autodetectPhpcliPath();
        }
        $this->validatePhpcliPath($phpcliPath);
        return $phpcliPath;
    }

    /**
     * Auto-detect the path to the PHP-CLI binary.
     *
     * @return null|string
     */
    public function autodetectPhpcliPath()
    {
        $command = 'which php';
        exec($command, $output, $returnVar);
        return 0 === $returnVar ? $output[0] : null;
    }

    /**
     * Validate the path to the PHP-CLI binary.
     *
     * @throws Exception\RuntimeException
     * @param string $phpcliPath
     */
    public function validatePhpcliPath($phpcliPath)
    {
        $command = sprintf('%s -v', escapeshellcmd($phpcliPath));
        exec($command, $output, $returnVar);

        if (0 !== $returnVar) {
            throw new Exception\RuntimeException(sprintf('The executable path "%s" is invalid.', $phpcliPath));
        }

        preg_match('/^PHP ([^ ]+)/', $output[0], $matches);

        if (!$matches) {
            throw new Exception\RuntimeException(sprintf('The executable path "%s" does not point to a PHP-CLI binary.', $phpcliPath));
        }

        if (version_compare($matches[1], CheckEnvironmentTask::PHP_MINIMUM_VERSION, '<')) {
            throw new Exception\RuntimeException(sprintf('The executable path "%s" points to a PHP-CLI binary with an invalid version.', $phpcliPath));
        }
    }
}
